<?php

namespace Framework\Core;

class HttpResponse {
    public $data;
    public $status;
    public $statusText;
    public $headers;
    
    public function __construct($response) {
        $this->data = $response['data'];
        $this->status = $response['status'];
        $this->statusText = $response['statusText'];
        $this->headers = $response['headers'];
    }
    
    public function isSuccess() {
        return $this->status >= 200 && $this->status < 300;
    }
    
    public function json() {
        return $this->data;
    }
}