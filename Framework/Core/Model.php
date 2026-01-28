<?php

namespace Framework\Core;

/**
 * Clase Base Model
 * Automatiza las operaciones CRUD usando la clase Database.
 */
abstract class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        // Conexión única vía Singleton
        $this->db = Database::getInstance();
    }

    public function all()
    {
        return $this->db->all($this->table);
    }

    public function find($id)
    {
        return $this->db->find($this->table, $id);
    }

    public function create(array $data)
    {
        // Mapea 'create' hacia el método 'insert' de la base de datos
        return $this->db->insert($this->table, $data);
    }

    public function update($id, array $data)
    {
        return $this->db->update($this->table, $data, "id = ?", [$id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, "id = ?", [$id]);
    }
    
    public function where($condition, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$condition}";
        return $this->db->fetchAll($sql, $params);
    }
}