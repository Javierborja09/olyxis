<?php
namespace Framework\Core;

class View {
    /**
     * Renderiza una vista con layout opcional
     */
    public static function render($template, $data = [], $layout = 'layouts/main') {
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
            // SI NO EXISTE, continuar sin layout (no lanza error)
        }
        
        return new Response($content);
    }
}