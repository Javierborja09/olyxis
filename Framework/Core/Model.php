<?php

namespace Framework\Core;

/**
 * Clase Base Model
 * Automatiza las operaciones CRUD detectando dinámicamente la Primary Key.
 */
abstract class Model
{
    protected $db;
    protected $table;
    protected $primaryKey = null;

    public function __construct()
    {
        // Conexión única vía Singleton
        $this->db = Database::getInstance();
        // 2. Detectar la Primary Key automáticamente si no se definió manualmente
        if (empty($this->primaryKey)) {
            $this->primaryKey = $this->fetchPrimaryKey();
        }
    }

    /**
     * Consulta el esquema de la base de datos para encontrar la PK.
     */
    private function fetchPrimaryKey()
    {
        $sql = "SHOW KEYS FROM {$this->table} WHERE Key_name = 'PRIMARY'";
        $result = $this->db->query($sql)->fetch(); 
        
        return $result ? $result['Column_name'] : 'id';
    }

    public function all()
    {
        return $this->db->all($this->table);
    }

    public function find($id)
    {
        // Usamos la PK dinámica en lugar de "id"
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        $result = $this->db->fetchAll($sql, [$id]);
        return $result ? $result[0] : null;
    }

    public function create(array $data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, array $data)
    {
        // Usamos la PK dinámica
        return $this->db->update($this->table, $data, "{$this->primaryKey} = ?", [$id]);
    }

    public function delete($id)
    {
        // Usamos la PK dinámica
        return $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
    }
    
    public function where($condition, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$condition}";
        return $this->db->fetchAll($sql, $params);
    }
}