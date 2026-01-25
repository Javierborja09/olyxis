<?php

use Framework\Core\View;

if (!function_exists('component')) {
    /**
     * Helper para renderizar componentes fácilmente
     * 
     * @param string $name Nombre del componente
     * @param array $data Datos a pasar al componente
     * @return void
     */
    function component($name, $data = []) {
        echo View::component($name, $data);
    }
}

if (!function_exists('view')) {
    /**
     * Helper para renderizar vistas
     */
    function view($template, $data = [], $layout = 'layouts/main') {
        return View::render($template, $data, $layout);
    }
}