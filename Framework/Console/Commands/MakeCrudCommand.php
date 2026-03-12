<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

class MakeCrudCommand extends Command
{
    public function execute(array $args)
    {
        if (empty($args[0])) {
            echo "❌ Error: Uso correcto -> php oly make:crud NombreEntidad\n";
            echo "Ejemplo: php oly make:crud Productos\n";
            echo "         (La Entity debe existir en app/Entitys/)\n";
            return;
        }

        $className  = ucfirst($args[0]);
        $entityPath = getcwd() . "/app/Entitys/{$className}.php";

        // ── 1. Verificar que la Entity exista ────────────────────────
        if (!file_exists($entityPath)) {
            echo "❌ No se encontró la Entity en: app/Entitys/{$className}.php\n";
            echo "💡 Primero ejecuta: php oly load:databases\n";
            return;
        }

        // ── 2. Leer la Entity y extraer todo lo necesario ─────────────
        $entityData = $this->parseEntity($entityPath, $className);

        if (!$entityData) {
            echo "❌ No se pudo parsear la Entity. Verifica que esté bien formada.\n";
            return;
        }

        $tableName  = $entityData['table'];
        $primaryKey = $entityData['primaryKey'];
        $fields     = $entityData['fields']; 

        $this->info("- Tabla:          '{$tableName}'");
        $this->info("- Clave primaria: '{$primaryKey}'");
        $this->info("- Atributos:      " . implode(', ', array_column($fields, 'name')));

        // ── 3. Verificar que no existan ya Controller/Views ──────────
        $paths = [
            'Controller' => getcwd() . "/app/Controllers/{$className}Controller.php",
            'Views'      => getcwd() . "/app/Views/{$tableName}",
        ];

        foreach ($paths as $label => $path) {
            if (file_exists($path)) {
                echo "⚠️  Abortado: El {$label} ya existe en: {$path}\n";
                return;
            }
        }

        echo "\n🚀 Generando CRUD para '{$className}' (Tabla: '{$tableName}', PK: '{$primaryKey}')...\n\n";

        $this->createController($className, $tableName, $primaryKey);
        $this->createViews($tableName, $primaryKey, $fields);
        $this->addCrudRoutes($tableName, $className . 'Controller');

        echo "\n✅ ¡Módulo Olyxis generado con éxito!\n";
    }

    // ─────────────────────────────────────────────────────────────────
    // Lee el archivo .php de la Entity y extrae: table, primaryKey, fields
    // ─────────────────────────────────────────────────────────────────
    private function parseEntity(string $path, string $className): ?array
    {
        $src = file_get_contents($path);

        // Extraer $table
        preg_match('/protected\s+\$table\s*=\s*[\'"]([^\'"]+)[\'"]/', $src, $tableMatch);
        $table = $tableMatch[1] ?? strtolower($className);

        // Extraer $primaryKey
        preg_match('/protected\s+\$primaryKey\s*=\s*[\'"]([^\'"]+)[\'"]/', $src, $pkMatch);
        $primaryKey = $pkMatch[1] ?? 'id';

        // Extraer propiedades públicas tipadas:
        // public int $id = 0;
        // public ?string $nombre = '';
        // public ?float $precio = null;
        preg_match_all(
            '/public\s+(\??)(\w+)\s+\$(\w+)\s*(?:=\s*[^;]+)?;/',
            $src,
            $propMatches,
            PREG_SET_ORDER
        );

        $fields = [];
        foreach ($propMatches as $m) {
            $nullable = $m[1] === '?';
            $type     = $m[2];
            $name     = $m[3];

            // Ignorar la PK (no va en formularios)
            if ($name === $primaryKey) continue;

            $fields[] = [
                'name'     => $name,
                'type'     => $type,
                'nullable' => $nullable,
            ];
        }

        return [
            'table'      => $table,
            'primaryKey' => $primaryKey,
            'fields'     => $fields,
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // Controller — usa App\Entitys y la PK leída de la Entity
    // ─────────────────────────────────────────────────────────────────
    private function createController(string $className, string $tableName, string $primaryKey): void
    {
        $controllerName = $className . "Controller";
        $path           = "app/Controllers/{$controllerName}.php";

        $content = <<<PHP
<?php

namespace App\Controllers;

use Framework\Core\Controller;
use App\Entitys\\{$className};

class {$controllerName} extends Controller
{
    public function index(\$request)
    {
        return \$this->view('{$tableName}/index', [
            'data' => (new {$className}())->all(),
        ], 'layouts/main');
    }

    public function store(\$request)
    {
        (new {$className}())->create(\$request->post());
        return \$this->redirect('/{$tableName}');
    }

    public function update(\$request)
    {
        \$data = \$request->post();
        \$id   = \$data['{$primaryKey}'];
        unset(\$data['{$primaryKey}']);
        (new {$className}())->update(\$id, \$data);
        return \$this->redirect('/{$tableName}');
    }

    public function delete(\$request)
    {
        (new {$className}())->delete(\$request->post('{$primaryKey}'));
        return \$this->redirect('/{$tableName}');
    }
}
PHP;
        $this->createFile($path, $content);
        $this->info("- Controller creado: {$path}");
    }

    // ─────────────────────────────────────────────────────────────────
    // Views — inputs generados desde los fields reales de la Entity
    // ─────────────────────────────────────────────────────────────────
    private function createViews(string $folder, string $primaryKey, array $fields): void
    {
        $dir = "app/Views/{$folder}";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        // Inputs para modal Crear
        $createInputs = '';
        foreach ($fields as $f) {
            $label     = ucfirst(str_replace('_', ' ', $f['name']));
            $inputType = $this->resolveInputType($f['type']);
            $required  = $f['nullable'] ? '' : ' required';
            $createInputs .= <<<HTML

                <div class="form-group">
                    <label>{$label}</label>
                    <input type="{$inputType}" name="{$f['name']}"{$required}>
                </div>
HTML;
        }

        // Inputs para modal Editar
        $editInputs = '';
        foreach ($fields as $f) {
            $label     = ucfirst(str_replace('_', ' ', $f['name']));
            $inputType = $this->resolveInputType($f['type']);
            $required  = $f['nullable'] ? '' : ' required';
            $editInputs .= <<<HTML

                <div class="form-group">
                    <label>{$label}</label>
                    <input type="{$inputType}" name="{$f['name']}" id="edit_{$f['name']}"{$required}>
                </div>
HTML;
        }

        // Cabeceras fijas desde la Entity (no depende de tener datos en la tabla)
        $thHeaders = "                        <th>" . ucfirst($primaryKey) . "</th>\n";
        foreach ($fields as $f) {
            $thHeaders .= "                        <th>" . ucfirst(str_replace('_', ' ', $f['name'])) . "</th>\n";
        }
        $thHeaders .= "                        <th>Acciones</th>";

        $indexContent = <<<HTML
<style>
    .oly-container { padding: 20px; font-family: sans-serif; color: #333; }
    .oly-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .oly-table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .oly-table th, .oly-table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
    .oly-table th { background-color: #f8f9fa; font-weight: bold; }
    .btn { padding: 8px 15px; border-radius: 4px; cursor: pointer; border: none; text-decoration: none; font-size: 14px; }
    .btn-primary { background: #2563eb; color: white; }
    .btn-warning { background: #f59e0b; color: white; }
    .btn-danger  { background: #dc2626; color: white; }
    .oly-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
    .oly-modal-content { background: white; margin: 8% auto; padding: 24px; width: 420px; border-radius: 8px; max-height: 82vh; overflow-y: auto; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
    .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
</style>

<div class="oly-container">
    <div class="oly-header">
        <h1>Gestión de {$folder}</h1>
        <button class="btn btn-primary" onclick="toggleModal('modalCreate', true)">+ Nuevo Registro</button>
    </div>

    <table class="oly-table">
        <thead>
            <tr>
{$thHeaders}
            </tr>
        </thead>
        <tbody>
            <?php foreach (\$data as \$item): ?>
            <tr>
                <?php foreach (\$item as \$value): ?>
                    <td><?= htmlspecialchars((string)(\$value ?? '')) ?></td>
                <?php endforeach; ?>
                <td>
                    <button class="btn btn-warning"
                        onclick="openEditModal(<?= htmlspecialchars(json_encode(\$item)) ?>)">
                        Editar
                    </button>
                    <form action="/{$folder}/delete" method="POST" style="display:inline;"
                          onsubmit="return confirm('¿Eliminar este registro?')">
                        <input type="hidden" name="{$primaryKey}" value="<?= \$item['{$primaryKey}'] ?>">
                        <button class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty(\$data)): ?>
                <tr>
                    <td colspan="99" style="text-align:center;color:#999;padding:24px;">
                        Sin registros aún.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Crear -->
<div id="modalCreate" class="oly-modal">
    <div class="oly-modal-content">
        <h2>Nuevo Registro</h2>
        <form action="/{$folder}/store" method="POST">
            {$createInputs}
            <div style="text-align:right;margin-top:12px;">
                <button type="button" class="btn" onclick="toggleModal('modalCreate', false)">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editar -->
<div id="modalEdit" class="oly-modal">
    <div class="oly-modal-content">
        <h2>Editar Registro</h2>
        <form action="/{$folder}/update" method="POST">
            <input type="hidden" name="{$primaryKey}" id="edit_{$primaryKey}">
            {$editInputs}
            <div style="text-align:right;margin-top:12px;">
                <button type="button" class="btn" onclick="toggleModal('modalEdit', false)">Cancelar</button>
                <button type="submit" class="btn btn-warning">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<script>
    const PRIMARY_KEY = '{$primaryKey}';

    function toggleModal(id, show) {
        document.getElementById(id).style.display = show ? 'block' : 'none';
    }

    function openEditModal(data) {
        document.getElementById('edit_' + PRIMARY_KEY).value = data[PRIMARY_KEY];
        for (const key in data) {
            const input = document.getElementById('edit_' + key);
            if (input) input.value = data[key] ?? '';
        }
        toggleModal('modalEdit', true);
    }

    window.onclick = function (event) {
        if (event.target.className === 'oly-modal') {
            event.target.style.display = 'none';
        }
    }
</script>
HTML;

        $this->createFile("{$dir}/index.php", $indexContent);
        $this->info("- View creada: {$dir}/index.php");
    }

    // Mapea tipo PHP → type de input HTML
    private function resolveInputType(string $phpType): string
    {
        return match ($phpType) {
            'int', 'float' => 'number',
            'bool'         => 'checkbox',
            default        => 'text',
        };
    }

    // Registra las rutas en config/routes.php
    private function addCrudRoutes(string $path, string $controller): void
    {
        $routesPath = 'config/routes.php';
        if (!file_exists($routesPath)) return;

        $content = file_get_contents($routesPath);

        $get  = "            '/{$path}' => '{$controller}@index',";
        $post = "            '/{$path}/store'  => '{$controller}@store',\n" .
                "            '/{$path}/update' => '{$controller}@update',\n" .
                "            '/{$path}/delete' => '{$controller}@delete',";

        $content = preg_replace("/'GET' => \[/",  "'GET' => [\n{$get}",   $content);
        $content = preg_replace("/'POST' => \[/", "'POST' => [\n{$post}", $content);

        file_put_contents($routesPath, $content);
        $this->info("- Rutas registradas en: {$routesPath}");
    }
}
