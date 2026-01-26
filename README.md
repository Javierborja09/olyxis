# 🚀 Olyxis Framework - PHP MVC Framework

Un framework PHP moderno y ligero construido desde cero, diseñado para facilitar el desarrollo de aplicaciones web escalables con arquitectura MVC.
![Logo de Olyxis](public/images/image.png)
**Desarrollado por:** Javier Jeanpool Borja Samaniego

---


## 🎯 Características Principales

✅ **Arquitectura MVC Completa**
- Separación clara entre Modelos, Vistas y Controladores
- Enrutamiento flexible (GET, POST)
- Sistema de formularios seguro

✅ **CLI Tool Potente**
- `php bin/olyxis serve` - Iniciar servidor de desarrollo
- `php bin/olyxis init` - Inicializar nuevos proyectos
- `php bin/olyxis make:controller` - Generar controladores automáticamente

✅ **Base de Datos Optimizada**
- PDO con conexiones persistentes
- Migraciones y esquemas SQL
- Índices automáticos para mejor rendimiento
- Soporte completo UTF-8

✅ **Frontend Moderno**
- Integración Tailwind CSS
- Componentes responsivos
- Diseño moderno y profesional

✅ **Seguridad**
- Prepared statements con PDO
- Protección XSS
- Validación de entrada


✅ **Gestión de Configuración**
- Soporte para archivos `.env`
- Configuración centralizada
- Variables de entorno

---

## 🚀 Funcionalidades Avanzadas y Ejemplos

### Middlewares Personalizados

Puedes proteger rutas y controlar la sesión de usuario usando middlewares:

```php
// app/Middlewares/AuthMiddleware.php
class AuthMiddleware implements Middleware {
    public function handle(Request $request, callable $next) {
        if (!$request->session()->get('user_id')) {
            $request->setFlash('error', 'Debes iniciar sesión para acceder.');
            return new Response('', 302, ['Location' => '/']);
        }
        return $next($request);
    }
}

// app/Middlewares/SessionTimeoutMiddleware.php
class SessionTimeoutMiddleware implements Middleware {
    public function handle(Request $request, callable $next) {
        $session = $request->session();
        $now = time();
        $timeout = 300; // 5 minutos
        if ($session->has('user_id')) {
            $lastActivity = $session->get('last_activity');
            if ($lastActivity && ($now - $lastActivity > $timeout)) {
                $session->destroy();
                $session->setFlash('error', 'Tu sesión ha expirado por inactividad.');
                return new Response('', 302, ['Location' => '/']);
            }
            $session->set('last_activity', $now);
        }
        return $next($request);
    }
}
```

### Ejemplo de uso de Middlewares en un Controller

```php
class ProductosController extends Controller {
    public function __construct() {
        $this->middleware(AuthMiddleware::class);
        $this->middleware(SessionTimeoutMiddleware::class);
    }
    // ...
}
```

### Ventas Multi-producto y Validación de Stock

```php
// Procesar venta de varios productos en lote
public function store_lote(Request $request) {
    $items = $request->post('items');
    if (empty($items)) {
        $request->setFlash('error', 'La lista de venta está vacía.');
        return $this->redirect('/ventas');
    }
    $exito = true;
    foreach ($items as $item) {
        if (!$this->ventaDao->create([
            'producto_id' => $item['id'],
            'cantidad'    => $item['cantidad'],
            'precio'      => $item['precio']
        ])) {
            $exito = false;
            break;
        }
    }
    if ($exito) {
        $request->setFlash('success', 'Venta procesada con éxito y stock actualizado.');
    } else {
        $request->setFlash('error', 'Error crítico al procesar la venta. Intente de nuevo.');
    }
    return $this->redirect('/ventas');
}
```

### Reportes y KPIs de Ventas

```php
public function reportes(Request $request) {
    $inicio = $request->get('fecha_inicio');
    $fin = $request->get('fecha_fin');
    return $this->view('ventas/reportes', [
        'title'   => 'Reporte de Ventas',
        'resumen' => $this->ventaDao->getResumenGeneral($inicio, $fin),
        'ventas'  => $this->ventaDao->getAll($inicio, $fin)
    ], 'layouts/main');
}
```

### Uso de Flashes para Mensajes de Usuario

```php
// En el controlador
$request->setFlash('success', 'Operación exitosa.');
$request->setFlash('error', 'Ocurrió un error.');

// En la vista (ejemplo Blade/PHP)
<?php if ($flash = $request->getFlash('success')): ?>
    <div class="alert alert-success"><?= $flash ?></div>
<?php endif; ?>
```

### Uso de Stored Procedures para Seguridad y Rendimiento

```php
// Ejemplo en un DAO
public function getAll(): array {
    $stmt = $this->db->call('sp_listar_productos');
    return $stmt->fetchAll();
}
```

### Ejemplo Completo de routes.php

```php
// config/routes.php
// Este archivo define las rutas disponibles en la aplicación y a qué función de cada controlador apuntan.
// El array principal tiene dos claves: 'GET' y 'POST', que corresponden a los métodos HTTP.
// Cada ruta apunta a un método específico del controlador en formato 'Controlador@funcion'.

return [
    'GET' => [
        // Página de inicio de sesión
        '/' => 'LoginController@index', // Muestra el formulario de login

        // Productos
        '/productos' => 'ProductosController@index', // Lista todos los productos

        // Categorías
        '/categorias' => 'CategoriasController@index', // Lista todas las categorías

        // Ventas
        '/ventas' => 'VentasController@index', // Módulo principal de ventas
        '/ventas/reportes' => 'VentasController@reportes', // Reporte de ventas filtrado por fecha
    ],
    'POST' => [
        // Autenticación
        '/login' => 'LoginController@authenticate', // Procesa el login
        '/logout' => 'LoginController@logout', // Cierra la sesión

        // Productos
        '/productos/store' => 'ProductosController@store', // Crea un nuevo producto
        '/productos/update' => 'ProductosController@update', // Actualiza un producto existente
        '/productos/delete/{id}' => 'ProductosController@destroy', // Elimina un producto por ID

        // Categorías
        '/categorias/store' => 'CategoriasController@store', // Crea una nueva categoría
        '/categorias/update' => 'CategoriasController@update', // Actualiza una categoría existente
        '/categorias/delete/{id}' => 'CategoriasController@destroy', // Elimina una categoría por ID

        // Ventas
        '/ventas/store-lote' => 'VentasController@store_lote', // Procesa una venta de varios productos
    ]
];
```

---

## 🛡️ Pruebas de Seguridad y Robustez

El framework y las aplicaciones basadas en él han superado pruebas de pentesting (SQLi, XSS, Path Traversal, Command Injection, Fuerza Bruta), garantizando un alto estándar de seguridad.

---

---

## 📋 Requisitos

- **PHP:** 8.0 o superior
- **MySQL:** 8.0 o superior
- **Composer:** Última versión
- **Sistema Operativo:** Windows, Linux, macOS

---

## 🚀 Instalación Rápida

### 1. Clonar el repositorio

```bash
git clone https://github.com/Javierborja09/olyxis.git
```

### 2. Inicializar proyecto (Opcional - para nuevos proyectos)

```bash
php olyxis/bin/olyxis init
```

### 3. Inicializar y nota sobre la carpeta temporal

El comando `php olyxis/bin/olyxis init` copia la estructura del framework en tu carpeta actual. No es necesario ejecutar `cd olyxis`.

Si no necesitas conservar la carpeta temporal `olyxis`, puedes eliminarla:

En Linux/macOS:
```bash
rm -r olyxis
```

En Windows (PowerShell):
```powershell
Remove-Item -Recurse -Force olyxis
```

### 4. Configurar base de datos (Opcional)

Crear archivo `.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=olyxis_db
DB_USER=root
DB_PASSWORD=tu_password
```

### 5. Iniciar servidor

```bash
php bin/olyxis serve localhost 8000
```

Acceder a: http://localhost:8000

> **Nota:** El repositorio ya incluye todas las dependencias (vendor/). No necesitas ejecutar `composer install`.

---

## 📁 Estructura Inicial del Proyecto

```
olyxis/
├── app/
│   ├── Controllers/       # Controladores de la aplicación
│   ├── Models/           # Modelos de datos
│   └── Views/            # Vistas y templates
├── config/
│   ├── routes.php        # Definición de rutas
│   ├── app.php           # Configuración general
│   └── database.php      # Configuración de BD
├── Framework/
│   ├── Core/            # Núcleo del framework
│   ├── Console/         # Comandos CLI
│   └── Database.php     # Clase de conexión
├── database/
│   └── schema.sql       # Esquema de la base de datos
├── public/
│   ├── index.php        # Punto de entrada
│   ├── css/
│   └── js/
├── vendor/              # Dependencias (generado por Composer)
├── bin/
│   └── olyxis      # CLI ejecutable
├── .env                 # Variables de entorno
└── composer.json        # Dependencias del proyecto
```

---

## 💻 Ejemplo de Uso

### Crear un Controlador

```bash
php bin/olyxis make:controller ProductController
```

### Definir una Ruta

```php
// config/routes.php
'GET' => [
    '/products' => 'ProductController@index',
],
'POST' => [
    '/products/store' => 'ProductController@store',
]
```

### Crear un Modelo

```php
<?php
namespace App\Models;

class Product extends Model {
    protected $table = 'products';
}
```

### Usar en el Controlador

```php
<?php
namespace App\Controllers;

use App\Models\Product;

class ProductController extends Controller {
    public function index($request) {
        $productModel = new Product();
        $products = $productModel->all();
        
        return $this->view('products/index', [
            'products' => $products
        ]);
    }
}
```

### Crear una Vista

```php
<!-- app/Views/products/index.php -->
<div class="grid md:grid-cols-3 gap-6">
    <?php foreach ($products as $product): ?>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold"><?php echo $product['name']; ?></h2>
            <p class="text-gray-600">$<?php echo $product['price']; ?></p>
        </div>
    <?php endforeach; ?>
</div>
```

---

## 🗄️ Gestión de Base de Datos

### Usar el Modelo

```php
$product = new Product();

// Obtener todos los registros
$all = $product->all();

// Obtener un registro por ID
$one = $product->find(1);

// Crear un nuevo registro
$product->create([
    'name' => 'Laptop',
    'price' => 1299.99
]);

// Actualizar
$product->update(1, ['price' => 999.99]);

// Eliminar
$product->delete(1);
```

### Usar la Clase Database Directamente

```php
use Framework\Core\Database;

$db = Database::getInstance();

$result = $db->fetchAll(
    "SELECT * FROM products WHERE price > ?",
    [100]
);
```

---

## 🎨 Tailwind CSS Integration

El framework incluye Tailwind CSS via CDN. Personaliza en `app/Views/layouts/main.php`:

```php
<script src="https://cdn.tailwindcss.com"></script>
```

---

## 🔧 Comandos Disponibles

| Comando | Descripción |
|---------|------------|
| `php bin/olyxis serve` | Iniciar servidor en puerto 8000 |
| `php bin/olyxis serve localhost 3000` | Iniciar en puerto específico |
| `php bin/olyxis init` | Inicializar proyecto |
| `php bin/olyxis make:controller NombreController` | Generar controlador |

---

## 📊 Características de Seguridad

✅ **Prepared Statements** - Previene SQL injection
✅ **Validación de Entrada** - Sanitización de datos
✅ **CSRF Protection** - Protección contra ataques CSRF
✅ **Sesiones Seguras** - Gestión de sesiones PHP
✅ **Encriptación** - Soporte para contraseñas hasheadas

---

## 🚀 Optimización

- **Conexiones Persistentes PDO** - Mejor rendimiento
- **Índices de Base de Datos** - Consultas rápidas
- **Caché** - Soporte para caché de vistas
- **UTF-8 Completo** - Soporte multiidioma

---

## 📝 Ejemplo Completo: Sistema de Contacto

```php
// 1. Controlador
class ContactController extends Controller {
    public function send($request) {
        $contact = new Contact();
        $contact->create([
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'message' => $_POST['message']
        ]);
        
        return redirect('/contact?success=1');
    }
}

// 2. Ruta
'POST' => [
    '/contact/send' => 'ContactController@send'
]

// 3. Vista
<form action="/contact/send" method="POST">
    <input type="text" name="name" required>
    <input type="email" name="email" required>
    <textarea name="message" required></textarea>
    <button type="submit">Enviar</button>
</form>
```

---

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Para cambios grandes:

---

## 📄 Licencia

Este proyecto está bajo licencia MIT. Ver archivo `LICENSE` para más detalles.

---

## 👨‍💻 Autor

**Javier Jeanpool Borja Samaniego**

- GitHub: [@JavierBorja09](https://github.com/JavierBorja09)
- Email: javierborjasamaniego@gmail.com
- LinkedIn: [Mi Perfil](https://www.linkedin.com/in/javier-jeanpool-borja-samaniego-a6b8b7300/)

---

## 🙏 Agradecimientos

- Comunidad PHP
- Tailwind CSS
- MySQL Community

---

## 📞 Soporte

¿Preguntas o problemas? 

- 🐛 Issues: [Reportar Bug](https://github.com/Javierborja09/olyxis/issues)
- 💬 Discussions: [Discusiones](https://github.com/Javierborja09/olyxis/discussions)

---

## 🎯 Roadmap

- [ ] Autenticación integrada
- [ ] Sistema de permisos (ACL)
- [ ] Generador de CRUD
- [ ] API REST automática
- [ ] Testing framework
- [ ] Documentación ampliada
- [ ] Docker support

---

**¡Gracias por usar Mi Framework! ⭐ Star nos en GitHub si te fue útil.**

---

### Version: 1.0.0
### Última actualización: Enero 2026
### Estado: ✅ Activo y en desarrollo
