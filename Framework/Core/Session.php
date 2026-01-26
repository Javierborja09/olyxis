<?php

namespace Framework\Core;

class Session {
    /**
     * Inicia la sesión si no ha sido iniciada
     */
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Verifica si una clave existe en la sesión
     * @param string $key
     * @return bool
     */
    public function has($key): bool {
        return isset($_SESSION[$key]);
    }

    /**
     * Guarda un valor en la sesión
     */
    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Obtiene un valor de la sesión
     */
    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Elimina una clave de la sesión
     */
    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Guarda un mensaje flash (solo dura una petición)
     */
    public function setFlash($key, $message) {
        $_SESSION['_flash'][$key] = $message;
    }

    /**
     * Obtiene y elimina un mensaje flash
     */
    public function getFlash($key) {
        $message = $_SESSION['_flash'][$key] ?? null;
        if ($message) {
            unset($_SESSION['_flash'][$key]);
        }
        return $message;
    }

    /**
     * Verifica si existe un mensaje flash sin eliminarlo
     */
    public function hasFlash($key): bool {
        return isset($_SESSION['_flash'][$key]);
    }

    /**
     * Destruye la sesión (Logout) de forma segura
     */
    public function destroy() {
        // Borrar variables de sesión
        $_SESSION = [];

        // Borrar la cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000,
                $params["path"], 
                $params["domain"],
                $params["secure"], 
                $params["httponly"]
            );
        }

        // Destruir la sesión en el servidor
        session_destroy();
    }
}