<?php
namespace Framework\Console\Commands;

use Framework\Console\Command;

class ServeCommand extends Command {
    
    public function execute(array $args) {
        $host = $args[0] ?? 'localhost';
        $port = $args[1] ?? '8000';
        
        $this->info("Iniciando servidor en http://{$host}:{$port}");
        $this->info("Presiona Ctrl+C para detener\n");
        
        passthru("php -S {$host}:{$port} -t public");
    }
}