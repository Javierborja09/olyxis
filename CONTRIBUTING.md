# Guía de Contribución

¡Gracias por tu interés en contribuir a Mi Framework! 🙌

## Cómo Contribuir

### Reportar Bugs

Antes de crear un reporte de bug, verifica que el problema no haya sido reportado antes. Si encuentras un bug:

1. **Usa un título descriptivo** para el issue
2. **Describe los pasos exactos** para reproducir el problema
3. **Proporciona ejemplos específicos** para demostrar los pasos
4. **Describe el comportamiento observado**
5. **Explica qué comportamiento esperabas** y por qué
6. **Incluye capturas de pantalla** si es posible

### Sugerir Mejoras

Las sugerencias de mejoras son bienvenidas. Para sugerir una mejora:

1. Usa un título descriptivo
2. Proporciona una descripción detallada
3. Proporciona ejemplos específicos para demostrar los pasos
4. Describe el comportamiento actual y el esperado

### Pull Requests

1. **Fork** el repositorio
2. **Crea una rama** para tu feature (`git checkout -b feature/AmazingFeature`)
3. **Commit tus cambios** (`git commit -m 'Add some AmazingFeature'`)
4. **Push** a la rama (`git push origin feature/AmazingFeature`)
5. **Abre un Pull Request**

#### Guía de Estilo del PR

- Escribe un título descriptivo
- Incluye una descripción clara de qué cambia y por qué
- Referencia al issue relacionado si existe
- Prueba tu código antes de enviar

## Estándares de Código

### PHP

```php
<?php
namespace App\Controllers;

// Usar namespace y use statements
use Framework\Core\Controller;

class MyController extends Controller {
    // Usar camelCase para métodos
    public function myMethod($request) {
        // Código bien indentado
        if ($request->isPost()) {
            return $this->view('my-view', [
                'data' => $data
            ]);
        }
    }
}
```

### Convenciones

- ✅ **Clases:** PascalCase (`MyController`)
- ✅ **Métodos:** camelCase (`myMethod`)
- ✅ **Propiedades:** camelCase (`$myProperty`)
- ✅ **Constantes:** UPPER_SNAKE_CASE (`MY_CONSTANT`)
- ✅ **Archivos:** PascalCase para clases

### Documentación

Documenta tu código con comentarios:

```php
/**
 * Descripción del método
 * 
 * @param string $name Nombre del usuario
 * @return string Saludo personalizado
 */
public function greet(string $name): string {
    return "Hola, {$name}!";
}
```

## Proceso de Revisión

1. Un mantenedor revisará tu PR
2. Se pueden solicitar cambios
3. Una vez aprobado, se fusionará con la rama main

## Preguntas

¿Tienes preguntas? Abre una **Discussion** en el repositorio.

---

**¡Gracias por contribuir a Mi Framework! ⭐**
