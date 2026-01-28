# 🚀 Olyxis Framework - PHP MVC Framework

Un framework PHP moderno y ligero construido desde cero, diseñado para facilitar el desarrollo de aplicaciones web escalables con arquitectura MVC.
<div align="center">
  <img src="https://i.ibb.co/FkfPgBSw/olyxsis.png" alt="Banner" width="100%"/>
</div>
**Desarrollado por:** Javier Jeanpool Borja Samaniego

---
## 📖 Documentación Web Completa

**[🌐 Ver Documentación](https://javierborja09.github.io/olyxis/olyxis-docs.html)**

## 🎯 Características Principales

✅ **Arquitectura MVC Completa**
- Separación clara entre Modelos, Vistas y Controladores.
- Enrutamiento flexible (GET, POST) con soporte para parámetros dinámicos.
- Sistema de gestión de respuestas y peticiones (Request & Response).

✅ **CLI Tool Potente (Oly CLI)**
- php oly serve - Levanta un servidor de desarrollo al instante.
- php oly make:controller - Scaffolding automático de controladores.
- php oly route:list - Visualiza todas tus rutas registradas en una tabla profesional.

✅ **Base de Datos Flexible & Optimizada**
- Uso Opcional: Olyxis funciona perfectamente sin base de datos para proyectos ligeros o estáticos.
- PDO Ready: Conexiones persistentes y Prepared Statements contra SQL Injection.
- Soporte completo para UTF-8 y gestión de errores de conexión.

✅ **Seguridad de Nivel Profesional**
- Sistema de Middlewares (Onion Pattern) para interceptar y proteger rutas.
- Protección nativa contra ataques XSS y validación de entrada.
- Gestión segura de sesiones y mensajes Flash.

✅ **Frontend & Configuración Modernos**
- Integración nativa con Tailwind CSS vía CDN.
- Gestión de configuración mediante archivos .env.

---

## 🚀 Instalación Rápida

Sigue estos pasos para tener Olyxis funcionando en menos de un minuto:

### 1. Clonar el repositorio
git clone https://github.com/Javierborja09/olyxis.git

### 2. Inicializar Proyecto
Este comando configura la estructura de carpetas necesaria en tu directorio actual.
php olyxis/oly init

### 3. Limpieza (Opcional)
Una vez ejecutado el init, ya tienes todo lo necesario en tu raíz. Puedes borrar la carpeta temporal:

# En Windows (PowerShell):
Remove-Item -Recurse -Force olyxis

# En Linux o macOS:
rm -rf olyxis

### 4. Configurar Base de Datos (Opcional)
Si tu proyecto requiere persistencia, crea un archivo .env en la raíz con tus credenciales. Si no lo usas, Olyxis ignorará la conexión automáticamente.

### 5. ¡Lanzar!
Inicia el servidor de desarrollo y empieza a crear:
php oly serve

Accede en: http://localhost:8000

---

## 📁 Estructura del Proyecto

app/
├── Controllers/    # Lógica de negocio y controladores
├── Models/         # Clases que representan tus tablas
├── Middlewares/    # Capas de seguridad y filtros
└── Views/          # Plantillas de interfaz de usuario
Framework/          # Núcleo del motor (Core, Router, Database)
config/             # Definición de rutas y archivos de configuración
public/             # Punto de entrada (index.php) y recursos públicos

---

## 💻 Comandos CLI (Oly CLI)

| Comando | Descripción |
| :--- | :--- |
| php oly serve | Inicia el servidor de desarrollo local. |
| php oly route:list | Muestra el mapa completo de rutas del sistema. |
| php oly make:controller [Nombre] | Crea un controlador en app/Controllers. |
| php oly make:model [Nombre] | Crea un modelo vinculado a una tabla. |
| php oly make:middleware [Nombre] | Genera una capa de seguridad Onion Pattern. |
| php oly make:crud [Nombre] | Genera un módulo CRUD completo de forma atómica. |

---

## 👨‍💻 Autor

Javier Jeanpool Borja Samaniego
- GitHub: @JavierBorja09
- LinkedIn: https://www.linkedin.com/in/javier-jeanpool-borja-samaniego-a6b8b7300/

---
### Version: 1.0.0 | Enero 2026