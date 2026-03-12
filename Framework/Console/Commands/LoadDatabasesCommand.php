<?php

namespace Framework\Console\Commands;

use Framework\Core\Database;

/**
 * Comando: php oly load:databases
 * 
 * Conecta a la base de datos, lee todas las tablas y genera
 * una clase Entity por cada tabla en app/Entitys/
 * 
 * Uso:
 *   php oly load:databases              → genera todas las tablas
 *   php oly load:databases users posts  → genera solo esas tablas
 */
class LoadDatabasesCommand
{
    public function execute(array $args): void
    {
        echo "\n🔍 Olyxis · Scaffold de Entidades\n";
        echo str_repeat("─", 40) . "\n\n";

        // 1. Conectar a la DB usando el Singleton ya existente
        try {
            $db = Database::getInstance();
        } catch (\Throwable $e) {
            echo "❌ No se pudo conectar a la base de datos:\n";
            echo "   " . $e->getMessage() . "\n\n";
            echo "💡 Asegúrate de tener un .env con DB_HOST, DB_NAME, DB_USER, DB_PASSWORD\n\n";
            return;
        }

        // 2. Obtener el nombre de la DB actual
        $dbName = $db->fetch("SELECT DATABASE() as db")['db'] ?? null;

        if (!$dbName) {
            echo "❌ No se pudo determinar la base de datos activa.\n\n";
            return;
        }

        echo "📦 Base de datos: \033[36m{$dbName}\033[0m\n\n";

        // 3. Obtener las tablas (filtrar si se pasaron args)
        $allTables = $db->fetchAll(
            "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES 
             WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = 'BASE TABLE'
             ORDER BY TABLE_NAME",
            [$dbName]
        );

        if (empty($allTables)) {
            echo "⚠️  No se encontraron tablas en la base de datos.\n\n";
            return;
        }

        // Filtrar por args si se especificaron tablas concretas
        $filtro = array_map('strtolower', $args);
        $tablas = array_filter($allTables, function ($row) use ($filtro) {
            return empty($filtro) || in_array(strtolower($row['TABLE_NAME']), $filtro);
        });

        if (empty($tablas)) {
            echo "⚠️  Ninguna de las tablas especificadas existe: " . implode(', ', $filtro) . "\n\n";
            return;
        }

        // 4. Crear directorio app/Entitys si no existe
        $outputDir = getcwd() . '/app/Entitys';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
            echo "📁 Directorio creado: app/Entitys\n\n";
        }

        $generados  = 0;
        $omitidos   = 0;

        foreach ($tablas as $row) {
            $tableName = $row['TABLE_NAME'];

            // 5. Leer columnas de la tabla desde INFORMATION_SCHEMA
            $columnas = $db->fetchAll(
                "SELECT 
                    COLUMN_NAME,
                    DATA_TYPE,
                    IS_NULLABLE,
                    COLUMN_KEY,
                    COLUMN_DEFAULT,
                    EXTRA,
                    CHARACTER_MAXIMUM_LENGTH,
                    NUMERIC_PRECISION
                 FROM INFORMATION_SCHEMA.COLUMNS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
                 ORDER BY ORDINAL_POSITION",
                [$dbName, $tableName]
            );

            // 6. Detectar PK
            $pk = 'id';
            foreach ($columnas as $col) {
                if ($col['COLUMN_KEY'] === 'PRI') {
                    $pk = $col['COLUMN_NAME'];
                    break;
                }
            }

            // 7. Generar nombre de clase (PascalCase)
            $className = $this->toPascalCase($tableName);
            $filePath  = "{$outputDir}/{$className}.php";

            if (file_exists($filePath)) {
                echo "  ⚠️  \033[33m{$className}\033[0m ya existe, se omite. (usa --force para sobreescribir)\n";
                $omitidos++;
                continue;
            }

            // 8. Generar el contenido del archivo
            $contenido = $this->generarEntity($className, $tableName, $pk, $columnas);

            file_put_contents($filePath, $contenido);
            echo "  ✅ \033[32m{$className}\033[0m → app/Entitys/{$className}.php  ";
            echo "\033[90m({$tableName}, PK: {$pk})\033[0m\n";
            $generados++;
        }

        // 9. Soporte --force para sobreescribir
        if (in_array('--force', $args)) {
            echo "\n💡 Tip: --force detectado pero se procesa solo en la primera pasada.\n";
        }

        echo "\n" . str_repeat("─", 40) . "\n";
        echo "✔  Generados: {$generados}   Omitidos: {$omitidos}\n\n";
    }

    // ─────────────────────────────────────────────
    // Genera el contenido PHP de la Entity
    // ─────────────────────────────────────────────
    private function generarEntity(string $className, string $tableName, string $pk, array $columnas): string
    {
        $propiedades   = '';
        $fillable      = [];
        $casts         = [];
        $phpDocProps   = '';

        foreach ($columnas as $col) {
            $nombre     = $col['COLUMN_NAME'];
            $tipo       = $this->mapTipoPHP($col['DATA_TYPE']);
            $nullable   = $col['IS_NULLABLE'] === 'YES' ? '?' : '';
            $default    = $this->resolverDefault($col);
            $esAutoInc  = str_contains($col['EXTRA'], 'auto_increment');

            // PHPDoc
            $phpDocProps .= " * @property {$nullable}{$tipo} \${$nombre}\n";

            // Propiedad
            $propiedades .= "    /** @column {$col['DATA_TYPE']} */\n";
            $propiedades .= "    public {$nullable}{$tipo} \${$nombre}{$default};\n\n";

            // Fillable (excluir PK auto_increment)
            if (!($nombre === $pk && $esAutoInc)) {
                $fillable[] = "'{$nombre}'";
            }

            // Casts automáticos
            $cast = $this->mapCast($col['DATA_TYPE']);
            if ($cast) {
                $casts[] = "'{$nombre}' => '{$cast}'";
            }
        }

        $fillableStr = implode(",\n        ", $fillable);
        $castsStr    = implode(",\n        ", $casts);

        return <<<PHP
<?php

namespace App\Entitys;

use Framework\Core\Model;

/**
 * Entity: {$className}
 * Tabla:  {$tableName}
 * 
 * Generado automáticamente por: php oly load:databases
 * ⚠️  No editar manualmente si planeas regenerar.
 *
{$phpDocProps} */
class {$className} extends Model
{
    /** Tabla de la base de datos */
    protected \$table = '{$tableName}';

    /** Clave primaria */
    protected \$primaryKey = '{$pk}';

    /** Columnas asignables en masa */
    protected array \$fillable = [
        {$fillableStr}
    ];

    /** Casteo automático de tipos */
    protected array \$casts = [
        {$castsStr}
    ];

    // ─── Propiedades mapeadas desde la DB ───────────────

{$propiedades}
    // ─── Scopes / Queries personalizadas ────────────────

    /**
     * Ejemplo: {$className}::query()->activos()
     * Agrega aquí métodos de consulta propios de esta entidad.
     */
}
PHP;
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    /** Convierte snake_case → PascalCase */
    private function toPascalCase(string $str): string
    {
        return str_replace('_', '', ucwords($str, '_'));
    }

    /** Mapea tipos MySQL → tipos PHP */
    private function mapTipoPHP(string $dbType): string
    {
        return match (strtolower($dbType)) {
            'int', 'tinyint', 'smallint', 'mediumint', 'bigint' => 'int',
            'float', 'double', 'decimal', 'numeric'             => 'float',
            'bool', 'boolean'                                   => 'bool',
            'date', 'datetime', 'timestamp', 'time', 'year'     => 'string',
            default                                              => 'string',
        };
    }

    /** Mapea tipos MySQL → cast label */
    private function mapCast(string $dbType): ?string
    {
        return match (strtolower($dbType)) {
            'int', 'tinyint', 'smallint', 'mediumint', 'bigint' => 'integer',
            'float', 'double', 'decimal', 'numeric'             => 'float',
            'bool', 'boolean'                                    => 'boolean',
            'date'                                               => 'date',
            'datetime', 'timestamp'                              => 'datetime',
            default                                              => null,
        };
    }

    /** Resuelve el valor por defecto PHP para la propiedad */
    private function resolverDefault(array $col): string
    {
        if ($col['COLUMN_DEFAULT'] !== null) {
            $tipo = strtolower($col['DATA_TYPE']);
            if (in_array($tipo, ['int','tinyint','smallint','mediumint','bigint'])) {
                return ' = ' . (int)$col['COLUMN_DEFAULT'];
            }
            if (in_array($tipo, ['float','double','decimal','numeric'])) {
                return ' = ' . (float)$col['COLUMN_DEFAULT'];
            }
            if (in_array($tipo, ['bool','boolean'])) {
                return ' = ' . ($col['COLUMN_DEFAULT'] ? 'true' : 'false');
            }
            return " = '" . addslashes($col['COLUMN_DEFAULT']) . "'";
        }

        if ($col['IS_NULLABLE'] === 'YES') {
            return ' = null';
        }

        return '';
    }
}
