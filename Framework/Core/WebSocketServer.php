<?php

namespace Framework\Core;

/**
 * Clase WebSocketServer
 * Gestiona la creación de un servidor WebSocket de bajo nivel.
 */
class WebSocketServer
{
    private $host;
    private $port;
    private $socket;
    private $clients = [];
    private $onMessageCallback;

    public function __construct($host = '0.0.0.0', $port = 8080)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Define la función que se ejecutará al recibir un mensaje.
     */
    public function onMessage(callable $callback)
    {
        $this->onMessageCallback = $callback;
    }

    /**
     * Inicia el bucle de eventos del servidor.
     */
    public function run()
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_bind($this->socket, $this->host, $this->port);
        socket_listen($this->socket);

        $this->clients = [$this->socket];

        echo "Servidor WebSocket iniciado en {$this->host}:{$this->port}\n";

        while (true) {
            $read = $this->clients;
            $write = $except = null;
            
            // Espera cambios en los sockets
            socket_select($read, $write, $except, 0, 10);

            if (in_array($this->socket, $read)) {
                $newSocket = socket_accept($this->socket);
                $this->clients[] = $newSocket;
                
                // Handshake inicial
                $header = socket_read($newSocket, 1024);
                $this->doHandshake($header, $newSocket);
                
                unset($read[array_search($this->socket, $read)]);
            }

            foreach ($read as $clientSocket) {
                $data = @socket_read($clientSocket, 1024);
                
                if ($data === false || strlen($data) === 0) {
                    $this->disconnect($clientSocket);
                    continue;
                }

                $message = $this->unmask($data);
                if ($this->onMessageCallback) {
                    call_user_func($this->onMessageCallback, $this, $clientSocket, $message);
                }
            }
        }
    }

    /**
     * Envía un mensaje a un cliente específico.
     */
    public function send($client, $message)
    {
        $response = $this->mask(json_encode($message));
        @socket_write($client, $response, strlen($response));
    }

    /**
     * Envía un mensaje a todos los clientes conectados excepto al emisor.
     */
    public function broadcast($message, $excludeSocket = null)
    {
        foreach ($this->clients as $client) {
            if ($client !== $this->socket && $client !== $excludeSocket) {
                $this->send($client, $message);
            }
        }
    }

    private function disconnect($socket)
    {
        $index = array_search($socket, $this->clients);
        unset($this->clients[$index]);
        socket_close($socket);
    }

    // --- Métodos de Protocolo WebSocket (Internos) ---

    private function doHandshake($buffer, $socket) {
        if (preg_match("/Sec-WebSocket-Key: (.*)\r\n/", $buffer, $matches)) {
            $key = base64_encode(pack('H*', sha1($matches[1] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
            $upgrade = "HTTP/1.1 101 Switching Protocols\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nSec-WebSocket-Accept: $key\r\n\r\n";
            socket_write($socket, $upgrade, strlen($upgrade));
        }
    }

    private function unmask($text) {
        $length = ord($text[1]) & 127;
        if($length == 126) { $masks = substr($text, 4, 4); $data = substr($text, 8); }
        elseif($length == 127) { $masks = substr($text, 10, 4); $data = substr($text, 14); }
        else { $masks = substr($text, 2, 4); $data = substr($text, 6); }
        $unmasked = "";
        for ($i = 0; $i < strlen($data); ++$i) { $unmasked .= $data[$i] ^ $masks[$i % 4]; }
        return $unmasked;
    }

    private function mask($text) {
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($text);
        if($length <= 125) $header = pack('CC', $b1, $length);
        else $header = pack('CCn', $b1, 126, $length);
        return $header . $text;
    }
}