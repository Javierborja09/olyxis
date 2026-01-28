<?php

namespace Framework\Console\Commands;

class MakeModelCommand {
    public function execute(array $args) {
        $input = $args[0] ?? null;

        if (!$input) {
            echo "❌ Error: Debes especificar el nombre del modelo.\n";
            return;
        }

        $className = ucfirst(strtolower($input));
        $tableName = strtolower($input);

        $directory = getcwd() . "/app/Models";
        $path = "$directory/$className.php";

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($path)) {
            echo "⚠️  Error: El modelo '$className' ya existe en $path\n";
            return; 
        }

        $template = "<?php\n\nnamespace App\Models;\n\nuse Framework\Database\Model;\n\nclass $className extends Model {\n    protected \$table = '" . $tableName . "';\n}\n";

        if (file_put_contents($path, $template)) {
            echo "✅ Modelo '$className' creado con éxito (Tabla: '$tableName').\n";
        } else {
            echo "❌ Error: No se pudo escribir el archivo en $path\n";
        }
    }
}