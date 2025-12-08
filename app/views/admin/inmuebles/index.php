<?php
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$data = $result['data'] ?? [];
$total = (int)($result['total'] ?? 0);
$page = (int)($result['page'] ?? 1);
$perPage = (int)($result['perPage'] ?? 15);
$totalPages = ceil($total / $perPage);

require VIEW . '/layouts/header.php';
?>

<div class="container py-5">
    <!-- Encabezado y Acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Inmuebles</h1>
            <p class="text-muted small mb-0">Gestión de propiedades</p>
        </div>
        <div class="btn-group">
            <a href="/admin/dashboard" class="btn btn-outline-secondary">
                <i class="bi bi-speedometer2"></i> Mi Panel
            </a>
            <a href="/admin/inmuebles/nuevo" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Inmueble
            </a>
        </div>
    </div>

    <!-- Mensajes de estado -->
    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'created'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <strong>✓ Inmueble creado correctamente</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['msg'] === 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <strong>✓ Inmueble actualizado correctamente</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <strong>✓ Inmueble eliminado correctamente</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body bg-light rounded">
            <form method="get" action="/admin/inmuebles" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Referencia</label>
                    <input type="text" name="ref" class="form-control form-control-sm" placeholder="Ej. REF-001" value="<?= e($_GET['ref'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Tipo</label>
                    <select name="tipo" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php foreach (['piso','casa','chalet','local','oficina'] as $t): ?>
                            <option value="<?= $t ?>" <?= ($_GET['tipo']??'')===$t ? 'selected':'' ?>><?= ucfirst($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Operación</label>
                    <select name="operacion" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="venta" <?= ($_GET['operacion']??'')==='venta'?'selected':'' ?>>Venta</option>
                        <option value="alquiler" <?= ($_GET['operacion']??'')==='alquiler'?'selected':'' ?>>Alquiler</option>
                         <option value="vacacional" <?= ($_GET['operacion']??'')==='vacacional'?'selected':'' ?>>Vacacional</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Localidad</label>
                    <input type="text" name="localidad" class="form-control form-control-sm" placeholder="Ej. Madrid" value="<?= e($_GET['localidad'] ?? '') ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i> Filtrar</button>
                    <a href="/admin/inmuebles" class="btn btn-sm btn-outline-secondary w-100">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <?php if (empty($data)): ?>
                <div class="text-center p-5 text-muted">
                    <i class="bi bi-house-x display-4"></i>
                    <p class="mt-2">No se encontraron inmuebles.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="80">Imagen</th>
                                <th>Ref</th>
                                <th>Tipo / Op.</th>
                                <th>Precio</th>
                                <th>Ubicación</th>
                                <th>Estado</th>
                                <th>Gestión</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data as $row): 
                            $isArr = is_array($row);
                            $id = $isArr ? ($row['id_inmueble']??0) : ($row->id_inmueble??0);
                            $ref = $isArr ? ($row['ref']??'') : ($row->ref??'');
                            $tipo = $isArr ? ($row['tipo']??'') : ($row->tipo??'');
                            $operacion = $isArr ? ($row['operacion']??'') : ($row->operacion??'');
                            $precio = $isArr ? ($row['precio']??0) : ($row->precio??0);
                            $localidad = $isArr ? ($row['localidad']??'') : ($row->localidad??'');
                            $provincia = $isArr ? ($row['provincia']??'') : ($row->provincia??''); // Assuming provincia exists or just fallback
                            $estado = $isArr ? ($row['estado']??'borrador') : ($row->estado??'borrador');
                            $imagen = $isArr ? ($row['imagen'] ?? null) : ($row->imagen ?? null);
                            
                            $propNombre = is_array($row) ? ($row['propietario_nombre']??'') : ($row->propietario_nombre??'');
                            $propApell = is_array($row) ? ($row['propietario_apellidos']??'') : ($row->propietario_apellidos??'');
                            $prop = trim("$propNombre $propApell");
                            
                            $com = $isArr ? ($row['comercial_nombre'] ?? '-') : ($row->comercial_nombre ?? '-');
                            
                            // Badges
                            $badgeOp = match($operacion){
                                'venta'=>'bg-success',
                                'alquiler'=>'bg-info text-dark',
                                'vacacional'=>'bg-warning text-dark',
                                default=>'bg-secondary'
                            };
                             $badgeSt = match($estado){
                                'activo'=>'bg-success',
                                'reservado'=>'bg-warning text-dark',
                                'vendido'=>'bg-danger',
                                'retirado'=>'bg-dark',
                                default=>'bg-secondary'
                            };
                        ?>
                            <tr>
                                <td>
                                    <?php if ($imagen): ?>
                                      <img src="/uploads/inmuebles/<?= e($imagen) ?>" 
                                           alt="Inmueble" 
                                           class="rounded shadow-sm"
                                           style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                      <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px;">
                                          <i class="bi bi-image"></i>
                                      </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= e($ref) ?></strong></td>
                                <td>
                                    <div class="mb-1"><?= ucfirst(e($tipo)) ?></div>
                                    <span class="badge <?= $badgeOp ?>"><?= ucfirst(e($operacion)) ?></span>
                                </td>
                                <td class="fw-bold text-primary">
                                    <?= number_format((float)$precio, 0, ',', '.') ?> €
                                </td>
                                <td>
                                    <?= e($localidad) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $badgeSt ?>"><?= ucfirst(e($estado)) ?></span>
                                </td>
                                <td>
                                    <div class="small">
                                        <div class="text-truncate" style="max-width: 150px;" title="Propietario: <?= e($prop) ?>">
                                            <i class="bi bi-person"></i> <?= e($prop) ?>
                                        </div>
                                        <div class="text-truncate text-muted" style="max-width: 150px;" title="Comercial: <?= e($com) ?>">
                                            <i class="bi bi-briefcase"></i> <?= e($com) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="/admin/inmuebles/editar?id=<?= (int)$id ?>" class="btn btn-sm btn-outline-primary mb-1">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    
                                    <form method="post" action="/admin/inmuebles/borrar" class="d-inline" onsubmit="return confirm('¿Confirma que desea eliminar este inmueble?');">
                                        <!-- Assuming csrfToken is available globally or passed to view -->
                                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken ?? '') ?>">
                                        <input type="hidden" name="id" value="<?= (int)$id ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger mb-1">
                                            <i class="bi bi-trash"></i> Borrar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Pagination -->
        <?php if($totalPages > 1): ?>
        <div class="card-footer bg-white border-top-0 d-flex justify-content-center py-3">
             <nav>
                <ul class="pagination pagination-sm m-0">
                    <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <li class="page-item <?= ($i==$page)?'active':'' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&ref=<?=e($_GET['ref']??'')?>&tipo=<?=e($_GET['tipo']??'')?>&localidad=<?=e($_GET['localidad']??'')?>&operacion=<?=e($_GET['operacion']??'')?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="text-end text-muted small mt-2">
        Total registros: <?= $total ?>
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>
