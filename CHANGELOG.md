# Changelog

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto se adhiere al [Semantic Versioning](https://semver.org/lang/es/).

## [1.0.0] - 2026-01-23

### ✨ Añadido

- **Arquitectura MVC completa** - Soporte para Modelos, Vistas y Controladores
- **Enrutamiento flexible** - Soporte para rutas GET y POST
- **CLI Tool** - Herramientas de línea de comandos para desarrollo
  - `serve` - Iniciar servidor de desarrollo
  - `init` - Inicializar nuevos proyectos
  - `make:controller` - Generar controladores automáticamente
- **Sistema de Base de Datos**
  - Clase Database con singleton pattern
  - Soporte PDO con conexiones persistentes
  - Métodos CRUD en modelos
  - Migraciones SQL
- **Vistas con Tailwind CSS** - Integración con Tailwind CSS via CDN
- **Gestión de Configuración**
  - Soporte .env
  - Variables de entorno
  - Carga automática de configuración
- **Sistema de Sesiones** - Gestión de sesiones PHP
- **Validación de Entrada** - Sanitización básica de datos
- **Documentación Completa** - README con ejemplos y guía de uso

### 🔒 Seguridad

- Prepared statements con PDO
- Validación de entrada HTML escapeada
- Soporte UTF-8 completo

### 🚀 Performance

- Conexiones persistentes a BD
- Índices optimizados en base de datos
- Búsqueda eficiente de archivos .env

### 📁 Estructura

- Carpeta `app/` para lógica de aplicación
- Carpeta `config/` para configuración
- Carpeta `Framework/` para núcleo
- Carpeta `public/` para archivos públicos
- CLI tool en `bin/miframework`

---

## [Próximas Versiones]

### Planeado para v1.1.0

- [ ] Sistema de autenticación integrado
- [ ] Middleware system
- [ ] Validación de formularios mejorada
- [ ] API REST automática

### Planeado para v2.0.0

- [ ] Sistema de permisos (ACL)
- [ ] Generador de CRUD
- [ ] Cache system
- [ ] Testing framework
- [ ] Docker support
- [ ] GraphQL support

---

## Notas de Lanzamiento

### v1.0.0 - Primera Versión

La primera versión estable de Mi Framework incluye todas las características básicas necesarias para crear aplicaciones web modernas con PHP. El framework ha sido probado en Windows y es totalmente funcional con PHP 8.0+.

**Características Clave:**
✅ MVC completo
✅ CLI tool funcional
✅ BD con MySQL
✅ Vistas con Tailwind CSS
✅ Seguridad básica
✅ Documentación completa

---

## Cómo Contribuir

Consulta [CONTRIBUTING.md](CONTRIBUTING.md) para información sobre cómo contribuir al proyecto.

---

## Soporte

- 📧 **Email:** javierborjasamaniego@gmail.com
- 🐛 **Issues:** [Reportar Bug](https://github.com/Javierborja09/olyxis/issues)
- 💬 **Discussions:** [Comunidad](https://github.com/tu-Javierborja09/olyxis/discussions)

---

**Mi Framework** - Desarrollado con ❤️ por Javier Jeanpool Borja Samaniego
