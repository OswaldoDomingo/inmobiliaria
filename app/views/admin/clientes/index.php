<?php require VIEW . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Clientes</h1>
        <a href="/admin/clientes/nuevo" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuevo Cliente
        </a>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <?php if ($_GET['msg'] === 'created'): ?>
            <div class="alert alert-success">Cliente creado correctamente.</div>
        <?php elseif ($_GET['msg'] === 'updated'): ?>
            <div class="alert alert-success">Cliente actualizado correctamente.</div>
        <?php elseif ($_GET['msg'] === 'deleted'): ?>
            <div class="alert alert-success">Cliente eliminado correctamente.</div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <?php if ($_GET['error'] === 'has_properties'): ?>
            <div class="alert alert-danger">No puedes eliminar un cliente con inmuebles asociados.</div>
        <?php elseif ($_GET['error'] === 'csrf'): ?>
            <div class="alert alert-danger">Sesión expirada. Inténtalo de nuevo.</div>
        <?php elseif ($_GET['error'] === 'notfound'): ?>
            <div class="alert alert-warning">Cliente no encontrado.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Nombre</th>
                            <th>DNI</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'coordinador'], true)): ?>
                                <th>Asignado a</th>
                            <?php endif; ?>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clientes)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay clientes.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td class="ps-4">#<?= $cliente->id_cliente ?></td>
                                    <td><?= htmlspecialchars($cliente->nombre . ' ' . $cliente->apellidos) ?></td>
                                    <td><?= htmlspecialchars($cliente->dni ?? '-') ?></td>
                                    <td><?= htmlspecialchars($cliente->telefono ?? '-') ?></td>
                                    <td><?= htmlspecialchars($cliente->email ?? '-') ?></td>
                                    <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'coordinador'], true)): ?>
                                        <td><?= htmlspecialchars($cliente->comercial_nombre ?? 'Sin asignar') ?></td>
                                    <?php endif; ?>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="/admin/clientes/editar?id=<?= $cliente->id_cliente ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <form action="/admin/clientes/borrar" method="POST" onsubmit="return confirm('¿Seguro que deseas borrar este cliente?');" style="display:inline;">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                                <input type="hidden" name="id" value="<?= $cliente->id_cliente ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Borrar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>
