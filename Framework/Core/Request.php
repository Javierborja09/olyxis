<?php

namespace Framework\Core;

/**
 * Clase Request
 * Gestiona la entrada de datos, la sesión y el estado de la petición HTTP.
 */
class Request {
    private $method;
    private $path;
    private $query;
    private $post;
    private $session;

    public function __construct() {
        // Inicializamos la sesión para que $request->session() funcione
        $this->session = new Session();

        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $this->query = $_GET;
        $this->post = $_POST;

        // Soporte para peticiones JSON (importante para APIs o Fetch)
        $input = json_decode(file_get_contents('php://input'), true);
        if (is_array($input)) {
            $this->post = array_merge($this->post, $input);
        }
    }

    // --- ACCESO A SESIÓN ---

    /**
     * Retorna la instancia de Session. 
     * Esto soluciona el error en tu LoginController.
     */
    public function session(): Session {
        return $this->session;
    }

    // --- MÉTODOS DE RUTA Y MÉTODO ---

    public function getMethod() {
        return $this->method;
    }

    public function getPath() {
        return $this->path;
    }

    public function isPost(): bool {
        return $this->method === 'POST';
    }

    public function isGet(): bool {
        return $this->method === 'GET';
    }

    public function isAjax(): bool {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    // --- MÉTODOS DE DATOS (GET/POST/INPUT) ---

    /**
     * Retorna todos los datos recibidos (POST + GET)
     */
    public function all(): array {
        return array_merge($this->query, $this->post);
    }

    /**
     * Obtiene datos de POST con limpieza.
     */
    public function post($key = null, $default = null) {
        if ($key === null) return $this->post;
        $value = $this->post[$key] ?? $default;
        return is_string($value) ? htmlspecialchars(trim($value)) : $value;
    }

    /**
     * Obtiene datos de GET con limpieza.
     */
    public function get($key = null, $default = null) {
        if ($key === null) return $this->query;
        $value = $this->query[$key] ?? $default;
        return is_string($value) ? htmlspecialchars(trim($value)) : $value;
    }

    /**
     * Helper para obtener cualquier dato sin importar si es GET o POST
     */
    public function input($key, $default = null) {
        return $this->post($key) ?? ($this->get($key) ?? $default);
    }

    public function has($key): bool {
        return isset($this->query[$key]) || isset($this->post[$key]);
    }

    // --- MÉTODOS DE FLASH (Proxies a Session) ---

    public function setFlash($key, $message) {
        $this->session->setFlash($key, $message);
    }

    public function getFlash($key) {
        return $this->session->getFlash($key);
    }

    public function hasFlash($key): bool {
        return $this->session->get('flash_' . $key) !== null;
    }

    // --- UTILIDADES ---

    /**
     * NOTA: He mantenido tu lógica de redirect pero adaptada a tu framework.
     * Es mejor que el Controlador retorne un objeto Response, pero este 
     * método ayuda si necesitas salir rápido.
     */
    public function redirect($url) {
        header("Location: {$url}");
        return $this;
    }

    public function with($key, $message) {
        $this->setFlash($key, $message);
        return $this;
    }

    public function exit() {
        exit;
    }
}