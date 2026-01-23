# 🚀 Mi Framework - PHP MVC Framework

Un framework PHP moderno y ligero construido desde cero, diseñado para facilitar el desarrollo de aplicaciones web escalables con arquitectura MVC.

**Desarrollado por:** Javier Jeanpool Borja Samaniego

---

## 🎯 Características Principales

✅ **Arquitectura MVC Completa**
- Separación clara entre Modelos, Vistas y Controladores
- Enrutamiento flexible (GET, POST)
- Sistema de formularios seguro

✅ **CLI Tool Potente**
- `php bin/miframework serve` - Iniciar servidor de desarrollo
- `php bin/miframework init` - Inicializar nuevos proyectos
- `php bin/miframework make:controller` - Generar controladores automáticamente

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

## 📋 Requisitos

- **PHP:** 8.0 o superior
- **MySQL:** 8.0 o superior
- **Composer:** Última versión
- **Sistema Operativo:** Windows, Linux, macOS

---

## 🚀 Instalación Rápida

### 1. Clonar el repositorio

```bash
git clone https://github.com/Javierborja09/mi-framework.git
cd mi-framework
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar base de datos

Crear archivo `.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=mi_framework_db
DB_USER=root
DB_PASSWORD=tu_password
```

### 4. Iniciar servidor

```bash
php bin/miframework serve localhost 8000
```

Acceder a: http://localhost:8000

---

## 📁 Estructura del Proyecto

```
mi-framework/
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
│   └── miframework      # CLI ejecutable
├── .env                 # Variables de entorno
└── composer.json        # Dependencias del proyecto
```

---

## 💻 Ejemplo de Uso

### Crear un Controlador

```bash
php bin/miframework make:controller ProductController
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
| `php bin/miframework serve` | Iniciar servidor en puerto 8000 |
| `php bin/miframework serve localhost 3000` | Iniciar en puerto específico |
| `php bin/miframework init` | Inicializar proyecto |
| `php bin/miframework make:controller NombreController` | Generar controlador |

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

1. Fork el proyecto
2. Crear una rama (`git checkout -b feature/AmazingFeature`)
3. Commit los cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

---

## 📄 Licencia

Este proyecto está bajo licencia MIT. Ver archivo `LICENSE` para más detalles.

---

## 👨‍💻 Autor

**Javier Jeanpool Borja Samaniego**

- GitHub: [@tu-usuario](https://github.com/tu-usuario)
- Email: tu-email@ejemplo.com
- LinkedIn: [Tu Perfil](https://linkedin.com/in/tu-perfil)

---

## 🙏 Agradecimientos

- Comunidad PHP
- Tailwind CSS
- MySQL Community

---

## 📞 Soporte

¿Preguntas o problemas? 

- 📧 Email: soporte@miframework.com
- 🐛 Issues: [Reportar Bug](https://github.com/tu-usuario/mi-framework/issues)
- 💬 Discussions: [Discusiones](https://github.com/tu-usuario/mi-framework/discussions)

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
