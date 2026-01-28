<?php

namespace Framework\Core;

use PDO;
use PDOException;

/**
 * Clase Database (Patrón Singleton)
 * Gestiona la conexión PDO y proporciona métodos simplificados para 
 * interactuar con la base de datos mediante Prepared Statements.
 */
class Database
{
    /** @var Database|null Instancia única de la clase */
    private static $instance = null;
    
    /** @var PDO Objeto de conexión nativo de PHP */
    private $connection;

    /**
     * Constructor privado para evitar instanciación externa.
     */
    private function __construct()
    {
        $this->connect();
    }

    /**
     * Método estático para obtener la instancia única de la conexión.
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Establece la conexión con el servidor de base de datos.
     * Configura el manejo de errores por excepciones y el modo de obtención asociativo.
     */
    private function connect()
    {
        try {
            static $env = null;
            if ($env === null) {
                $env = $this->loadEnv();
            }

            // Configuración con valores por defecto (fallback)
            $host = $env['DB_HOST'] ?? 'localhost';
            $port = $env['DB_PORT'] ?? 3306;
            $dbname = $env['DB_NAME'] ?? 'mi_framework_db';
            $user = $env['DB_USER'] ?? 'root';
            $password = $env['DB_PASSWORD'] ?? '';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

            $this->connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,           // Lanza excepciones en errores SQL
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Retorna arrays asociativos
                PDO::ATTR_EMULATE_PREPARES => false,                  // Usa consultas preparadas reales
                PDO::ATTR_PERSISTENT => true,                         // Conexión persistente para mayor rendimiento
            ]);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    /**
     * Ejecuta un Procedimiento Almacenado.
     * @param string $procedure Nombre del procedimiento.
     * @param array $params Parámetros de entrada.
     * @return \PDOStatement
     */
    public function call($procedure, $params = [])
    {
        $placeholders = !empty($params)
            ? implode(',', array_fill(0, count($params), '?'))
            : '';

        $sql = "CALL {$procedure}({$placeholders})";
        return $this->query($sql, $params);
    }

    /**
     * Busca y parsea el archivo .env para cargar variables de entorno.
     * Soporta comentarios con '#' y valores con comillas.
     * @return array
     */
    private function loadEnv()
    {
        $possiblePaths = [
            getcwd() . '/.env',
            __DIR__ . '/../../.env',
            __DIR__ . '/../../../.env',
            __DIR__ . '/../../../../.env',
        ];

        $env = [];
        $envFile = null;

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $envFile = $path;
                break;
            }
        }

        if (!$envFile) {
            error_log("Advertencia: No se encontró archivo .env");
            return $env;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || $line[0] === '#') continue;
            if (strpos($line, '=') === false) continue;

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Limpieza de comillas en los valores
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")
            ) {
                $value = substr($value, 1, -1);
            }

            $env[$key] = $value;
        }

        return $env;
    }

    /**
     * Ejecuta una consulta SQL genérica con parámetros seguros.
     * @param string $sql
     * @param array $params
     * @return \PDOStatement
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Error en la consulta: " . $e->getMessage());
        }
    }


    /**
     * Obtiene todos los registros de una tabla.
     * @param string $table
     * @return array
     */
    public function all($table)
    {
        return $this->fetchAll("SELECT * FROM {$table}");
    }

    /**
     * Busca un registro por su ID primario.
     * @param string $table
     * @param mixed $id
     * @return array|false
     */
    public function find($table, $id)
    {
        return $this->fetch("SELECT * FROM {$table} WHERE id = ?", [$id]);
    }

    /**
     * Obtiene una única fila de un resultado.
     */
    public function fetch($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Obtiene todas las filas de un resultado.
     */
    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Inserta un registro de forma sencilla pasando un array asociativo.
     * @param string $table Nombre de la tabla.
     * @param array $data ['columna' => 'valor']
     */
    public function insert($table, $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        return $this->query($sql, array_values($data));
    }

    /**
     * Actualiza registros basados en una condición WHERE.
     */
    public function update($table, $data, $where, $whereParams = [])
    {
        $set = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";

        return $this->query($sql, array_merge(array_values($data), $whereParams));
    }

    /**
     * Elimina registros de una tabla.
     */
    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params);
    }

    /**
     * Retorna el objeto PDO original para operaciones complejas.
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Cierra la conexión.
     */
    public function close()
    {
        $this->connection = null;
    }
}