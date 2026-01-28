<?php

namespace Framework\Console\Commands;

class MakeMiddlewareCommand 
{
    public function execute(array $args)
    {
        $name = $args[0] ?? null;

        if (!$name) {
            echo "❌ Error: Especifica el nombre del Middleware (ej: AuthMiddleware).\n";
            return;
        }

        if (!str_ends_with($name, 'Middleware')) {
            $name .= 'Middleware';
        }

        $directory = getcwd() . "/app/Middlewares";
        $path = "$directory/$name.php";

        if (file_exists($path)) {
            echo "⚠️  El middleware '$name' ya existe.\n";
            return;
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $template = $this->getTemplate($name);

        if (file_put_contents($path, $template)) {
            echo "✅ Middleware '$name' creado correctamente.\n";
        }
    }

    private function getTemplate($name)
    {
        return "<?php\n\nnamespace App\Middlewares;\n\nuse Framework\Core\Request;\nuse Framework\Core\Middleware;\n\nclass $name\n implements Middleware {\n    /**\n     * Procesa la petición.\n     *\n     * @param Request \$request\n     * @param callable \$next\n     * @return mixed\n     */\n    public function handle(Request \$request, callable \$next)\n    {\n        // Lógica antes del controlador\n\n        return \$next(\$request);\n    }\n}\n";
    }
}