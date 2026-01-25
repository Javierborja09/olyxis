<?php

namespace Framework\Core;

class Component
{
    protected $data = [];
    protected $viewPath;

    public function __construct($viewPath, $data = [])
    {
        $this->viewPath = $viewPath;
        $this->data = $data;
    }

    public function render()
    {
        extract($this->data);
        ob_start();
        include $this->viewPath;
        return ob_get_clean();
    }

    public static function make($name, $data = [])
    {
        // Buscar primero en componentes globales
        $globalPath = __DIR__ . '/../../Views/components/' . $name . '.php';
        
        if (file_exists($globalPath)) {
            $component = new static($globalPath, $data);
            return $component->render();
        }
        
        return "<!-- Componente '{$name}' no encontrado -->";
    }

    public static function local($name, $data = [], $context = null)
    {
        // Para componentes locales de una vista específica
        if ($context) {
            $trace = debug_backtrace();
            $callerFile = $trace[0]['file'];
            $viewDir = dirname($callerFile);
            $localPath = $viewDir . '/components/' . $name . '.php';
            
            if (file_exists($localPath)) {
                $component = new static($localPath, $data);
                return $component->render();
            }
        }
        
        // Fallback a componente global
        return static::make($name, $data);
    }
}