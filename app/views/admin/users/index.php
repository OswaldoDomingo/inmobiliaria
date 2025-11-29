<?php require VIEW . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion de Usuarios</h1>
        <a href="/admin/usuarios/nuevo" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nuevo Usuario
        </a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'created'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Usuario creado correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Avatar</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No hay usuarios registrados.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="ps-4">#<?= $user->id_usuario ?></td>
                                    <td>
                                        <?php if (!empty($user->foto_perfil)): ?>
                                            <img src="/uploads/profiles/<?= htmlspecialchars($user->foto_perfil) ?>" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-medium"><?= htmlspecialchars($user->nombre) ?></td>
                                    <td><?= htmlspecialchars($user->email) ?></td>
                                    <td>
                                        <?php
                                        $badges = [
                                            'admin' => 'bg-primary',
                                            'coordinador' => 'bg-success',
                                            'comercial' => 'bg-info text-dark'
                                        ];
                                        $badgeClass = $badges[$user->rol] ?? 'bg-secondary';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= ucfirst($user->rol) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($user->activo): ?>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <!-- Boton Bloquear/Desbloquear -->
                                            <?php if ($user->id_usuario != $_SESSION['user_id']): ?>
                                                <form action="/admin/usuarios/cambiar-bloqueo" method="POST" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                                    <input type="hidden" name="id" value="<?= $user->id_usuario ?>">
                                                    <?php if ((int)$user->cuenta_bloqueada === 1): ?>
                                                        <input type="hidden" name="status" value="0">
                                                        <button type="submit" class="btn btn-sm btn-success" title="Desbloquear Cuenta">
                                                            <i class="bi bi-unlock"></i> Desbloquear
                                                        </button>
                                                    <?php else: ?>
                                                        <input type="hidden" name="status" value="1">
                                                        <button type="submit" class="btn btn-sm btn-warning" title="Bloquear Cuenta" onclick="return confirm('Estas seguro de bloquear a este usuario?');">
                                                            <i class="bi bi-lock"></i> Bloquear
                                                        </button>
                                                    <?php endif; ?>
                                                </form>
                                            <?php endif; ?>

                                            <a href="/admin/usuarios/editar?id=<?= $user->id_usuario ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            
                                            <?php if ($user->id_usuario != 1 && $user->id_usuario != $_SESSION['user_id']): ?>

                                            <?php if ($user->activo): ?>
                                                <form action="/admin/usuarios/baja" method="POST" onsubmit="return confirm('Estas seguro de desactivar este usuario?');" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                                    <input type="hidden" name="id" value="<?= $user->id_usuario ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Dar de Baja">
                                                        <i class="bi bi-person-x"></i> Baja
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled>Inactivo</button>
                                            <?php endif; ?>

                                        <?php endif; ?>
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