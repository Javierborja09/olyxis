<?php
namespace Framework\Core;

class Router {
    private $routes = [];
    
    public function __construct() {
        $this->loadRoutes();
    }
    
    private function loadRoutes() {
        $routesFile = __DIR__ . '/../../config/routes.php';
        if (file_exists($routesFile)) {
            $this->routes = require $routesFile;
        }
    }
    
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }
    
    private function addRoute($method, $path, $handler) {
        $this->routes[$method][$path] = $handler;
    }
    
    public function dispatch(Request $request) {
        $method = $request->getMethod();
        $path = $request->getPath();
        
        // Buscar coincidencia exacta primero
        if (isset($this->routes[$method][$path])) {
            return $this->executeRoute($this->routes[$method][$path], $request, []);
        }
        
        // Buscar coincidencia con parámetros
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $routePath => $handler) {
                $params = $this->matchRoute($routePath, $path);
                if ($params !== false) {
                    return $this->executeRoute($handler, $request, $params);
                }
            }
        }
        
        return new Response('404 Not Found', 404);
    }
    
    private function matchRoute($routePath, $requestPath) {
        // Convertir ruta con parámetros a regex
        $pattern = preg_replace('/:(\w+)/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $requestPath, $matches)) {
            // Eliminar coincidencias numéricas (indices)
            $params = [];
            foreach ($matches as $key => $value) {
                if (!is_numeric($key)) {
                    $params[$key] = $value;
                }
            }
            return $params;
        }
        
        return false;
    }
    
    private function executeRoute($handler, $request, $params) {
        list($controller, $action) = explode('@', $handler);
        $controllerClass = "App\\Controllers\\{$controller}";
        
        if (!class_exists($controllerClass)) {
            return new Response('Controller not found', 500);
        }
        
        $instance = new $controllerClass();
        
        // Pasar parámetros a la acción
        if (!empty($params)) {
            return $instance->$action($request, ...$params);
        }
        
        return $instance->$action($request);
    }
}
