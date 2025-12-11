<?php
declare(strict_types=1);

function e(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
function getv(array|object $row, string $key, mixed $default = null): mixed {
    return is_array($row) ? ($row[$key] ?? $default) : ($row->$key ?? $default);
}

// Datos del inmueble
$id        = (int)getv($inmueble, 'id_inmueble', 0);
$ref       = (string)getv($inmueble, 'ref', '');
$direccion = (string)getv($inmueble, 'direccion', '');
$localidad = (string)getv($inmueble, 'localidad', '');
$provincia = (string)getv($inmueble, 'provincia', '');
$cp        = (string)getv($inmueble, 'cp', '');

$tipo      = (string)getv($inmueble, 'tipo', '');
$operacion = (string)getv($inmueble, 'operacion', '');
$precio    = (float)getv($inmueble, 'precio', 0);

$superficie   = (int)getv($inmueble, 'superficie', 0);
$habitaciones = (int)getv($inmueble, 'habitaciones', 0);
$banos        = (int)getv($inmueble, 'banos', 0);
$descripcion  = (string)getv($inmueble, 'descripcion', '');
$imagen       = (string)getv($inmueble, 'imagen', '');

$titulo    = ucfirst($tipo) . ' en ' . ucfirst($operacion);
$ubicacion = $direccion . ($localidad ? ', ' . $localidad : '') . ($provincia ? ' (' . $provincia . ')' : '');

// Imagen principal
$imagenSrc = $imagen 
    ? '/uploads/inmuebles/' . e($imagen) 
    : '/assets/img/placeholder-property.png';
?>

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/propiedades">Propiedades</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= e($ref ?: 'Detalle') ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Columna principal: imagen y detalles -->
        <div class="col-lg-8">
            <!-- Imagen principal -->
            <div class="mb-4">
                <img src="<?= $imagenSrc ?>" 
                     class="img-fluid rounded shadow w-100" 
                     style="max-height: 500px; object-fit: cover;"
                     alt="<?= e($titulo) ?>"
                     onerror="this.src='/assets/img/placeholder-property.png'">
            </div>

            <!-- Título y ubicación -->
            <h1 class="mb-2"><?= e($titulo) ?></h1>
            <p class="text-muted h5 mb-4">
                <i class="bi bi-geo-alt-fill text-primary"></i>
                <?= e($ubicacion) ?>
            </p>

            <!-- Descripción -->
            <?php if ($descripcion): ?>
                <div class="mb-4">
                    <h5 class="mb-3">Descripción</h5>
                    <p class="text-muted" style="white-space: pre-line;"><?= e($descripcion) ?></p>
                </div>
            <?php endif; ?>

            <!-- Características principales -->
            <div class="mb-4">
                <h5 class="mb-3">Características</h5>
                <div class="row g-3">
                    <?php if ($superficie > 0): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="bi bi-rulers fs-3 text-primary me-3"></i>
                                <div>
                                    <small class="d-block text-muted">Superficie</small>
                                    <strong><?= $superficie ?> m²</strong>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($habitaciones > 0): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="bi bi-door-closed fs-3 text-primary me-3"></i>
                                <div>
                                    <small class="d-block text-muted">Habitaciones</small>
                                    <strong><?= $habitaciones ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($banos > 0): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="bi bi-droplet fs-3 text-primary me-3"></i>
                                <div>
                                    <small class="d-block text-muted">Baños</small>
                                    <strong><?= $banos ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detalles adicionales -->
            <div class="mb-4">
                <h5 class="mb-3">Detalles</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Referencia:</strong>
                        <span><?= e($ref) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Tipo:</strong>
                        <span><?= e(ucfirst($tipo)) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <strong>Operación:</strong>
                        <span><?= e(ucfirst($operacion)) ?></span>
                    </li>
                    <?php if ($direccion): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Dirección:</strong>
                            <span><?= e($direccion) ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if ($localidad): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Localidad:</strong>
                            <span><?= e($localidad) ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if ($provincia): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Provincia:</strong>
                            <span><?= e($provincia) ?></span>
                        </li>
                    <?php endif; ?>
                    <?php if ($cp): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <strong>Código Postal:</strong>
                            <span><?= e($cp) ?></span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Botón volver -->
            <div class="mb-4">
                <a href="/propiedades" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div>

        <!-- Columna lateral: precio y contacto -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 2rem;">
                <div class="card-body">
                    <!-- Precio -->
                    <h3 class="card-title text-primary mb-4 text-center">
                        <?= number_format($precio, 0, ',', '.') ?> €
                    </h3>
                    
     <!-- Botones de contacto -->
                    <div class="d-grid gap-3 mb-4">
                        <a href="/contacto?id_inmueble=<?= $id ?>" class="btn btn-dark btn-lg">
                            <i class="bi bi-envelope"></i> Contactar
                        </a>
                        <a href="tel:+34900000000" class="btn btn-outline-dark">
                            <i class="bi bi-telephone"></i> Llamar
                        </a>
                        <a href="https://wa.me/34900000000" class="btn btn-outline-success" target="_blank" rel="noopener">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                    </div>

                    <hr>

                    <!-- Información adicional -->
                    <div class="small text-muted">
                        <p class="mb-2">
                            <i class="bi bi-info-circle"></i>
                            <strong>Referencia:</strong> <?= e($ref) ?>
                        </p>
                        <?php if (!empty($contacto)): ?>
                            <p class="mb-2">
                                <i class="bi bi-person-badge"></i>
                                <strong>Gestionado por:</strong> <?= e($contacto['nombre'] ?? 'No disponible') ?>
                            </p>
                            <?php if (!empty($contacto['email'])): ?>
                                <p class="mb-2">
                                    <i class="bi bi-envelope"></i>
                                    <strong>Correo contacto:</strong> 
                                    <a href="mailto:<?= e($contacto['email']) ?>" class="text-decoration-none">
                                        <?= e($contacto['email']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($contacto['telefono'])): ?>
                                <p class="mb-2">
                                    <i class="bi bi-telephone"></i>
                                    <strong>Teléfono:</strong> 
                                    <a href="tel:<?= e($contacto['telefono']) ?>" class="text-decoration-none">
                                        <?= e($contacto['telefono']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="mb-2">
                                <i class="bi bi-building"></i>
                                Gestionado por <strong>Inmobiliaria</strong>
                            </p>
                        <?php endif; ?>
                        <p class="mb-0">
                            <i class="bi bi-shield-check"></i>
                            Propiedad verificada
                        </p>
                    </div>
                </div>
            </div>

            <!-- Información de ayuda -->
            <div class="card mt-3 bg-light border-0">
                <div class="card-body">
                    <h6 class="card-title">¿Necesitas ayuda?</h6>
                    <p class="card-text small text-muted mb-3">
                        Nuestro equipo está disponible para resolver todas tus dudas sobre esta propiedad.
                    </p>
                    <a href="/contacto?id_inmueble=<?= $id ?>" class="btn btn-sm btn-outline-primary w-100">
                        Solicitar información
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons CDN (si no está ya incluido) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
