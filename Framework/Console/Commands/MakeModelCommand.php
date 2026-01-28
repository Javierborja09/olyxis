<?php

namespace Framework\Console\Commands;

class MakeModelCommand {
    public function execute(array $args) {
        $name = $args[0] ?? null;

        if (!$name) {
            echo "❌ Error: Debes especificar el nombre del modelo.\n";
            return;
        }

        $directory = getcwd() . "/app/Models";
        $path = "$directory/$name.php";

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($path)) {
            echo "⚠️  Error: El modelo '$name' ya existe en $path\n";
            return; 
        }


        $template = "<?php\n\nnamespace App\Models;\n\nuse Framework\Database\Model;\n\nclass $name extends Model {\n    protected \$table = '" . strtolower($name) . "s';\n}\n";

        if (file_put_contents($path, $template)) {
            echo "✅ Modelo '$name' creado con éxito.\n";
        } else {
            echo "❌ Error: No se pudo escribir el archivo en $path\n";
        }
    }
}