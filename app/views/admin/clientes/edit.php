<?php require VIEW . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="/admin/clientes" class="btn btn-outline-secondary me-3">&larr; Volver</a>
                <h1 class="h3 mb-0">Editar Cliente #<?= (int)$cliente->id_cliente ?></h1>
            </div>

            <div class="mb-4">
                <?php 
                    $returnPath = '/admin/clientes/editar?id=' . (int)$cliente->id_cliente;
                    $nuevoInmuebleLink = '/admin/inmuebles/nuevo?propietario_id=' . (int)$cliente->id_cliente . '&return_to=' . urlencode($returnPath);
                ?>
                <a href="<?= htmlspecialchars($nuevoInmuebleLink) ?>" class="btn btn-outline-primary me-2">
                    <i class="bi bi-house-add"></i> ➕ Añadir inmueble
                </a>
                <a href="/admin/demandas/nuevo?cliente_id=<?= (int)$cliente->id_cliente ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-search"></i> ➕ Añadir demanda
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="/admin/clientes/actualizar" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                        <input type="hidden" name="id" value="<?= (int)$cliente->id_cliente ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre *</label>
                                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($cliente->nombre) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Apellidos *</label>
                                <input type="text" name="apellidos" class="form-control" value="<?= htmlspecialchars($cliente->apellidos) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">DNI</label>
                                <input type="text" name="dni" class="form-control" value="<?= htmlspecialchars($cliente->dni ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($cliente->telefono ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($cliente->email ?? '') ?>">
                            </div>
                            <?php $rolSesion = $_SESSION['user_role'] ?? ($_SESSION['rol'] ?? 'comercial'); ?>
                            <?php if (in_array($rolSesion, ['admin', 'coordinador'], true)): ?>
                                <div class="col-md-6">
                                    <label class="form-label">Comercial Asignado</label>
                                    <select name="usuario_id" class="form-select">
                                        <option value="">Selecciona un comercial</option>
                                        <?php foreach ($comerciales ?? [] as $comercial): ?>
                                            <?php $seleccionado = (int)($cliente->usuario_id ?? 0) === (int)$comercial->id_usuario; ?>
                                            <option value="<?= (int)$comercial->id_usuario ?>" <?= $seleccionado ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($comercial->nombre) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                            <div class="col-12">
                                <label class="form-label">Dirección</label>
                                <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($cliente->direccion ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notas</label>
                                <textarea name="notas" rows="3" class="form-control"><?= htmlspecialchars($cliente->notas ?? '') ?></textarea>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
                        </div>
                    </form>
                </div>
            </div>
            </div>

            <!-- Inmuebles del Cliente -->
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Inmuebles de este cliente</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($inmueblesCliente)): ?>
                        <p class="text-muted mb-0">Este cliente no tiene inmuebles asociados todavía.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref</th>
                                        <th>Dirección</th>
                                        <th>Localidad</th>
                                        <th>Operación</th>
                                        <th>Precio</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inmueblesCliente as $inm): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($inm['ref']) ?></td>
                                            <td><?= htmlspecialchars(mb_strimwidth($inm['direccion'] ?? '', 0, 30, '...')) ?></td>
                                            <td><?= htmlspecialchars($inm['localidad'] ?? '') ?></td>
                                            <td><?= htmlspecialchars(ucfirst($inm['operacion'])) ?></td>
                                            <td>
                                                <?= $inm['precio'] !== null 
                                                    ? number_format((float)$inm['precio'], 0, ',', '.') . ' €' 
                                                    : '-' ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= match($inm['estado']) {
                                                    'activo' => 'success',
                                                    'reservado' => 'warning',
                                                    'vendido', 'alquilado' => 'info',
                                                    'cancelado', 'no_exclusiva' => 'danger',
                                                    default => 'secondary'
                                                } ?>">
                                                    <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $inm['estado']))) ?>
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <?php 
                                                    $returnPath = '/admin/clientes/editar?id=' . (int)$cliente->id_cliente;
                                                    $editLink = '/admin/inmuebles/editar?id=' . $inm['id_inmueble'] . '&return_to=' . urlencode($returnPath);
                                                ?>
                                                <a href="<?= htmlspecialchars($editLink) ?>" class="btn btn-sm btn-outline-primary">
                                                    Editar
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>
