<?php

namespace Framework\Core;

/**
 * Clase Component
 * * Gestiona la renderización de fragmentos de vista (UI components) permitiendo
 * el paso de datos aislados y el uso de buffers de salida.
 */
class Component
{
    /** @var array Datos que se pasarán a la vista del componente */
    protected $data = [];

    /** @var string Ruta absoluta al archivo de la vista (.php) */
    protected $viewPath;

    /**
     * Constructor del componente.
     * * @param string $viewPath Ruta al archivo de vista.
     * @param array $data Datos para extraer en la vista.
     */
    public function __construct($viewPath, $data = [])
    {
        $this->viewPath = $viewPath;
        $this->data = $data;
    }

    /**
     * Renderiza el componente y devuelve el HTML como string.
     * * Utiliza extract() para convertir las claves del array en variables 
     * y Output Buffering para capturar el contenido del archivo incluido.
     * * @return string Contenido HTML renderizado.
     */
    public function render()
    {
        // Convierte ['nombre' => 'Juan'] en $nombre = 'Juan'
        extract($this->data);
        
        // Inicia el almacenamiento en memoria (buffer)
        ob_start();
        
        include $this->viewPath;
        
        // Retorna el contenido del buffer y lo limpia
        return ob_get_clean();
    }

    /**
     * Factory method para crear y renderizar un componente global.
     * Busca en la carpeta centralizada de componentes del framework.
     * * @param string $name Nombre del archivo del componente (sin .php).
     * @param array $data Datos dinámicos para el componente.
     * @return string HTML resultante o comentario de error.
     */
    public static function make($name, $data = [])
    {
        // Define la ruta en el directorio global de vistas
        $globalPath = __DIR__ . '/../../Views/components/' . $name . '.php';
        
        if (file_exists($globalPath)) {
            $component = new static($globalPath, $data);
            return $component->render();
        }
        
        return "";
    }

    /**
     * Renderiza un componente local relativo al archivo que lo invoca.
     * Si no encuentra el componente local, intenta buscar uno global.
     * * @param string $name Nombre del componente.
     * @param array $data Datos para la vista.
     * @param bool $context Flag para activar la búsqueda por contexto.
     * @return string HTML renderizado.
     */
    public static function local($name, $data = [], $context = null)
    {
        if ($context) {
            // debug_backtrace permite identificar desde qué archivo se llamó esta función
            $trace = debug_backtrace();
            $callerFile = $trace[0]['file'];
            $viewDir = dirname($callerFile);
            
            // Busca en una subcarpeta /components/ dentro de la carpeta de la vista actual
            $localPath = $viewDir . '/components/' . $name . '.php';
            
            if (file_exists($localPath)) {
                $component = new static($localPath, $data);
                return $component->render();
            }
        }
        
        // Fallback: Si no es local, busca en el repositorio global
        return static::make($name, $data);
    }
}