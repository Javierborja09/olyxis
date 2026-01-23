<?php
namespace Framework\Core;

class EventDispatcher {
    private $listeners = [];
    
    public function listen($event, callable $callback) {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
        $this->listeners[$event][] = $callback;
    }
    
    public function dispatch($event, $data = null) {
        if (!isset($this->listeners[$event])) {
            return;
        }
        
        foreach ($this->listeners[$event] as $callback) {
            $callback($data);
        }
    }
}