<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;

class InitCommand extends Command
{

    public function execute(array $args)
    {
        $this->info("🚀 Inicializando proyecto en: " . getcwd() . "\n");

        $this->createDirectories();
        $this->createConfigFiles();
        $this->createControllers();
        $this->createViews();
        $this->createLayout();
        $this->createComponents();
        $this->createAssets();
        $this->createIndexFile();
        $this->copyFrameworkCore();
        $this->createComposerJson();
        $this->createLocalBin();
        $this->info("\n🔄 Regenerando autoloader...");
        shell_exec('composer dump-autoload -o');
        $this->success("Autoloader actualizado correctamente.");
        $this->success("\n¡Proyecto inicializado correctamente! 🎉");
        $this->info("\nPara iniciar el servidor ejecuta:");
        $this->info("   php bin/olyxis serve\n");
    }

    private function copyFrameworkCore()
    {
        $source = __DIR__ . '/../../';
        $dest = getcwd() . '/Framework';

        if ($this->copyRecursive($source, $dest)) {
            $this->info("✔️ Carpeta Framework copiada íntegramente");
        }
    }

    private function copyRecursive($src, $dst)
    {
        if (!is_dir($src)) return false;
        if (!is_dir($dst)) mkdir($dst, 0777, true);

        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyRecursive($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
        return true;
    }

    private function createComponents()
    {
        $this->info("📦 Configurando componentes globales...");
        $source = __DIR__ . '/../../../app/Views/components';
        $dest = getcwd() . '/app/Views/components';

        if (is_dir($source)) {
            $this->copyRecursive($source, $dest);
            $this->info("✔️ Componentes copiados a app/Views/components");
        } else {
            $alertContent = <<<'PHP'
<?php if (isset($message) && $message): ?>
    <div id="alert-container" style="padding: 10px; background: <?= $type === 'success' ? '#dff0d8' : '#f2dede' ?>; color: <?= $type === 'success' ? '#3c763d' : '#a94442' ?>; margin-bottom: 15px; border-radius: 4px; transition: opacity 0.5s ease;">
        <?= htmlspecialchars($message) ?>
    </div>
    <script>
        setTimeout(function() {
            const alert = document.getElementById('alert-container');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        }, 2000);
    </script>
<?php endif; ?>
PHP;
            $this->createFile('app/Views/components/Alert.php', $alertContent);
        }
    }

    private function createLocalBin()
    {
        $binDir = getcwd() . '/bin';
        if (!is_dir($binDir)) {
            mkdir($binDir, 0777, true);
        }
        $originalBin = __DIR__ . '/../../../bin/olyxis';
        $newBin = $binDir . '/olyxis';

        if (file_exists($originalBin)) {
            copy($originalBin, $newBin);
            chmod($newBin, 0755);
            $this->info("✔️ Binario local creado en bin/olyxis");
        }
    }

    private function createDirectories()
    {
        $dirs = [
            'app/Controllers',
            'app/Models',
            'app/Views/home',
            'app/Views/layouts',
            'config',
            'app/Views/components',
            'public/css',
            'public/js',
            'public/images',
            'bin'
        ];

        foreach ($dirs as $dir) {
            $path = getcwd() . '/' . $dir;
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
                $this->info("✔️ Carpeta creada: $dir");
            }
        }
    }
    protected function createFile($file, $content)
    {
        $path = getcwd() . '/' . $file;
        file_put_contents($path, $content);
        $this->info("✔️ Archivo creado: $file");
    }

    private function createConfigFiles()
    {
        $routesContent = <<<'PHP'
<?php
return [
    'GET' => [
        '/' => 'HomeController@index',
        '/about' => 'HomeController@about',
        '/contact' => 'HomeController@contact',
    ],
    'POST' => [
        '/contact' => 'HomeController@contactSubmit',
    ]
];
PHP;
        $this->createFile('config/routes.php', $routesContent);

        $appContent = <<<'PHP'
<?php
return [
    'name' => 'Mi Aplicación',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://localhost:8000',
];
PHP;
        $this->createFile('config/app.php', $appContent);
    }

    private function createControllers()
    {
        $homeControllerContent = <<<'PHP'
<?php
namespace App\Controllers;

use Framework\Core\Controller;

class HomeController extends Controller {
    
    public function index($request) {
        return $this->view('home/index', [
            'title' => 'Bienvenido',
            'message' => '¡Tu framework está funcionando correctamente!'
        ], 'layouts/main');
    }
    
    public function about($request) {
        return $this->view('home/about', [
            'title' => 'Acerca de'
        ], 'layouts/main');
    }
    
    public function contact($request) {
        return $this->view('home/contact', [
            'title' => 'Contacto'
        ], 'layouts/main');
    }
    
    public function contactSubmit($request) {
        $data = $request->post();
        
        return $this->json([
            'success' => true,
            'message' => 'Mensaje recibido correctamente',
            'data' => $data
        ]);
    }
}
PHP;
        $this->createFile('app/Controllers/HomeController.php', $homeControllerContent);
    }

    private function createViews()
    {
        $indexView = <<<'PHP'
<div class="hero">
    <h1><?php echo htmlspecialchars($title); ?></h1>
    <p><?php echo htmlspecialchars($message); ?></p>
    <a href="/about" class="btn btn-primary">Conoce más</a>
</div>

<section class="features">
    <h2>Características</h2>
    <div class="features-grid">
        <div class="feature-card">
            <h3>🚀 Rápido</h3>
            <p>Framework ligero y eficiente</p>
        </div>
        <div class="feature-card">
            <h3>📦 MVC</h3>
            <p>Arquitectura Modelo-Vista-Controlador</p>
        </div>
        <div class="feature-card">
            <h3>🎨 Flexible</h3>
            <p>Fácil de personalizar y extender</p>
        </div>
    </div>
</section>
PHP;
        $this->createFile('app/Views/home/index.php', $indexView);

        $aboutView = <<<'PHP'
<div class="page-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
</div>

<section class="content">
    <h2>Acerca de este proyecto</h2>
    <p>Este es un framework PHP MVC inspirado en la arquitectura de PocketMine.</p>
</section>
PHP;
        $this->createFile('app/Views/home/about.php', $aboutView);

        $contactView = <<<'PHP'
<div class="page-header">
    <h1><?php echo htmlspecialchars($title); ?></h1>
</div>

<section class="content">
    <form action="/contact" method="POST" class="contact-form">
        <input type="text" name="name" placeholder="Nombre" required>
        <input type="email" name="email" placeholder="Email" required>
        <textarea name="message" placeholder="Mensaje" rows="5" required></textarea>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>
</section>
PHP;
        $this->createFile('app/Views/home/contact.php', $contactView);
    }

    private function createLayout()
    {
        $layoutContent = <<<'PHP'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Mi Aplicación'; ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="/" class="logo">Mi Framework</a>
            <ul class="nav-menu">
                <li><a href="/">Inicio</a></li>
                <li><a href="/about">Acerca de</a></li>
                <li><a href="/contact">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <?php echo $content; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Mi Framework</p>
        </div>
    </footer>
</body>
</html>
PHP;
        $this->createFile('app/Views/layouts/main.php', $layoutContent);
    }

    private function createAssets()
    {
        $cssContent = <<<'CSS'
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: system-ui, sans-serif; line-height: 1.6; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
header { background: #2c3e50; color: white; padding: 1rem 0; }
nav { display: flex; justify-content: space-between; align-items: center; }
.logo { font-size: 1.5rem; font-weight: bold; color: white; text-decoration: none; }
.nav-menu { display: flex; list-style: none; gap: 2rem; }
.nav-menu a { color: white; text-decoration: none; }
main { min-height: calc(100vh - 200px); padding: 2rem 0; }
.hero { text-align: center; padding: 4rem 2rem; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 10px; margin-bottom: 3rem; }
.btn { display: inline-block; padding: 0.8rem 2rem; background: #3498db; color: white; text-decoration: none; border-radius: 5px; }
.features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 2rem; }
.feature-card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.content { background: white; padding: 2rem; border-radius: 10px; }
footer { background: #34495e; color: white; text-align: center; padding: 2rem 0; margin-top: 3rem; }
CSS;
        $this->createFile('public/css/style.css', $cssContent);

        $this->createFile('public/js/app.js', '');
    }

    private function createIndexFile()
    {
        $indexContent = <<<'PHP'
<?php
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Framework/Core/Helpers.php';
use Framework\Core\Application;

try {
    $app = new Application();
    $app->run();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
PHP;
        $this->createFile('public/index.php', $indexContent);
    }

    private function createComposerJson()
    {
        $frameworkPath = realpath(__DIR__ . '/../../..');
        $projectPath = getcwd();
        $relativePath = $this->getRelativePath($projectPath, $frameworkPath);

        $composerContent = <<<JSON
{
    "require": {
        "php": ">=8.0"
    },
    "repositories": [
        {
            "type": "path",
            "url": "$relativePath"
        }
    ],
    "require-dev": {
        "javierborja09/olyxis": "@dev"
    },
    "autoload": {
        "psr-4": {
            "Framework\\\\": "Framework/",
            "App\\\\": "app/"
        }
    }
}
JSON;
        $this->createFile('composer.json', $composerContent);
    }

    private function getRelativePath($from, $to)
    {
        $from = str_replace(DIRECTORY_SEPARATOR, '/', realpath($from));
        $to = str_replace(DIRECTORY_SEPARATOR, '/', realpath($to));

        $from = explode('/', $from);
        $to = explode('/', $to);

        $relpath = array();

        foreach ($from as $dir) {
            if (isset($to[0]) && $dir === $to[0]) {
                array_shift($to);
            } else {
                $relpath[] = '..';
            }
        }

        return implode('/', array_merge($relpath, $to));
    }
}
