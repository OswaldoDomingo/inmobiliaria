<?php require VIEW . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="d-flex align-items-center mb-4">
                <a href="/admin/usuarios" class="btn btn-outline-secondary me-3">&larr; Volver</a>
                <h1 class="h3 mb-0">Nuevo Usuario</h1>
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

                    <form action="/admin/usuarios/guardar" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($nombre ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($email ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   minlength="6" required>
                            <div class="form-text">Mínimo 6 caracteres.</div>
                        </div>

                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="" disabled <?= empty($rol) ? 'selected' : '' ?>>Selecciona un rol</option>
                                <option value="admin" <?= (isset($rol) && $rol === 'admin') ? 'selected' : '' ?>>Administrador</option>
                                <option value="coordinador" <?= (isset($rol) && $rol === 'coordinador') ? 'selected' : '' ?>>Coordinador</option>
                                <option value="comercial" <?= (isset($rol) && $rol === 'comercial') ? 'selected' : '' ?>>Comercial</option>
                            </select>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>
