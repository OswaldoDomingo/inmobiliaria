# Documentación Técnica: Carrusel de Propiedades Destacadas

## 1. Objetivo y Alcance
Mejorar la experiencia de usuario (UX) en la página de inicio (Landing Page) mostrando una selección dinámica de inmuebles destacados. 

**Objetivo clave:** 
- Dar sensación de "sitio vivo" y con movimiento.
- Mostrar inmuebles reales de la cartera.
- **Restricción crítica:** No modificar el esquema de base de datos (sin columna `destacado` por ahora) para minimizar riesgos en fase de entrega final.

## 2. Reglas de Negocio

### 2.1. Criterio "Publicable"
Para que un inmueble aparezca en el carrusel (y en el portal público en general), debe cumplir una única condición simplificada:

*   **`activo = 1`**

> **Nota histórica:** Inicialmente se consideraban campos adicionales como `estado = 'activo'` y `archivado = 0`. Sin embargo, esto restringía excesivamente la muestra (dejando el carrusel vacío). Se decidió simplificar la lógica para que el check "Activo" del panel de administración sea el único interruptor de visibilidad pública.

### 2.2. Estrategia de Selección
Se seleccionan hasta **6 inmuebles** de forma aleatoria, pero con una particularidad:

*   **Aleatoriedad Estable por Día:**  
    `ORDER BY RAND(TO_DAYS(CURDATE()))`
    
    **¿Por qué?**
    Si usáramos `RAND()` puro, los inmuebles cambiarían cada vez que el usuario refresca la página (F5), lo que puede generar una sensación de sitio inestable o "roto". Con esta fórmula, el orden es aleatorio para cada día pero se mantiene constante durante las 24 horas, ofreciendo una experiencia más profesional y sólida.

## 3. Implementación Backend

### 3.1. Modelo (`app/Models/Inmueble.php`)
Se implementó el método estático `getHomeCarousel()`:

```php
public static function getHomeCarousel(int $limit = 6, bool $stableDaily = true): array
{
    // Clamp del límite por seguridad (mínimo 1, máximo 12)
    $limit = max(1, min(12, $limit));
    
    $sort = $stableDaily ? 'RAND(TO_DAYS(CURDATE()))' : 'RAND()';
    
    // SQL optimizado
    $sql = "SELECT ... FROM inmuebles WHERE activo = 1 ORDER BY {$sort} LIMIT {$limit}";
    
    // ... ejecución y retorno ...
}
```

**Punto crítico (Bug Fix):**
Se detectó que el uso de `fetchObject()` devolvía objetos `stdClass`, mientras que las vistas esperaban arrays asociativos. Esto causaba errores fatales.
**Solución:** Se forzó el uso de `$stmt->fetchAll(\PDO::FETCH_ASSOC)` para garantizar que siempre se trabaja con arrays, alineándose con el resto del proyecto.

### 3.2. Controlador (`HomeController`)
El controlador es ligero y solo se encarga de invocar al modelo y pasar los datos a la vista:
```php
$carouselInmuebles = \App\Models\Inmueble::getHomeCarousel(6, true);
```

## 4. Implementación Frontend

### 4.1. Estructura HTML
Se utiliza un esquema semántico:
- `.carousel-wrapper`: Contenedor relativo para posicionar botones.
- `.carousel-container`: El área de scroll (flexbox).
- `.carousel-item`: Cada tarjeta de propiedad.

### 4.2. CSS (Vanilla Layout)
Se optó por **NO usar librerías externas** (como Slick o Swiper) para mantener el rendimiento alto y la complejidad baja.

```css
.carousel-container {
    display: flex !important;
    overflow-x: auto;
    scroll-snap-type: x mandatory; /* Efecto magnético */
    
    /* Estrategia robusta anticaída */
    white-space: nowrap !important; 
}

.carousel-item {
    scroll-snap-align: start;
    flex: 0 0 auto;
    width: 280px; /* Ancho fijo para consistencia */
    display: inline-block !important; /* Fallback de seguridad */
}
```

### 4.3. JavaScript (Vanilla)
Script ligero integrado en `home.php` para:
- Desplazamiento suave (`scroll-behavior: smooth`) al clicar botones.
- Gestión de botones: se ocultan "Anterior" si estamos al inicio y "Siguiente" si estamos al final.
- Soporte nativo de `swipe` en móviles gracias a `overflow-x`.

## 5. Casos Borde y Comportamiento Esperado

| Escenario | Comportamiento |
|-----------|----------------|
| **0 Inmuebles activos** | Se muestra un mensaje amigable: "No hay propiedades destacadas disponibles". |
| **< 6 Inmuebles** | Se muestran todos los disponibles ocupando el ancho necesario (no se rompe el layout). |
| **Recarga de página** | Se mantienen los mismos inmuebles (por la estrategia stable random). |
| **Sin imagen** | Se muestra un placeholder (`/assets/img/placeholder-property.png`). |

## 6. Limitaciones y Mejoras Futuras
- **Selección Manual:** Actualmente no se puede elegir "qué" inmuebles salen específicamente; es aleatorio entre los activos.
  - *Mejora:* Crear columna `destacado` (boolean) en BD.
- **Ordenación:** No se puede definir el orden (siempre random diario).
  - *Mejora:* Crear columna `orden_destacado` (int).
- **Rendimiento:** `ORDER BY RAND()` puede ser lento con millones de registros. Para el volumen actual (<10.000) es despreciable.
