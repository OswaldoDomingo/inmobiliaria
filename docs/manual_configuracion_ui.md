# Manual de Configuracion de Interfaz (Hero y Popups)

## 1. Banner Principal (Hero Section)

**Descripcion:** Bloque destacado a pantalla ancha (500px de alto) con imagen de fondo, overlay oscuro y contenido centrado para captar la atencion del usuario y ofrecer un buscador rapido.

**Como activar/desactivar:** La variable `$showHero` se define en `app/Controllers/HomeController.php` dentro de `index()`. Si esta en `true`, `home.php` incluye `app/Views/partials/hero.php`; si esta en `false`, el banner no se renderiza.

**Personalizacion:** Desde el controlador se ajustan:
- `$heroTitle`: titulo principal.
- `$heroSubTitle`: texto secundario.
- `$heroImage`: URL de la imagen (se usa Lorem Picsum para generar una foto aleatoria en cada recarga).

**Codigo explicado (controlador):**

```php
public function index(): void
{
    $showHero = true; // Activa/desactiva el hero en la vista principal
    $heroTitle = "Encuentra tu hogar ideal"; // Titular destacado
    $heroSubTitle = "Miles de propiedades te esperan en Valencia"; // Subtitulo descriptivo
    $heroImage = "https://picsum.photos/1920/600"; // Imagen dinamica de Lorem Picsum

    $mostrar_tarjeta = false; // Flag para popup estacional
    if (!isset($_SESSION['tarjeta_vista'])) { // Si es la primera visita de sesion
        $_SESSION['tarjeta_vista'] = true; // Marcamos la sesion para no repetir el popup
    } else { // Si ya se ha visto
        $mostrar_tarjeta = false; // Mantenemos oculto el popup
    }

    require VIEW . '/layouts/header.php'; // Carga cabecera comun
    require VIEW . '/home.php'; // Renderiza la vista principal con hero/popup segun flags
    require VIEW . '/layouts/footer.php'; // Cierra con el pie comun
}
```

## 2. Tarjeta Estacional (Overlay)

**Descripcion:** Overlay de bienvenida (Navidad) a pantalla completa con fondo animado y boton para cerrar antes de entrar al sitio.

**Logica de Sesion (IMPORTANTE):** Se usa `$_SESSION['tarjeta_vista']` como sello para no mostrar el popup en cada recarga. Al detectar que la sesion no lo ha visto, se marca la variable y se puede habilitar `$mostrar_tarjeta` para la primera visita; en visitas posteriores, el popup permanece oculto. Ejemplo:

```php
$mostrar_tarjeta = true; // Solo si queremos mostrarlo en la primera visita
if (!isset($_SESSION['tarjeta_vista'])) {
    $_SESSION['tarjeta_vista'] = true; // Sello para evitar rebote en recargas
} else {
    $mostrar_tarjeta = false; // Oculta en visitas siguientes
}
```

**Ubicacion de archivos:**
- HTML/CSS/JS del overlay: `app/Views/temporada/tarjeta_navidad.php` (incluido a traves de `app/Views/temporada/plantilla.php`).
- Vista parcial del hero: `app/Views/partials/hero.php`.
- Logica de control de visibilidad: `app/Controllers/HomeController.php` (las vistas no deciden, solo renderizan segun las banderas recibidas).
