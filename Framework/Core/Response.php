<?php
namespace Framework\Core;

/**
 * Clase Response
 * Gestiona la salida final del servidor hacia el cliente, incluyendo
 * el cuerpo de la respuesta, el código de estado HTTP y las cabeceras.
 */
class Response {
    /** @var mixed El contenido que se enviará al navegador (HTML, JSON, texto) */
    private $content;
    
    /** @var int Código de estado HTTP (por defecto 200 OK) */
    private $status;
    
    /** @var array Lista de cabeceras HTTP adicionales */
    private $headers;
    
    /**
     * Constructor de la respuesta.
     * @param mixed $content Contenido de la respuesta.
     * @param int $status Código HTTP.
     * @param array $headers Diccionario de cabeceras ['Header-Name' => 'Value'].
     */
    public function __construct($content = '', $status = 200, $headers = []) {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }
    
    /**
     * Envía la respuesta al cliente.
     * Realiza tres pasos críticos:
     * 1. Establece el código de estado HTTP.
     * 2. Envía todas las cabeceras configuradas.
     * 3. Imprime el contenido final.
     * @return void
     */
    public function send() {
        // Establece el código de estado (200, 404, etc.)
        http_response_code($this->status);
        
        // Itera y envía cada cabecera personalizada
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        
        // Imprime el cuerpo de la respuesta
        echo $this->content;
    }
}