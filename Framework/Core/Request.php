<?php

namespace Framework\Core;

class Request {
    private $method;
    private $path;
    private $query;
    private $post;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->query = $_GET;
        $this->post = $_POST;
    }

    // --- MÉTODOS DE RUTA Y MÉTODO ---

    public function getMethod() {
        return $this->method;
    }

    public function getPath() {
        return $this->path;
    }

    public function isMethod($method) {
        return strtoupper($this->method) === strtoupper($method);
    }

    // --- MÉTODOS DE DATOS (GET/POST) ---

    public function get($key = null, $default = null) {
        if ($key === null) return $this->query;
        return $this->query[$key] ?? $default;
    }

    public function post($key = null, $default = null) {
        if ($key === null) return $this->post;
        return $this->post[$key] ?? $default;
    }

    public function all() {
        return array_merge($this->query, $this->post);
    }

    // --- MÉTODOS DE SESIÓN ---

    /**
     * Obtiene o establece un valor en la sesión.
     */
    public function session($key, $value = null) {
        if ($value !== null) {
            $_SESSION[$key] = $value;
            return;
        }
        return $_SESSION[$key] ?? null;
    }

    // --- MÉTODOS FLASH (Mensajes temporales) ---

    /**
     * Guarda un mensaje que solo durará hasta la siguiente carga de página.
     */
    public function setFlash($key, $message) {
        $_SESSION['_flash'][$key] = $message;
    }

    /**
     * Recupera un mensaje flash y lo elimina de la sesión.
     */
    public function getFlash($key) {
        $message = $_SESSION['_flash'][$key] ?? null;
        if ($message) {
            unset($_SESSION['_flash'][$key]);
        }
        return $message;
    }

    // --- UTILIDADES ---

    public function redirect($url) {
        header("Location: {$url}");
        exit;
    }
}