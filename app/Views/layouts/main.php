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