<?php

namespace Framework\Core;

/**
 * Clase HttpClient
 * Proporciona una interfaz fluida para realizar peticiones HTTP (GET, POST, etc.)
 * encapsulando la complejidad de las funciones curl_*.
 */
class HttpClient
{
    /** @var string URL base para las peticiones (ej: https://api.ejemplo.com) */
    private $baseURL = '';

    /** @var array Cabeceras HTTP por defecto para todas las peticiones */
    private $headers = [];

    /** @var int Tiempo máximo de espera en segundos */
    private $timeout = 30;

    /**
     * Constructor del cliente HTTP.
     * @param array $config ['baseURL' => string, 'headers' => array, 'timeout' => int]
     */
    public function __construct($config = [])
    {
        if (isset($config['baseURL'])) {
            $this->baseURL = rtrim($config['baseURL'], '/');
        }
        if (isset($config['headers'])) {
            $this->headers = $config['headers'];
        }
        if (isset($config['timeout'])) {
            $this->timeout = $config['timeout'];
        }
    }

    /**
     * Realiza una petición GET.
     * @param string $url Ruta relativa o absoluta.
     * @param array $config Configuración específica (parámetros query, headers).
     * @return HttpResponse
     */
    public function get($url, $config = [])
    {
        return $this->request('GET', $url, $config);
    }

    /**
     * Realiza una petición POST enviando datos.
     * @param string $url
     * @param array $data Cuerpo del mensaje.
     * @param array $config
     * @return HttpResponse
     */
    public function post($url, $data = [], $config = [])
    {
        $config['data'] = $data;
        return $this->request('POST', $url, $config);
    }

    // Métodos PUT, PATCH y DELETE siguen la misma lógica...

    /**
     * El motor principal que ejecuta cURL.
     * Gestiona la construcción de la URL, headers, serialización de datos y ejecución.
     * * @throws \Exception Si cURL falla (ej: problemas de red).
     */
    public function request($method, $url, $config = [])
    {
        // 1. Preparación de entorno
        $fullUrl = $this->buildUrl($url, $config['params'] ?? []);
        $headers = array_merge($this->headers, $config['headers'] ?? []);

        $ch = curl_init();

        // 2. Configuración de cURL
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Devuelve el resultado como string
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout'] ?? $this->timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        // Bypass de verificación SSL (útil para desarrollo local como XAMPP)
        if (strpos($fullUrl, 'https') === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        // 3. Procesamiento de cabeceras de envío
        if (!empty($headers)) {
            $headerLines = [];
            foreach ($headers as $key => $value) {
                $headerLines[] = "$key: $value";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerLines);
        }

        // 4. Procesamiento del cuerpo (Body) según Content-Type
        if (isset($config['data']) && !empty($config['data'])) {
            $contentType = $headers['Content-Type'] ?? 'application/json';

            if (strpos($contentType, 'application/json') !== false) {
                $body = json_encode($config['data']);
            } elseif (strpos($contentType, 'application/x-www-form-urlencoded') !== false) {
                $body = http_build_query($config['data']);
            } else {
                $body = $config['data'];
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        // 5. Captura de cabeceras de la respuesta mediante un callback
        $responseHeaders = [];
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curl, $header) use (&$responseHeaders) {
            $len = strlen($header);
            $headerParts = explode(':', $header, 2);
            if (count($headerParts) >= 2) {
                $responseHeaders[strtolower(trim($headerParts[0]))] = trim($headerParts[1]);
            }
            return $len;
        });

        // 6. Ejecución y manejo de errores
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new \Exception("HTTP Request Error: $error");
        }

        return $this->parseResponse($response, $statusCode, $responseHeaders);
    }

    /**
     * Construye la URL final concatenando la base y los parámetros GET.
     */
    private function buildUrl($url, $params = [])
    {
        $fullUrl = (strpos($url, 'http') === 0) ? $url : $this->baseURL . '/' . ltrim($url, '/');

        if (!empty($params)) {
            $separator = strpos($fullUrl, '?') !== false ? '&' : '?';
            $fullUrl .= $separator . http_build_query($params);
        }

        return $fullUrl;
    }

    /**
     * Convierte la respuesta bruta en un objeto HttpResponse amigable.
     * Si la respuesta es JSON, la decodifica automáticamente.
     */
    private function parseResponse($body, $statusCode, $headers)
    {
        $data = $body;
        $contentType = $headers['content-type'] ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            $decoded = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data = $decoded;
            }
        }

        return new HttpResponse([
            'data' => $data,
            'status' => $statusCode,
            'statusText' => $this->getStatusText($statusCode),
            'headers' => $headers
        ]);
    }

     private function getStatusText($code)
    {
        $statusTexts = [
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
        ];

        return $statusTexts[$code] ?? 'Unknown';
    }
}

