<?php
namespace Framework\Core;

/**
 * Clase Kernel de la Aplicación.
 * * Actúa como el orquestador central (Service Container) que gestiona el ciclo de vida
 * de la solicitud, la configuración y los componentes principales.
 */
class Application {
    /** @var Application Instancia única de la clase (Patrón Singleton) */
    private static $instance;

    /** @var Router Componente encargado de gestionar las rutas y controladores */
    private $router;

    /** @var EventDispatcher Sistema de ganchos (hooks) y eventos */
    private $eventDispatcher;

    /** @var array Repositorio central de configuraciones del sistema */
    private $config = [];
    
    /**
     * Constructor de la aplicación.
     * Inicializa los componentes básicos y registra la instancia global.
     */
    public function __construct() {
        self::$instance = $this;
        $this->loadConfig();
        $this->router = new Router();
        $this->eventDispatcher = new EventDispatcher();
    }
    
    /**
     * Obtiene la instancia activa de la aplicación.
     * @return Application
     */
    public static function getInstance() {
        return self::$instance;
    }
    
    /**
     * Carga automáticamente todos los archivos PHP de la carpeta /config.
     * El nombre del archivo (sin extensión) se convierte en la clave del array.
     * @return void
     */
    private function loadConfig() {
        // Busca archivos .php en el directorio de configuración relativo a este archivo
        $configFiles = glob(__DIR__ . '/../../config/*.php');
        foreach ($configFiles as $file) {
            $name = basename($file, '.php');
            // Almacena el array devuelto por el archivo de configuración
            $this->config[$name] = require $file;
        }
    }
    
    /**
     * Ejecuta el ciclo de vida de la petición HTTP.
     * 1. Captura la solicitud.
     * 2. Notifica a los observadores.
     * 3. Resuelve la ruta y genera una respuesta.
     * 4. Envía la respuesta al cliente.
     * @return void
     */
    public function run() {
        // Inicializa el objeto Request con los datos del entorno (GET, POST, etc.)
        $request = new Request();

        // Dispara evento antes de procesar la ruta (útil para Middlewares o Auth)
        $this->eventDispatcher->dispatch('request.received', $request);
        
        // El router busca el controlador correspondiente y devuelve un objeto Response
        $response = $this->router->dispatch($request);
        
        // Dispara evento antes de enviar datos al navegador (útil para comprimir salida)
        $this->eventDispatcher->dispatch('response.ready', $response);
        
        // Envía headers y cuerpo de la respuesta al cliente
        $response->send();
    }
    
    /**
     * Recupera un valor de configuración mediante su clave.
     * @param string $key Nombre del archivo de configuración.
     * @return mixed|null
     */
    public function getConfig($key) {
        return $this->config[$key] ?? null;
    }
    
    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher() {
        return $this->eventDispatcher;
    }
    
    /**
     * @return Router
     */
    public function getRouter() {
        return $this->router;
    }
}