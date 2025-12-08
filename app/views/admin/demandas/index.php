<?php require VIEW . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Demandas</h1>
        <a href="/admin/demandas/nueva" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Demanda
        </a>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'created'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <strong>✓ Demanda creada correctamente</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['msg'] === 'updated'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <strong>✓ Demanda actualizada correctamente</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <strong>✓ Demanda eliminada correctamente</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error:</strong>
            <?php
                echo match($_GET['error']) {
                    'csrf' => 'Token de seguridad inválido',
                    'forbidden' => 'No tienes permiso para realizar esta acción',
                    'notfound' => 'Demanda no encontrada',
                    'db' => 'Error en la base de datos',
                    default => 'Ha ocurrido un error'
                };
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="/admin/demandas" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Tipo de Operación</label>
                    <select name="tipo_operacion" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php
                            $tipoActual = $_GET['tipo_operacion'] ?? '';
                            $tipos = ['compra' => 'Compra', 'alquiler' => 'Alquiler', 'vacacional' => 'Vacacional'];
                            foreach ($tipos as $val => $label):
                        ?>
                            <option value="<?= $val ?>" <?= $tipoActual === $val ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Estado</label>
                    <select name="estado" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php
                            $estadoActual = $_GET['estado'] ?? '';
                            $estados = [
                                'activa' => 'Activa',
                                'en_gestion' => 'En Gestión',
                                'pausada' => 'Pausada',
                                'archivada' => 'Archivada'
                            ];
                            foreach ($estados as $val => $label):
                        ?>
                            <option value="<?= $val ?>" <?= $estadoActual === $val ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if (!empty($comerciales)): ?>
                    <div class="col-md-3">
                        <label class="form-label small">Comercial</label>
                        <select name="comercial_id" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <?php
                                $comercialActual = $_GET['comercial_id'] ?? '';
                                foreach ($comerciales as $com):
                            ?>
                                <option value="<?= (int)$com['id_usuario'] ?>" <?= (int)$comercialActual === (int)$com['id_usuario'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($com['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="col-md-<?= !empty($comerciales) ? '3' : '6' ?> d-flex align-items-end">
                    <button type="submit" class="btn btn-sm btn-primary me-2">Filtrar</button>
                    <a href="/admin/demandas" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de demandas -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <?php if (empty($result['data'])): ?>
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-search" style="font-size: 3rem;"></i>
                    <p class="mt-3">No se encontraron demandas</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Comercial</th>
                                <th>Tipo</th>
                                <th>Precio</th>
                                <th>Superficie</th>
                                <th>Hab./Baños</th>
                                <th>Estado</th>
                                <th>Fecha Alta</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result['data'] as $dem): ?>
                                <tr>
                                    <td><small class="text-muted">#<?= (int)$dem['id_demanda'] ?></small></td>
                                    <td>
                                        <strong><?= htmlspecialchars($dem['cliente_nombre'] . ' ' . $dem['cliente_apellidos']) ?></strong>
                                    </td>
                                    <td>
                                        <small><?= htmlspecialchars($dem['comercial_nombre'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= match($dem['tipo_operacion']) {
                                            'compra' => 'primary',
                                            'alquiler' => 'info',
                                            'vacacional' => 'warning',
                                            default => 'secondary'
                                        } ?>">
                                            <?= htmlspecialchars(ucfirst($dem['tipo_operacion'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <?php if ($dem['rango_precio_min'] || $dem['rango_precio_max']): ?>
                                                <?= $dem['rango_precio_min'] ? number_format((float)$dem['rango_precio_min'], 0, ',', '.') : '0' ?> - 
                                                <?= $dem['rango_precio_max'] ? number_format((float)$dem['rango_precio_max'], 0, ',', '.') : '∞' ?> €
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small><?= $dem['superficie_min'] ? $dem['superficie_min'] . ' m²' : '-' ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <?= $dem['habitaciones_min'] ?? '-' ?> / <?= $dem['banos_min'] ?? '-' ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= match($dem['estado']) {
                                            'activa' => 'success',
                                            'en_gestion' => 'primary',
                                            'pausada' => 'warning',
                                            'archivada' => 'secondary',
                                            default => 'secondary'
                                        } ?>">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $dem['estado']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y', strtotime($dem['fecha_alta'])) ?></small>
                                    </td>
                                    <td class="text-end">
                                        <a href="/admin/demandas/editar?id=<?= (int)$dem['id_demanda'] ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Editar">
                                            <i class="bi bi-pencil"></i> Editar</a>
                                        

                                        <form action="/admin/demandas/borrar" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar esta demanda?')">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                            <input type="hidden" name="id" value="<?= (int)$dem['id_demanda'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i> Borrar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if ($result['totalPages'] > 1): ?>
                    <div class="card-footer">
                        <nav>
                            <ul class="pagination pagination-sm mb-0 justify-content-center">
                                <?php for ($i = 1; $i <= $result['totalPages']; $i++): ?>
                                    <li class="page-item <?= $i === $result['page'] ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=<?= $i ?><?= !empty($_GET['tipo_operacion']) ? '&tipo_operacion=' . urlencode($_GET['tipo_operacion']) : '' ?><?= !empty($_GET['estado']) ? '&estado=' . urlencode($_GET['estado']) : '' ?><?= !empty($_GET['comercial_id']) ? '&comercial_id=' . urlencode($_GET['comercial_id']) : '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                        <p class="text-muted text-center small mb-0 mt-2">
                            Mostrando <?= count($result['data']) ?> de <?= $result['total'] ?> demandas
                        </p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>


