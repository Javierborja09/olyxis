<?php
namespace Framework\Core;

/**
 * Clase EventDispatcher
 * * Gestiona el ciclo de vida de los eventos del sistema, permitiendo el registro
 * de funciones de retorno (callbacks) y su ejecución asíncrona o secuencial.
 */
class EventDispatcher {
    /** * @var array Almacena los listeners agrupados por nombre de evento.
     * Estructura: ['nombre.evento' => [callable, callable, ...]]
     */
    private $listeners = [];
    
    /**
     * Registra un nuevo listener (escuchador) para un evento específico.
     * * @param string $event El nombre identificador del evento (ej: 'user.created').
     * @param callable $callback Una función anónima, método de clase o nombre de función.
     * @return void
     */
    public function listen($event, callable $callback) {
        // Si el evento no ha sido registrado antes, inicializa un array vacío
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }
        
        // Añade el callback a la lista de ejecución para este evento
        $this->listeners[$event][] = $callback;
    }
    
    /**
     * Dispara todos los listeners registrados para un evento dado.
     * * @param string $event El nombre del evento a ejecutar.
     * @param mixed $data Datos u objetos que se pasarán a cada listener (opcional).
     * @return void
     */
    public function dispatch($event, $data = null) {
        // Si no hay nadie escuchando este evento, terminamos silenciosamente
        if (!isset($this->listeners[$event])) {
            return;
        }
        
        // Ejecuta cada callback registrado pasando la información proporcionada
        foreach ($this->listeners[$event] as $callback) {
            $callback($data);
        }
    }
}