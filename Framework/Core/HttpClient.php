<?php

namespace Framework\Core;

class HttpClient {
    private $baseURL = '';
    private $headers = [];
    private $timeout = 30;
    
    public function __construct($config = []) {
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
    
    // Métodos principales
    public function get($url, $config = []) {
        return $this->request('GET', $url, $config);
    }
    
    public function post($url, $data = [], $config = []) {
        $config['data'] = $data;
        return $this->request('POST', $url, $config);
    }
    
    public function put($url, $data = [], $config = []) {
        $config['data'] = $data;
        return $this->request('PUT', $url, $config);
    }
    
    public function patch($url, $data = [], $config = []) {
        $config['data'] = $data;
        return $this->request('PATCH', $url, $config);
    }
    
    public function delete($url, $config = []) {
        return $this->request('DELETE', $url, $config);
    }
    
    // Método principal de request
    public function request($method, $url, $config = []) {
        // Construir URL completa
        $fullUrl = $this->buildUrl($url, $config['params'] ?? []);
        
        // Preparar headers
        $headers = array_merge($this->headers, $config['headers'] ?? []);
        
        // Inicializar cURL
        $ch = curl_init();
        
        // Configurar opciones básicas
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $config['timeout'] ?? $this->timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        
        // Configurar headers
        if (!empty($headers)) {
            $headerLines = [];
            foreach ($headers as $key => $value) {
                $headerLines[] = "$key: $value";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerLines);
        }
        
        // Configurar datos (POST, PUT, PATCH)
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
        
        // Capturar headers de respuesta
        $responseHeaders = [];
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$responseHeaders) {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) return $len;
            
            $responseHeaders[strtolower(trim($header[0]))] = trim($header[1]);
            return $len;
        });
        
        // Ejecutar request
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        // Manejar errores de cURL
        if ($error) {
            throw new \Exception("HTTP Request Error: $error");
        }
        
        // Parsear respuesta
        return $this->parseResponse($response, $statusCode, $responseHeaders);
    }
    
    // Construir URL con parámetros
    private function buildUrl($url, $params = []) {
        // Si la URL ya es completa, usarla directamente
        if (strpos($url, 'http') === 0) {
            $fullUrl = $url;
        } else {
            $fullUrl = $this->baseURL . '/' . ltrim($url, '/');
        }
        
        // Agregar parámetros query
        if (!empty($params)) {
            $separator = strpos($fullUrl, '?') !== false ? '&' : '?';
            $fullUrl .= $separator . http_build_query($params);
        }
        
        return $fullUrl;
    }
    
    // Parsear respuesta
    private function parseResponse($body, $statusCode, $headers) {
        $data = $body;
        
        // Intentar decodificar JSON
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
    
    // Obtener texto del código de estado
    private function getStatusText($code) {
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
