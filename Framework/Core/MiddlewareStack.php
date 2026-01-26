<?php

namespace Framework\Core;

/**
 * Gestiona la ejecución de múltiples middlewares en cadena.
 * Implementa el patrón "Onion Architecture".
 */
class MiddlewareStack {
    protected $middlewares = [];

    /**
     * Añade un middleware a la lista de ejecución.
     */
    public function add($middleware) {
        $this->middlewares[] = $middleware;
    }

    /**
     * Ejecuta la cadena de middlewares.
     * * @param Request $request
     * @param callable $coreAction La acción final (el controlador).
     * @return Response
     */
    public function handle(Request $request, callable $coreAction) {
        // Reducimos el array de atrás hacia adelante para crear la cebolla
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            function ($next, $middleware) {
                return function ($request) use ($next, $middleware) {
                    // Si el middleware es un string (nombre de clase), lo instanciamos
                    if (is_string($middleware)) {
                        $instance = new $middleware();
                        return $instance->handle($request, $next);
                    }
                    // Si ya es un objeto, lo usamos directamente
                    return $middleware->handle($request, $next);
                };
            },
            $coreAction
        );

        return $pipeline($request);
    }
}