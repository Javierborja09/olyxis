<?php

namespace Framework\Core;

class View
{
    public static function render($template, $data = [], $layout = 'layouts/main')
    {
        // 1. Inyectamos AUTOMÁTICAMENTE el objeto Request si no viene en los datos
        if (!isset($data['request'])) {
            $data['request'] = new Request();
        }

        extract($data);

        ob_start();
        $templateFile = __DIR__ . '/../../app/Views/' . $template . '.php';

        if (!file_exists($templateFile)) {
            throw new \Exception("Template not found: {$template}");
        }

        require $templateFile;
        $content = ob_get_clean();

        if ($layout !== null) {
            $layoutFile = __DIR__ . '/../../app/Views/' . $layout . '.php';

            if (file_exists($layoutFile)) {
                ob_start();
                // El layout ahora también tendrá acceso a $request y $content
                require $layoutFile;
                $content = ob_get_clean();
            }
        }

        return new Response($content);
    }

    public static function component($name, $data = [])
    {
        // 2. También inyectamos el Request en los componentes
        if (!isset($data['request'])) {
            $data['request'] = new Request();
        }

        extract($data);

        $trace = debug_backtrace();
        $callerFile = null;

        foreach ($trace as $step) {
            if (isset($step['file']) && strpos($step['file'], 'app' . DIRECTORY_SEPARATOR . 'Views') !== false) {
                $callerFile = $step['file'];
                break;
            }
        }

        // Búsqueda Local
        if ($callerFile) {
            $viewDir = dirname($callerFile);
            $localPath = $viewDir . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $name . '.php';

            if (file_exists($localPath)) {
                ob_start();
                require $localPath;
                return ob_get_clean();
            }
        }

        // Búsqueda Global
        $globalPath = __DIR__ . '/../../app/Views/components/' . $name . '.php';
        if (file_exists($globalPath)) {
            ob_start();
            require $globalPath;
            return ob_get_clean();
        }

        return ""; 
    }

    
}