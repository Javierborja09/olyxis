<?php

namespace Framework\Core;

/**
 * Clase Router
 * Gestiona el ciclo de vida de la búsqueda y ejecución de rutas,
 * integrando un sistema de Middlewares en cadena (Onion Pattern).
 */
class Router
{
    /** @var array Repositorio de rutas agrupadas por método HTTP */
    private $routes = [];

    public function __construct()
    {
        $this->loadRoutes();
    }

    /**
     * Carga las definiciones de rutas desde el archivo de configuración.
     */
    private function loadRoutes()
    {
        $routesFile = __DIR__ . '/../../config/routes.php';
        if (file_exists($routesFile)) {
            $this->routes = require $routesFile;
        }
    }



    /**
     * Devuelve todas las rutas cargadas en el sistema.
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Procesa la petición y ejecuta la cadena de middlewares y el controlador.
     */
    public function dispatch(Request $request)
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        // 1. Intentar encontrar la ruta
        $routeData = $this->findRoute($method, $path);

        if (!$routeData) {
            return new Response('404 Not Found', 404);
        }

        $handler = $routeData['handler'];
        $params = $routeData['params'];

        // 2. Extraer Controlador y Acción
        list($controllerName, $action) = explode('@', $handler);
        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            return new Response("Controlador {$controllerClass} no encontrado", 500);
        }

        // 3. Instanciar el controlador para leer sus middlewares del __construct
        $controllerInstance = new $controllerClass();

        // 4. Recopilar Middlewares (Ruta + Controlador)
        $routeMiddlewares = $routeData['middleware'] ?? [];
        $controllerMiddlewares = $controllerInstance->getMiddlewaresForAction($action);
        $allMiddlewares = array_merge($routeMiddlewares, $controllerMiddlewares);

        // 5. Definir la acción final (el controlador)
        $coreAction = function ($request) use ($controllerInstance, $action, $params) {
            return $controllerInstance->$action($request, ...array_values($params));
        };

        // 6. Ejecutar a través del MiddlewareStack
        $stack = new MiddlewareStack();
        foreach ($allMiddlewares as $mw) {
            $stack->add($mw);
        }

        return $stack->handle($request, $coreAction);
    }

    /**
     * Busca una coincidencia exacta o dinámica para la URL.
     */
    private function findRoute($method, $path)
    {
        // Coincidencia exacta
        if (isset($this->routes[$method][$path])) {
            $data = $this->routes[$method][$path];
            return $this->normalizeRouteData($data, []);
        }

        // Coincidencia dinámica (:id, :slug, etc)
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $routePath => $data) {
                $params = $this->matchRoute($routePath, $path);
                if ($params !== false) {
                    return $this->normalizeRouteData($data, $params);
                }
            }
        }

        return null;
    }

    /**
     * Normaliza la estructura de la ruta para que siempre sea un array.
     */
    private function normalizeRouteData($data, $params)
    {
        if (is_string($data)) {
            return ['handler' => $data, 'params' => $params, 'middleware' => []];
        }
        $data['params'] = $params;
        return $data;
    }

    /**
     * Compara el patrón de la ruta con la URI usando Regex.
     */
    private function matchRoute($routePath, $requestPath)
    {
        $pattern = preg_replace('/:(\w+)/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestPath, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return false;
    }
}
