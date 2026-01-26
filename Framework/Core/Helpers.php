<?php

use Framework\Core\View;

/**
 * ARCHIVO DE HELPERS GLOBALES
 * Este archivo suele cargarse mediante Composer (autoload: files) o 
 * manualmente en el arranque de la aplicación (bootstrap).
 */

if (!function_exists('component')) {
    /**
     * Helper para renderizar e imprimir componentes de UI.
     * Facilita la inserción de fragmentos HTML reutilizables directamente en las vistas.
     * * @param string $name Nombre del archivo del componente.
     * @param array $data Datos dinámicos (variables) para el componente.
     * @return void
     */
    function component($name, $data = []) {
        // Imprime directamente el resultado del método estático de la clase View
        echo View::component($name, $data);
    }
}

if (!function_exists('view')) {
    /**
     * Helper global para la renderización de vistas completas.
     * Es el método estándar que los controladores devuelven al Router.
     * * @param string $template Nombre de la plantilla.
     * @param array $data Variables para la vista.
     * @param string $layout Nombre del layout maestro (por defecto 'layouts/main').
     * @return \Framework\Core\Response
     */
    function view($template, $data = [], $layout = 'layouts/main') {
        // Retorna un objeto Response listo para ser enviado al cliente
        return View::render($template, $data, $layout);
    }
}