<?php

namespace Framework\Console\Commands;

use Framework\Core\Router;

class RouteListCommand 
{
    public function execute(array $args)
    {
        $router = new Router();
        $methods = $router->getRoutes();

        if (empty($methods)) {
            echo "⚠️  No hay rutas registradas en el sistema.\n";
            return;
        }

        echo "\nListado de Rutas (vía Router::getRoutes)\n";
        echo str_repeat("=", 80) . "\n";
        printf("%-10s | %-35s | %-30s\n", "MÉTODO", "URI", "CONTROLADOR");
        echo str_repeat("-", 80) . "\n";

        foreach ($methods as $method => $routes) {
            foreach ($routes as $path => $data) {
                $handler = is_array($data) ? $data['handler'] : $data;
                printf("%-10s | %-35s | %-30s\n", $method, $path, $handler);
            }
        }
    }
}