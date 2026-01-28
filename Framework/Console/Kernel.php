<?php

namespace Framework\Console;

class Kernel {
    protected array $commands = [
        'init'            => \Framework\Console\Commands\InitCommand::class,
        'serve'           => \Framework\Console\Commands\ServeCommand::class,
        'make:controller' => \Framework\Console\Commands\MakeControllerCommand::class,
        'make:crud'       => \Framework\Console\Commands\MakeCrudCommand::class,
        'make:model'       => \Framework\Console\Commands\MakeModelCommand::class,
        'route:list'      => \Framework\Console\Commands\RouteListCommand::class,
        'make:middleware' => \Framework\Console\Commands\MakeMiddlewareCommand::class,
    ];

    public function handle(array $argv) {
        $commandName = $argv[1] ?? 'help';
        $args = array_slice($argv, 2);

        if ($commandName === 'help' || !isset($this->commands[$commandName])) {
            $this->displayHelp();
            return;
        }
        $commandClass = $this->commands[$commandName];
        (new $commandClass())->execute($args);
    }

    protected function displayHelp() {
        echo "\n🚀 Olyxis CLI v2.0 Stable\n\n";
        echo "Comandos disponibles:\n";
        foreach (array_keys($this->commands) as $name) {
            echo "  ├─ $name\n";
        }
        echo "\nUso: php olyxis [comando] [argumentos]\n";
    }
}