<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

class MakeControllerCommand extends Command
{
    public function execute(array $args)
    {
        if (empty($args[0])) {
            $this->error("Debes especificar un nombre para el controlador");
            $this->info("Uso: php bin/olyxis make:controller NombreController");
            return;
        }

        $name = $args[0];
        if (!str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }

        $controllerPath = "app/Controllers/{$name}.php";
        if (file_exists($controllerPath)) {
            $this->error("❌ El controlador '{$name}' ya existe.");
            return;
        }

        $viewFolder = strtolower(str_replace('Controller', '', $name));

        $controllerContent = <<<PHP
<?php
namespace App\Controllers;

use Framework\Core\Controller;

class {$name} extends Controller {
    
    public function index(\$request) {
        return \$this->view('{$viewFolder}/index', [
            'title' => '{$name}'
        ], 'layouts/main');
    }
}
PHP;

        $viewContent = <<<HTML
<h1>Vista de {$viewFolder}</h1>
<p>Generada automáticamente por el comando make:controller</p>
HTML;

        $this->createFile($controllerPath, $controllerContent);
        $this->createFile("app/Views/{$viewFolder}/index.php", $viewContent);

        $this->addRouteToConfig($viewFolder, $name);

        $this->success("\n✅ ¡Todo listo!");
        $this->info("- Controlador: {$controllerPath}");
        $this->info("- Vista: app/Views/{$viewFolder}/index.php");
        $this->info("- Ruta: Añadida automáticamente a config/routes.php");
    }

    private function addRouteToConfig($path, $controllerName)
    {
        $routesPath = 'config/routes.php';
        
        if (!file_exists($routesPath)) return;

        $content = file_get_contents($routesPath);

        $newRoute = "        '/{$path}' => '{$controllerName}@index',";

        $pattern = "/'GET' => \[/";
        
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, "'GET' => [\n{$newRoute}", $content);
            file_put_contents($routesPath, $content);
        }
    }
}
