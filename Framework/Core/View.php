<?php

namespace Framework\Core;

class View
{
    /**
     * Renderiza una vista con layout opcional
     */
    public static function render($template, $data = [], $layout = 'layouts/main')
    {
        extract($data);

        // Renderizar la vista principal
        ob_start();
        $templateFile = __DIR__ . '/../../app/Views/' . $template . '.php';

        if (!file_exists($templateFile)) {
            throw new \Exception("Template not found: {$template}");
        }

        require $templateFile;
        $content = ob_get_clean();

        // Si se especificó un layout, verificar si existe
        if ($layout !== null) {
            $layoutFile = __DIR__ . '/../../app/Views/' . $layout . '.php';

            // SI EL LAYOUT EXISTE, usarlo
            if (file_exists($layoutFile)) {
                ob_start();
                require $layoutFile;
                $content = ob_get_clean();
            }
            // SI NO EXISTE, continuar sin layout
        }

        return new Response($content);
    }

    /**
     * Renderiza un componente
     * 
     * @param string $name Nombre del componente
     * @param array $data Datos a pasar al componente
     * @return string HTML renderizado
     */
    public static function component($name, $data = [])
    {
        extract($data);

        $trace = debug_backtrace();
        $callerFile = null;

        foreach ($trace as $step) {
            if (isset($step['file']) && strpos($step['file'], 'app\Views') !== false) {
                $callerFile = $step['file'];
                break;
            }
        }

        if ($callerFile) {
            $viewDir = dirname($callerFile);
            $localPath = $viewDir . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $name . '.php';

            if (file_exists($localPath)) {
                ob_start();
                require $localPath;
                return ob_get_clean();
            }
        }

        $globalPath = __DIR__ . '/../../app/Views/components/' . $name . '.php';
        if (file_exists($globalPath)) {
            ob_start();
            require $globalPath;
            return ob_get_clean();
        }

        return "";
    }
}
