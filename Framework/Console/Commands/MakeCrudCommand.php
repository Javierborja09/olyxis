<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

class MakeCrudCommand extends Command
{
  public function execute(array $args)
    {
        if (empty($args[0])) {
            echo "❌ Error: Uso correcto -> php olyxis make:crud NombreEntidad [nombre_tabla]\n";
            echo "Ejemplo: php olyxis make:crud Product productos\n";
            return;
        }

        $className = ucfirst($args[0]);
        $tableName = isset($args[1]) ? strtolower($args[1]) : strtolower($className);

        $paths = [
            'Models'      => getcwd() . "/app/Models/{$className}.php",
            'Controllers' => getcwd() . "/app/Controllers/{$className}Controller.php",
            'Views' => getcwd() . "/app/Views/{$tableName}",
        ];

        foreach ($paths as $label => $path) {
            if (file_exists($path)) {
                echo "⚠️  Abortado: El $label ya existe en: $path\n";
                return;
            }
        }

        echo "🚀 Iniciando generación de CRUD para '{$className}' (Tabla: '{$tableName}')...\n";
        $this->createModel($className, $tableName);
        $this->createController($className, $tableName);
        $this->createViews($tableName);
        $this->addCrudRoutes($tableName, $className . 'Controller');

        echo "\n✅ ¡Módulo Olyxis generado con éxito!\n";
    }

    private function createModel($className, $tableName)
    {
        $path = "app/Models/{$className}.php";
        $content = "<?php\nnamespace App\Models;\n\nuse Framework\Core\Model;\n\nclass {$className} extends Model {\n    protected \$table = '{$tableName}';\n}";
        $this->createFile($path, $content);
        $this->info("- Modelo creado: {$path}");
    }

   private function createController($className, $tableName)
    {
        $controllerName = $className . "Controller";
        $path = "app/Controllers/{$controllerName}.php";

        $content = <<<PHP
<?php
namespace App\Controllers;

use Framework\Core\Controller;
use App\Models\\{$className};

class {$controllerName} extends Controller {
    
    public function index(\$request) {
        \$model = new {$className}();
        return \$this->view('{$tableName}/index', [
            'data' => \$model->all()
        ], 'layouts/main');
    }

    public function store(\$request) {
        (new {$className}())->create(\$request->post());
        return \$this->redirect('/{$tableName}');
    }

    public function update(\$request) {
        \$data = \$request->post();
        \$id = \$data['id'];
        unset(\$data['id']);
        (new {$className}())->update(\$id, \$data);
        return \$this->redirect('/{$tableName}');
    }

    public function delete(\$request) {
        (new {$className}())->delete(\$request->post('id'));
        return \$this->redirect('/{$tableName}');
    }
}
PHP;
        $this->createFile($path, $content);
    }

    
private function createViews($folder)
    {
        $dir = "app/Views/{$folder}";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

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
    .btn-danger { background: #dc2626; color: white; }
    .oly-modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
    .oly-modal-content { background: white; margin: 10% auto; padding: 20px; width: 400px; border-radius: 8px; position: relative; }
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
                <?php if (!empty(\$data)): ?>
                    <?php foreach (array_keys(\$data[0]) as \$column): ?>
                        <th><?= ucfirst(str_replace('_', ' ', \$column)) ?></th>
                    <?php endforeach; ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach (\$data as \$item): ?>
            <tr>
                <?php foreach (\$item as \$value): ?>
                    <td><?= htmlspecialchars(\$value ?? '') ?></td>
                <?php endforeach; ?>
                <td>
                    <button class="btn btn-warning" onclick="openEditModal(<?= htmlspecialchars(json_encode(\$item)) ?>)">Editar</button>
                    <form action="/{$folder}/delete" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar?')">
                        <input type="hidden" name="id" value="<?= \$item['id'] ?>">
                        <button class="btn btn-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="modalCreate" class="oly-modal">
    <div class="oly-modal-content">
        <h2>Nuevo Registro</h2>
        <form action="/{$folder}/store" method="POST">
            <?php foreach (array_keys(\$data[0] ?? []) as \$col): 
                if(\$col == 'id' || str_contains(\$col, 'fecha') || str_contains(\$col, 'at')) continue; ?>
                <div class="form-group">
                    <label><?= ucfirst(str_replace('_', ' ', \$col)) ?></label>
                    <input type="text" name="<?= \$col ?>" required>
                </div>
            <?php endforeach; ?>
            <div style="text-align: right;">
                <button type="button" class="btn" onclick="toggleModal('modalCreate', false)">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEdit" class="oly-modal">
    <div class="oly-modal-content">
        <h2>Editar Registro</h2>
        <form action="/{$folder}/update" method="POST">
            <input type="hidden" name="id" id="edit_id">
            <?php foreach (array_keys(\$data[0] ?? []) as \$col): 
                if(\$col == 'id' || str_contains(\$col, 'fecha') || str_contains(\$col, 'at')) continue; ?>
                <div class="form-group">
                    <label><?= ucfirst(str_replace('_', ' ', \$col)) ?></label>
                    <input type="text" name="<?= \$col ?>" id="edit_<?= \$col ?>" required>
                </div>
            <?php endforeach; ?>
            <div style="text-align: right;">
                <button type="button" class="btn" onclick="toggleModal('modalEdit', false)">Cancelar</button>
                <button type="submit" class="btn btn-warning">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id, show) {
        document.getElementById(id).style.display = show ? 'block' : 'none';
    }

    function openEditModal(data) {
        document.getElementById('edit_id').value = data.id;
        for (const key in data) {
            const input = document.getElementById('edit_' + key);
            if (input) input.value = data[key];
        }
        toggleModal('modalEdit', true);
    }
    window.onclick = function(event) {
        if (event.target.className === 'oly-modal') {
            event.target.style.display = "none";
        }
    }
</script>
HTML;
           $this->createFile("{$dir}/index.php", $indexContent);
    }

    private function addCrudRoutes($path, $controller)
    {
        $routesPath = 'config/routes.php';
        if (!file_exists($routesPath)) return;

        $content = file_get_contents($routesPath);

        $get = "            '/{$path}' => '{$controller}@index',";
        $post = "            '/{$path}/store' => '{$controller}@store',\n" .
                "            '/{$path}/update' => '{$controller}@update',\n" .
                "            '/{$path}/delete' => '{$controller}@delete',";

        $content = preg_replace("/'GET' => \[/", "'GET' => [\n{$get}", $content);
        $content = preg_replace("/'POST' => \[/", "'POST' => [\n{$post}", $content);
        
        file_put_contents($routesPath, $content);
    }
}