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

        $content = <<<PHP
<?php
namespace App\Controllers;

use Framework\Core\Controller;

class {$name} extends Controller {
    
    public function index(\$request) {
        return \$this->view('home/index', [
            'title' => '{$name}'
        ], 'layouts/main');
    }
}
PHP;

        $this->createFile("app/Controllers/{$name}.php", $content);
        $this->success("\nControlador creado correctamente!");
        $this->info("No olvides agregarlo a config/routes.php\n");
    }
}
