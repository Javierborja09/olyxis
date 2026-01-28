# 🚀 Olyxis Framework - PHP MVC Framework

Un framework PHP moderno y ligero construido desde cero, diseñado para facilitar el desarrollo de aplicaciones web escalables con arquitectura MVC.
<div align="center">
  <img src="https://i.ibb.co/FkfPgBSw/olyxsis.png" alt="Banner" width="100%"/>
</div>
**Desarrollado por:** Javier Jeanpool Borja Samaniego

---


## 🎯 Características Principales

✅ **Arquitectura MVC Completa**
- Separación clara entre Modelos, Vistas y Controladores
- Enrutamiento flexible (GET, POST)
- Sistema de formularios seguro

✅ **CLI Tool Potente**
- `php oly serve` - Iniciar servidor de desarrollo
- `php oly make:controller` - Generar controladores automáticamente

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
php olyxis/oly init
```

### 3. Inicializar y nota sobre la carpeta temporal

El comando `php olyxis/oly init` copia la estructura del framework en tu carpeta actual. No es necesario ejecutar `cd olyxis`.

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
php oly serve localhost 8000
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
php oly make:controller Product
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


```markdown
# 🏛️ Guía de Desarrollo: Modelos y Base de Datos

El framework utiliza un sistema de abstracción que separa la lógica del núcleo (**Core**) de la lógica de tu aplicación (**App**).

---

## 🏗️ 1. El Núcleo: `Framework\Core\Model`

Esta clase reside en el motor del framework y automatiza todas las operaciones CRUD. **No debe ser modificada**, ya que proporciona los métodos heredados para todos tus modelos.

```php
<?php

namespace Framework\Core;

/**
 * Clase Base Model
 * Automatiza las operaciones CRUD usando la clase Database.
 */
abstract class Model
{
    protected $db;
    protected $table;

    public function __construct()
    {
        // Obtiene la instancia única de la conexión (Singleton)
        $this->db = Database::getInstance();
    }

    public function all()
    {
        return $this->db->all($this->table);
    }

    public function find($id)
    {
        return $this->db->find($this->table, $id);
    }

    public function create(array $data)
    {
        // El modelo expone 'create' pero ejecuta 'insert' en el Core Database
        return $this->db->insert($this->table, $data);
    }

    public function update($id, array $data)
    {
        return $this->db->update($this->table, $data, "id = ?", [$id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, "id = ?", [$id]);
    }
    
    public function where($condition, $params = [])
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$condition}";
        return $this->db->fetchAll($sql, $params);
    }
}
```

---

## 📦 2. Tus Modelos: `App\Models`

Cada modelo de tu aplicación extiende la clase base del Core. Solo necesitas definir la tabla asociada.

### Crear un Modelo

```php
<?php
namespace App\Models;

use Framework\Core\Model;

class Product extends Model 
{
    protected $table = 'products';
}
```

---

## 🎮 3. Usar Modelos en Controladores

```php
<?php
namespace App\Controllers;

use App\Models\Product;
use Framework\Core\Controller;

class ProductController extends Controller 
{
    public function index($request) 
    {
        $productModel = new Product();
        $products = $productModel->all();
        
        return $this->view('products/index', [
            'products' => $products
        ]);
    }
    
    public function show($request) 
    {
        $productModel = new Product();
        $id = $request['params']['id'];
        $product = $productModel->find($id);
        
        return $this->view('products/show', [
            'product' => $product
        ]);
    }
    
    public function store($request) 
    {
        $productModel = new Product();
        $data = [
            'name' => $request['post']['name'],
            'price' => $request['post']['price'],
            'description' => $request['post']['description']
        ];
        
        $productModel->create($data);
        
        return $this->redirect('/products');
    }
}
```

---

## 🎨 4. Crear Vistas

```php
<!-- app/Views/products/index.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Catálogo de Productos</h1>
        
        <div class="grid md:grid-cols-3 gap-6">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                    <h2 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="text-2xl font-bold text-green-600">$<?php echo number_format($product['price'], 2); ?></p>
                    <a href="/products/<?php echo $product['id']; ?>" 
                       class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Ver Detalles
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
```

---

## 🗄️ 5. Operaciones CRUD Completas

### Obtener todos los registros

```php
$product = new Product();
$all = $product->all();
```

### Obtener un registro por ID

```php
$product = new Product();
$one = $product->find(1);
```

### Crear un nuevo registro

```php
$product = new Product();
$product->create([
    'name' => 'Laptop HP',
    'price' => 1299.99,
    'description' => 'Laptop de alto rendimiento'
]);
```

### Actualizar un registro

```php
$product = new Product();
$product->update(1, [
    'price' => 999.99,
    'description' => 'Laptop en oferta'
]);
```

### Eliminar un registro

```php
$product = new Product();
$product->delete(1);
```

### Consultas personalizadas con WHERE

```php
$product = new Product();

// Productos con precio mayor a 500
$expensive = $product->where('price > ?', [500]);

// Productos por nombre
$laptops = $product->where('name LIKE ?', ['%Laptop%']);

// Productos activos
$active = $product->where('status = ?', ['active']);
```

---

## 🔒 6. Buenas Prácticas

### Validación de Datos

```php
public function store($request) 
{
    // Validar datos antes de crear
    $errors = [];
    
    if (empty($request['post']['name'])) {
        $errors[] = 'El nombre es requerido';
    }
    
    if (!is_numeric($request['post']['price']) || $request['post']['price'] <= 0) {
        $errors[] = 'El precio debe ser un número positivo';
    }
    
    if (!empty($errors)) {
        return $this->view('products/create', ['errors' => $errors]);
    }
    
    $productModel = new Product();
    $productModel->create($request['post']);
    
    return $this->redirect('/products');
}
```

### Sanitización de Salida

```php
<!-- Siempre usar htmlspecialchars para prevenir XSS -->
<h2><?php echo htmlspecialchars($product['name']); ?></h2>
<p><?php echo htmlspecialchars($product['description']); ?></p>
```

### Manejo de Errores

```php
public function show($request) 
{
    $productModel = new Product();
    $product = $productModel->find($request['params']['id']);
    
    if (!$product) {
        return $this->view('errors/404', [
            'message' => 'Producto no encontrado'
        ]);
    }
    
    return $this->view('products/show', ['product' => $product]);
}
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
| `php oly serve` | Iniciar servidor en puerto 8000 |
| `php oly serve localhost 3000` | Iniciar en puerto específico |
| `php oly init` | Inicializar proyecto |
| `php oly make:controller NombreController` | Generar controlador |

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
