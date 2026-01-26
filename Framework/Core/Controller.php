<?php
namespace Framework\Core;

/**
 * Clase Base Controller (Actualizada)
 * Proporciona métodos de conveniencia y gestión de middlewares por controlador.
 */
abstract class Controller {

    /** @var array Almacena los middlewares registrados en el constructor */
    protected $middlewares = [];

    /**
     * Registra un middleware para este controlador.
     * * @param string $middlewareClass Nombre de la clase (ej: AuthMiddleware::class)
     * @param array $options Opciones como ['only' => ['save']] o ['except' => ['index']]
     */
    protected function middleware($middlewareClass, $options = []) {
        $this->middlewares[] = [
            'class' => $middlewareClass,
            'only' => $options['only'] ?? null,
            'except' => $options['except'] ?? null
        ];
    }

    /**
     * Filtra y devuelve los middlewares que deben ejecutarse para una acción específica.
     * Este método es consultado por el Router durante el dispatch.
     * * @param string $action Nombre del método del controlador (ej: 'index')
     * @return array Lista de nombres de clases de middleware.
     */
    public function getMiddlewaresForAction($action) {
        $toExecute = [];
        foreach ($this->middlewares as $m) {
            // Si hay restricción 'only' y la acción no está incluida, saltar
            if ($m['only'] && !in_array($action, $m['only'])) {
                continue;
            }
            // Si hay restricción 'except' y la acción está excluida, saltar
            if ($m['except'] && in_array($action, $m['except'])) {
                continue;
            }
            
            $toExecute[] = $m['class'];
        }
        return $toExecute;
    }

    /**
     * Renderiza una vista con layout.
     */
    protected function view($template, $data = [], $layout = 'layouts/main') {
        return View::render($template, $data, $layout);
    }
    
    /**
     * Renderiza una vista sin layout.
     */
    protected function viewWithoutLayout($template, $data = []) {
        return View::render($template, $data, null);
    }
    
    /**
     * Retorna una respuesta JSON.
     */
    protected function json($data, $status = 200) {
        return new Response(json_encode($data), $status, [
            'Content-Type' => 'application/json'
        ]);
    }
    
    /**
     * Redirige a otra URL.
     */
    protected function redirect($url) {
        return new Response('', 302, ['Location' => $url]);
    }
}