<?php
namespace Framework\Console;

abstract class Command {
    abstract public function execute(array $args);
    
    protected function success($message) {
        echo "\033[32m✓\033[0m {$message}\n";
    }
    
    protected function error($message) {
        echo "\033[31m✗\033[0m {$message}\n";
    }
    
    protected function info($message) {
        echo "\033[36mℹ\033[0m {$message}\n";
    }
    
    protected function createDirectory($path) {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
            $this->success("Directorio creado: {$path}");
        }
    }
    
    protected function createFile($path, $content) {
        $directory = dirname($path);
        $this->createDirectory($directory);
        file_put_contents($path, $content);
        $this->success("Archivo creado: {$path}");
    }
}