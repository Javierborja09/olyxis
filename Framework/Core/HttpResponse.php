<?php

namespace Framework\Core;

/**
 * Clase HttpResponse
 * Representa de forma estructurada la respuesta recibida tras una petición HTTP.
 * Facilita la validación del estado de la respuesta y el acceso a sus datos.
 */
class HttpResponse {
    /** @var mixed El cuerpo de la respuesta (puede ser un array decodificado o un string) */
    public $data;
    
    /** @var int El código de estado HTTP (ej: 200, 404, 500) */
    public $status;
    
    /** @var string Representación textual del estado (ej: "OK", "Not Found") */
    public $statusText;
    
    /** @var array Cabeceras recibidas del servidor remoto en minúsculas */
    public $headers;
    
    /**
     * Constructor de la respuesta.
     * @param array $response Array asociativo generado por el HttpClient.
     */
    public function __construct($response) {
        $this->data = $response['data'];
        $this->status = $response['status'];
        $this->statusText = $response['statusText'];
        $this->headers = $response['headers'];
    }
    
    /**
     * Determina si la petición fue exitosa (Códigos 2xx).
     * @return bool
     */
    public function isSuccess() {
        return $this->status >= 200 && $this->status < 300;
    }
    
    /**
     * Retorna los datos de la respuesta. 
     * Si el HttpClient detectó JSON, este método devuelve el array ya procesado.
     * @return mixed
     */
    public function json() {
        return $this->data;
    }
}