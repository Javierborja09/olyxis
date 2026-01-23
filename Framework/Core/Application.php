<?php
namespace Framework\Core;

class Application {
    private static $instance;
    private $router;
    private $eventDispatcher;
    private $config = [];
    
    public function __construct() {
        self::$instance = $this;
        $this->loadConfig();
        $this->router = new Router();
        $this->eventDispatcher = new EventDispatcher();
    }
    
    public static function getInstance() {
        return self::$instance;
    }
    
    private function loadConfig() {
        $configFiles = glob(__DIR__ . '/../../config/*.php');
        foreach ($configFiles as $file) {
            $name = basename($file, '.php');
            $this->config[$name] = require $file;
        }
    }
    
    public function run() {
        $request = new Request();

        $this->eventDispatcher->dispatch('request.received', $request);
        
        $response = $this->router->dispatch($request);
        
        $this->eventDispatcher->dispatch('response.ready', $response);
        
        $response->send();
    }
    
    public function getConfig($key) {
        return $this->config[$key] ?? null;
    }
    
    public function getEventDispatcher() {
        return $this->eventDispatcher;
    }
    
    public function getRouter() {
        return $this->router;
    }
}