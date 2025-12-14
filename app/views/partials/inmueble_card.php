<?php
/**
 * Partial: Card de Inmueble
 * Recibe una variable $inmueble (array) con los datos del inmueble
 * Muestra una tarjeta compacta compatible con carrusel
 */

// Defensive: convertir objeto a array si es necesario
if (is_object($inmueble)) {
    $inmueble = (array) $inmueble;
}

// Helper para escapar HTML
if (!function_exists('e')) {
    function e(mixed $v): string {
        return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }
}

// Extraer datos del inmueble
$id = (int)($inmueble['id_inmueble'] ?? 0);
$ref = (string)($inmueble['ref'] ?? '');
$tipo = (string)($inmueble['tipo'] ?? '');
$operacion = (string)($inmueble['operacion'] ?? '');
$localidad = (string)($inmueble['localidad'] ?? '');
$provincia = (string)($inmueble['provincia'] ?? '');
$precio = (float)($inmueble['precio'] ?? 0);
$habitaciones = (int)($inmueble['habitaciones'] ?? 0);
$banos = (int)($inmueble['banos'] ?? 0);
$superficie = (int)($inmueble['superficie'] ?? 0);
$imagen = (string)($inmueble['imagen'] ?? '');

// Construir título y ubicación
$titulo = ucfirst($tipo) . ' en ' . ucfirst($operacion);
$ubicacion = $localidad . ($provincia ? ', ' . $provincia : '');

// Ruta de la imagen (reutilizando convención del listado público)
$imagenSrc = $imagen 
    ? '/uploads/inmuebles/' . e($imagen)
    : '/assets/img/placeholder-property.png';
?>

<article class="carousel-card card h-100 shadow-sm">
    <a href="/propiedades/ver?id=<?= $id ?>" class="text-decoration-none">
        <img src="<?= $imagenSrc ?>" 
             class="card-img-top carousel-card-img" 
             alt="<?= e($titulo) ?>"
             onerror="this.src='/assets/img/placeholder-property.png'">
    </a>
    <div class="card-body d-flex flex-column">
        <!-- Título -->
        <h3 class="h6 card-title mb-1">
            <a href="/propiedades/ver?id=<?= $id ?>" class="text-dark text-decoration-none">
                <?= e($titulo) ?>
            </a>
        </h3>
        
        <!-- Ubicación -->
        <p class="text-muted small mb-2">
            <i class="bi bi-geo-alt-fill"></i>
            <?= e($ubicacion) ?>
        </p>
        
        <!-- Precio -->
        <p class="text-primary fw-bold mb-3">
            <?= number_format($precio, 0, ',', '.') ?> €
        </p>
        
        <!-- Características -->
        <div class="d-flex gap-3 mb-3 text-muted small">
            <?php if ($superficie > 0): ?>
                <span><i class="bi bi-rulers"></i> <?= $superficie ?> m²</span>
            <?php endif; ?>
            <?php if ($habitaciones > 0): ?>
                <span><i class="bi bi-door-closed"></i> <?= $habitaciones ?> hab.</span>
            <?php endif; ?>
            <?php if ($banos > 0): ?>
                <span><i class="bi bi-droplet"></i> <?= $banos ?></span>
            <?php endif; ?>
        </div>
        
        <!-- Botón -->
        <a href="/propiedades/ver?id=<?= $id ?>" class="btn btn-outline-dark btn-sm mt-auto w-100">
            Ver detalle
        </a>
    </div>
</article>
