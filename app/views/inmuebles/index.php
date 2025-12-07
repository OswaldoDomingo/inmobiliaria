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
$perPage = (int)($result['perPage'] ?? 12);
$pages   = $perPage > 0 ? (int)ceil($total / $perPage) : 1;

$localidad = (string)($_GET['localidad'] ?? '');
$tipo      = (string)($_GET['tipo'] ?? '');
$operacion = (string)($_GET['operacion'] ?? '');
?>
<div class="container py-4">
    <h1 class="mb-4">Inmuebles</h1>

    <form method="get" action="/inmuebles" class="row g-3 mb-4 p-3 bg-light rounded">
        <div class="col-md-3">
            <input type="text" name="localidad" class="form-control" placeholder="Localidad" value="<?= e($localidad) ?>">
        </div>
        <div class="col-md-3">
            <select name="tipo" class="form-select">
                <option value="">Tipo...</option>
                <?php foreach (['piso','casa','local','terreno','nave','otro'] as $t): ?>
                    <option value="<?= $t ?>" <?= $tipo === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="operacion" class="form-select">
                <option value="">Operación...</option>
                <?php foreach (['venta','alquiler','vacacional'] as $op): ?>
                    <option value="<?= $op ?>" <?= $operacion === $op ? 'selected' : '' ?>><?= ucfirst($op) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-dark w-100">Buscar</button>
        </div>
    </form>

    <?php if (!$data): ?>
        <p class="text-center text-muted">No hay inmuebles publicados que coincidan con tu búsqueda.</p>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($data as $row): ?>
                <?php
                    $ref   = (string)getv($row, 'ref', '');
                    $loc   = (string)getv($row, 'localidad', '');
                    $prov  = (string)getv($row, 'provincia', '');
                    $tip   = (string)getv($row, 'tipo', '');
                    $op    = (string)getv($row, 'operacion', '');
                    $pre   = (float)getv($row, 'precio', 0);
                    
                    $titulo = ucfirst($tip) . ' en ' . ucfirst($op);
                ?>
                <div class="col">
                    <div class="card h-100">
                        <img src="/assets/img/placeholedr.jpg" class="card-img-top" alt="Inmueble">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="/inmuebles/ver?ref=<?= e($ref) ?>" class="text-decoration-none text-dark">
                                    <?= e($titulo) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted mb-2">
                                <i class="bi bi-geo-alt"></i> <?= e($loc) ?>
                            </p>
                            <h6 class="text-primary fw-bold mb-3">
                                <?= number_format($pre, 2, ',', '.') ?> €
                            </h6>
                            <a href="/inmuebles/ver?ref=<?= e($ref) ?>" class="btn btn-outline-dark btn-sm w-100">Ver detalle</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($pages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php $queryBase = $_GET; ?>
                    <?php for ($p = 1; $p <= $pages; $p++): ?>
                        <?php $queryBase['page'] = $p; ?>
                        <?php $url = '/inmuebles?' . http_build_query($queryBase); ?>
                        <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= e($url) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
