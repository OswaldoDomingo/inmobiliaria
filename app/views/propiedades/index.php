<?php
declare(strict_types=1);

function e(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
function getv(array|object $row, string $key, mixed $default = null): mixed {
    return is_array($row) ? ($row[$key] ?? $default) : ($row->$key ?? $default);
}

$data    = $result['data'] ?? [];
$total   = (int)($result['total'] ?? 0);
$page    = (int)($result['page'] ?? 1);
$perPage = (int)($result['perPage'] ?? 10);
$pages   = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

// Use normalized filters from controller (not raw $_GET)
$localidad = isset($filtersNormalized['localidad']) ? (string)$filtersNormalized['localidad'] : '';
$tipo      = isset($filtersNormalized['tipo']) ? (string)$filtersNormalized['tipo'] : '';
$operacion = isset($filtersNormalized['operacion']) ? (string)$filtersNormalized['operacion'] : '';
$precioMin = isset($filtersNormalized['precio_min']) ? $filtersNormalized['precio_min'] : null;
$precioMax = isset($filtersNormalized['precio_max']) ? $filtersNormalized['precio_max'] : null;
$m2Min     = isset($filtersNormalized['m2_min']) ? $filtersNormalized['m2_min'] : null;
?>
<div class="container py-5">
    <h1 class="mb-4">Nuestras Propiedades</h1>

    <!-- Filtros de búsqueda -->
    <form method="get" action="/propiedades" class="row g-3 mb-4 p-4 bg-light rounded shadow-sm">
        <div class="col-md-3">
            <label for="localidad" class="form-label small">Localidad</label>
            <input type="text" name="localidad" id="localidad" class="form-control" placeholder="Ej: Madrid" value="<?= e($localidad) ?>">
        </div>
        <div class="col-md-3">
            <label for="tipo" class="form-label small">Tipo de inmueble</label>
            <select name="tipo" id="tipo" class="form-select">
                <option value="">Todos los tipos</option>
                <?php foreach (['piso','casa','chalet','adosado','duplex','local','oficina','terreno','otros'] as $t): ?>
                    <option value="<?= $t ?>" <?= $tipo === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="operacion" class="form-label small">Operación</label>
            <select name="operacion" id="operacion" class="form-select">
                <option value="">Todas</option>
                <?php foreach (['venta','alquiler','vacacional'] as $op): ?>
                    <option value="<?= $op ?>" <?= $operacion === $op ? 'selected' : '' ?>><?= ucfirst($op) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label for="m2_min" class="form-label small">Mín. m²</label>
            <input type="number" name="m2_min" id="m2_min" class="form-control" placeholder="Ej: 80" min="0" step="1" value="<?= $m2Min !== null ? e((string)$m2Min) : '' ?>">
        </div>
        <div class="col-md-2">
            <label for="precio_min" class="form-label small">Precio mín. (€)</label>
            <input type="number" name="precio_min" id="precio_min" class="form-control" placeholder="Ej: 100000" min="0" step="1" value="<?= $precioMin !== null ? e((string)$precioMin) : '' ?>">
        </div>
        <div class="col-md-2">
            <label for="precio_max" class="form-label small">Precio máx. (€)</label>
            <input type="number" name="precio_max" id="precio_max" class="form-control" placeholder="Ej: 250000" min="0" step="1" value="<?= $precioMax !== null ? e((string)$precioMax) : '' ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-dark w-100">
                <i class="bi bi-search"></i> Buscar
            </button>
        </div>
    </form>

    <?php if (!$data): ?>
        <div class="alert alert-info text-center" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            No hay propiedades disponibles que coincidan con tu búsqueda.
        </div>
    <?php else: ?>
        <p class="text-muted mb-4">
            <?= $total ?> propiedad<?= $total !== 1 ? 'es' : '' ?> encontrada<?= $total !== 1 ? 's' : '' ?>
        </p>

        <!-- Listado de propiedades -->
        <div class="row g-4">
            <?php foreach ($data as $row): ?>
                <?php
                    $id    = (int)getv($row, 'id_inmueble', 0);
                    $ref   = (string)getv($row, 'ref', '');
                    $dir   = (string)getv($row, 'direccion', '');
                    $loc   = (string)getv($row, 'localidad', '');
                    $prov  = (string)getv($row, 'provincia', '');
                    $tip   = (string)getv($row, 'tipo', '');
                    $op    = (string)getv($row, 'operacion', '');
                    $pre   = (float)getv($row, 'precio', 0);
                    $sup   = (int)getv($row, 'superficie', 0);
                    $hab   = (int)getv($row, 'habitaciones', 0);
                    $ban   = (int)getv($row, 'banos', 0);
                    $desc  = (string)getv($row, 'descripcion', '');
                    $img   = (string)getv($row, 'imagen', '');
                    
                    $titulo = ucfirst($tip) . ' en ' . ucfirst($op);
                    $ubicacion = $loc . ($prov ? ', ' . $prov : '');
                    
                    // Imagen
                    $imagenSrc = $img 
                        ? '/uploads/inmuebles/' . e($img) 
                        : '/assets/img/placeholder-property.png';
                    
                    // Descripción breve (máx 120 caracteres)
                    $descBreve = $desc ? (mb_strlen($desc) > 120 ? mb_substr($desc, 0, 120) . '...' : $desc) : $ubicacion;
                ?>
                <div class="col-12">
                    <div class="card shadow-sm h-100 hover-shadow">
                        <div class="row g-0">
                            <!-- Imagen -->
                            <div class="col-md-4">
                                <a href="/propiedades/ver?id=<?= $id ?>">
                                    <img src="<?= $imagenSrc ?>" 
                                         class="img-fluid rounded-start h-100 w-100" 
                                         style="object-fit: cover; min-height: 250px; max-height: 300px;"
                                         alt="<?= e($titulo) ?>"
                                         onerror="this.src='/assets/img/placeholder-property.png'">
                                </a>
                            </div>
                            
                            <!-- Información -->
                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column h-100">
                                    <!-- Título y ubicación -->
                                    <div class="mb-3">
                                        <h5 class="card-title mb-2">
                                            <a href="/propiedades/ver?id=<?= $id ?>" class="text-decoration-none text-dark">
                                                <?= e($titulo) ?>
                                            </a>
                                        </h5>
                                        <p class="text-muted mb-0">
                                            <i class="bi bi-geo-alt-fill text-primary"></i>
                                            <?= e($ubicacion) ?>
                                        </p>
                                        <?php if ($ref): ?>
                                            <small class="text-muted">Ref: <?= e($ref) ?></small>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Precio destacado -->
                                    <h4 class="text-primary fw-bold mb-3">
                                        <?= number_format($pre, 0, ',', '.') ?> €
                                    </h4>

                                    <!-- Descripción breve -->
                                    <p class="card-text text-muted small mb-3">
                                        <?= e($descBreve) ?>
                                    </p>

                                    <!-- Características -->
                                    <div class="d-flex gap-4 mb-3 text-muted small">
                                        <?php if ($sup > 0): ?>
                                            <span>
                                                <i class="bi bi-rulers"></i>
                                                <?= $sup ?> m²
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($hab > 0): ?>
                                            <span>
                                                <i class="bi bi-door-closed"></i>
                                                <?= $hab ?> hab.
                                            </span>
                                        <?php endif; ?>
                                        <?php if ($ban > 0): ?>
                                            <span>
                                                <i class="bi bi-droplet"></i>
                                                <?= $ban ?> baño<?= $ban > 1 ? 's' : '' ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Botones -->
                                    <div class="mt-auto d-flex gap-2">
                                        <a href="/propiedades/ver?id=<?= $id ?>" class="btn btn-outline-dark flex-grow-1">
                                            <i class="bi bi-info-circle"></i> Más información
                                        </a>
                                        <a href="/tasacion" class="btn btn-dark">
                                            <i class="bi bi-envelope"></i> Contactar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Paginación -->
        <?php if ($pages > 1): ?>
            <nav class="mt-5" aria-label="Paginación de propiedades">
                <ul class="pagination justify-content-center">
                    <?php 
                    $queryBase = $_GET; 
                    
                    // Botón anterior
                    if ($page > 1):
                        $queryBase['page'] = $page - 1;
                        $urlPrev = '/propiedades?' . http_build_query($queryBase);
                    ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= e($urlPrev) ?>" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">&laquo;</span>
                        </li>
                    <?php endif; ?>

                    <?php 
                    // Números de página
                    for ($p = 1; $p <= $pages; $p++): 
                        $queryBase['page'] = $p;
                        $url = '/propiedades?' . http_build_query($queryBase);
                    ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= e($url) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php 
                    // Botón siguiente
                    if ($page < $pages):
                        $queryBase['page'] = $page + 1;
                        $urlNext = '/propiedades?' . http_build_query($queryBase);
                    ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= e($urlNext) ?>" aria-label="Siguiente">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">&raquo;</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.hover-shadow {
    transition: box-shadow 0.3s ease-in-out;
}
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
