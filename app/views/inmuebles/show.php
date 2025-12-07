<?php
declare(strict_types=1);

function e(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
function getv(array|object $row, string $key, mixed $default = null): mixed {
    return is_array($row) ? ($row[$key] ?? $default) : ($row->$key ?? $default);
}

// Datos
$ref       = (string)getv($inmueble, 'ref', '');
$localidad = (string)getv($inmueble, 'localidad', '');
$provincia = (string)getv($inmueble, 'provincia', '');
$cp        = (string)getv($inmueble, 'cp', '');
$direccion = (string)getv($inmueble, 'direccion', '');

$tipo      = (string)getv($inmueble, 'tipo', '');
$operacion = (string)getv($inmueble, 'operacion', '');
$precio    = (float)getv($inmueble, 'precio', 0);

$titulo    = ucfirst($tipo) . ' en ' . ucfirst($operacion) . ' - ' . $localidad;
?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item"><a href="/inmuebles">Inmuebles</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= e($ref) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <img src="/assets/img/placeholedr.jpg" class="img-fluid rounded mb-4 w-100" alt="<?= e($titulo) ?>">
            
            <h1 class="mb-2"><?= e($titulo) ?></h1>
            <p class="text-muted h5 mb-4">
                <i class="bi bi-geo-alt"></i> <?= e($localidad) ?> (<?= e($provincia) ?>)
            </p>

            <div class="mb-4">
                <h5>Detalles</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Ref:</strong> <?= e($ref) ?></li>
                    <li class="list-group-item"><strong>Tipo:</strong> <?= ucfirst($tipo) ?></li>
                    <li class="list-group-item"><strong>Operación:</strong> <?= ucfirst($operacion) ?></li>
                    <?php if ($direccion): ?>
                        <li class="list-group-item"><strong>Dirección:</strong> <?= e($direccion) ?></li>
                    <?php endif; ?>
                    <?php if ($cp): ?>
                        <li class="list-group-item"><strong>CP:</strong> <?= e($cp) ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm sticky-top" style="top: 2rem;">
                <div class="card-body">
                    <h3 class="card-title text-primary mb-3">
                        <?= number_format($precio, 2, ',', '.') ?> €
                    </h3>
                    <div class="d-grid gap-2">
                        <a href="/tasacion" class="btn btn-dark">Contactar</a>
                        <a href="tel:+34900000000" class="btn btn-outline-dark">Llamar</a>
                    </div>
                    <hr>
                    <p class="small text-muted mb-0">
                        Referencia: <?= e($ref) ?><br>
                        Gestionado por Inmobiliaria
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
