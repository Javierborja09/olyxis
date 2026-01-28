# 🚀 Olyxis Framework - PHP MVC Framework

<div align="center">
  <img src="https://i.ibb.co/FkfPgBSw/olyxsis.png" alt="Olyxis Framework Banner" width="100%"/>
  
  **Un framework PHP moderno y ligero construido desde cero**
  
  Diseñado para facilitar el desarrollo de aplicaciones web escalables con arquitectura MVC
  
  ![PHP](https://img.shields.io/badge/PHP-%3E%3D%208.0-777BB4?style=for-the-badge&logo=php&logoColor=white)
  ![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
  ![Tailwind](https://img.shields.io/badge/Tailwind-CSS-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
  ![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge)
  ![Version](https://img.shields.io/badge/version-1.0.0-blue?style=for-the-badge)
  
  **Desarrollado por:** Javier Jeanpool Borja Samaniego
</div>

---

## 📖 Documentación Web Completa

**[🌐 Ver Documentación Interactiva](https://javierborja09.github.io/olyxis/olyxis-docs.html)**

---

## 🎯 Características Principales

### ✅ **Arquitectura MVC Completa**
- Separación clara entre Modelos, Vistas y Controladores
- Enrutamiento flexible (GET, POST) con soporte para parámetros dinámicos
- Sistema de gestión de respuestas y peticiones (Request & Response)

### ✅ **CLI Tool Potente (Oly CLI)**
```bash
php oly serve                    # Levanta un servidor de desarrollo al instante
php oly make:controller          # Scaffolding automático de controladores
php oly route:list              # Visualiza todas tus rutas registradas
```

### ✅ **Base de Datos Flexible & Optimizada**
![Database](https://img.shields.io/badge/PDO-Prepared_Statements-success?style=flat-square&logo=database)
![UTF8](https://img.shields.io/badge/Encoding-UTF--8-blue?style=flat-square)

- **Uso Opcional:** Olyxis funciona perfectamente sin base de datos para proyectos ligeros o estáticos
- **PDO Ready:** Conexiones persistentes y Prepared Statements contra SQL Injection
- Soporte completo para UTF-8 y gestión de errores de conexión

### ✅ **Seguridad de Nivel Profesional**

<div align="center">
  
| 🛡️ Protección | 📊 Estado |
|:---:|:---:|
| **SQL Injection** | ![Protected](https://img.shields.io/badge/Protected-✓-success?style=flat-square) |
| **XSS Attacks** | ![Protected](https://img.shields.io/badge/Protected-✓-success?style=flat-square) |
| **CSRF** | ![Protected](https://img.shields.io/badge/Protected-✓-success?style=flat-square) |
| **Session Hijacking** | ![Protected](https://img.shields.io/badge/Protected-✓-success?style=flat-square) |

</div>

- Sistema de Middlewares (Onion Pattern) para interceptar y proteger rutas
- Protección nativa contra ataques XSS y validación de entrada
- Gestión segura de sesiones y mensajes Flash

**Ejemplo de protección XSS automática:**
```php
// ✅ Salida segura (htmlspecialchars automático)
<?= htmlspecialchars($producto['nombre']) ?>

// ❌ Nunca hagas esto
<?php echo $_GET['search']; ?>
```

### ✅ **Frontend & Configuración Modernos**
![Tailwind CSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

- Integración nativa con Tailwind CSS vía CDN
- Gestión de configuración mediante archivos `.env`

---

## 🚀 Instalación Rápida

Sigue estos pasos para tener Olyxis funcionando en menos de un minuto:

### 1️⃣ Clonar el repositorio
```bash
git clone https://github.com/Javierborja09/olyxis.git
```

### 2️⃣ Inicializar Proyecto
Este comando configura la estructura de carpetas necesaria en tu directorio actual.
```bash
php olyxis/oly init
```

### 3️⃣ Limpieza (Opcional)
Una vez ejecutado el `init`, ya tienes todo lo necesario en tu raíz. Puedes borrar la carpeta temporal:

**Windows (PowerShell):**
```powershell
Remove-Item -Recurse -Force olyxis
```

**Linux / macOS:**
```bash
rm -rf olyxis
```

### 4️⃣ Configurar Base de Datos (Opcional)
Si tu proyecto requiere persistencia, crea un archivo `.env` en la raíz con tus credenciales:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=olyxis_db
DB_USER=root
DB_PASSWORD=tu_password
```

> **💡 Nota:** Si no lo usas, Olyxis ignorará la conexión automáticamente.

### 5️⃣ ¡Lanzar!
Inicia el servidor de desarrollo y empieza a crear:
```bash
php oly serve
```

🌐 **Accede en:** http://localhost:8000

---

## 📁 Estructura del Proyecto

```
olyxis/
├── 📂 app/
│   ├── Controllers/    # Lógica de negocio y controladores
│   ├── Models/         # Clases que representan tus tablas
│   ├── Middlewares/    # Capas de seguridad y filtros
│   └── Views/          # Plantillas de interfaz de usuario
├── 📂 Framework/       # Núcleo del motor (Core, Router, Database)
├── 📂 config/          # Definición de rutas y archivos de configuración
└── 📂 public/          # Punto de entrada (index.php) y recursos públicos
```

---

## 💻 Comandos CLI (Oly CLI)

<div align="center">

| Comando | Descripción |
|:--------|:------------|
| `php oly serve` | Inicia el servidor de desarrollo local |
| `php oly route:list` | Muestra el mapa completo de rutas del sistema |
| `php oly make:controller [Nombre]` | Crea un controlador en `app/Controllers` |
| `php oly make:model [Nombre]` | Crea un modelo vinculado a una tabla |
| `php oly make:middleware [Nombre]` | Genera una capa de seguridad Onion Pattern |
| `php oly make:crud [Nombre]` | Genera un módulo CRUD completo de forma atómica |

</div>

**Ejemplos de uso:**
```bash
# Crear un controlador de productos
php oly make:controller ProductoController

# Crear un modelo de usuario
php oly make:model Usuario

# Ver todas las rutas
php oly route:list
```

---

## 🛡️ Seguridad y Validación

### Protección contra SQL Injection
```php
// ✅ Correcto - Prepared Statements automáticos
$productos = $producto->where('precio > ?', [500]);

// ❌ Incorrecto - Nunca hagas esto
$productos = $producto->where("precio > {$_GET['precio']}");
```

### Protección contra XSS
```php
<!-- ✅ Correcto - Escapado automático -->
<h1><?= htmlspecialchars($titulo) ?></h1>

<!-- ❌ Incorrecto - Vulnerable a XSS -->
<h1><?php echo $_POST['titulo']; ?></h1>
```

### Validación de Entrada
```php
// Validación en controlador
if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $request->setFlash('error', 'Email inválido');
    return $this->redirect('/registro');
}
```

---

## 📊 Características Técnicas

<div align="center">

| Característica | Tecnología |
|:--------------|:-----------|
| **Lenguaje** | PHP 8.0+ |
| **Base de Datos** | MySQL 8.0+ / PDO |
| **Routing** | Custom Router |
| **Template Engine** | PHP Nativo |
| **CSS Framework** | Tailwind CSS |
| **Seguridad** | Prepared Statements, XSS Protection, CSRF Tokens |

</div>

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Consulta el archivo `LICENSE` para más detalles.

---

## 👨‍💻 Autor

**Javier Jeanpool Borja Samaniego**

<div align="center">

[![GitHub](https://img.shields.io/badge/GitHub-JavierBorja09-181717?style=for-the-badge&logo=github)](https://github.com/JavierBorja09)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-Javier_Borja-0A66C2?style=for-the-badge&logo=linkedin)](https://www.linkedin.com/in/javier-jeanpool-borja-samaniego-a6b8b7300/)

</div>

---

<div align="center">

**⭐ Si te gusta Olyxis, dale una estrella en GitHub ⭐**

### Version: 1.0.0 | Enero 2026

![Status](https://img.shields.io/badge/Status-Activo-success?style=for-the-badge)
![Maintained](https://img.shields.io/badge/Maintained-Yes-green?style=for-the-badge)

**Hecho con ❤️ por Javier Borja**

</div>