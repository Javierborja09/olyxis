<?php
namespace Framework\Core;

abstract class Controller {
    /**
     * Renderiza una vista con layout
     * 
     * @param string $template Ruta de la vista
     * @param array $data Datos para la vista
     * @param string|null $layout Layout a usar (default: 'layouts/main', null: sin layout)
     * @return Response
     */
    protected function view($template, $data = [], $layout = 'layouts/main') {
        return View::render($template, $data, $layout);
    }
    
    /**
     * Renderiza una vista SIN layout
     * 
     * @param string $template Ruta de la vista
     * @param array $data Datos para la vista
     * @return Response
     */
    protected function viewWithoutLayout($template, $data = []) {
        return View::render($template, $data, null);
    }
    
    /**
     * Retorna una respuesta JSON
     */
    protected function json($data, $status = 200) {
        return new Response(json_encode($data), $status, [
            'Content-Type' => 'application/json'
        ]);
    }
    
    /**
     * Redirige a otra URL
     */
    protected function redirect($url) {
        return new Response('', 302, ['Location' => $url]);
    }
}