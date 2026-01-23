<?php
namespace Framework\Core;

class Response {
    private $content;
    private $status;
    private $headers;
    
    public function __construct($content = '', $status = 200, $headers = []) {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }
    
    public function send() {
        http_response_code($this->status);
        
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        
        echo $this->content;
    }
}