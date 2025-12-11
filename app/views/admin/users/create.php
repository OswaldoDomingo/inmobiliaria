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

                    <form action="/admin/usuarios/guardar" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        
                        <div class="mb-3">
                            <label for="foto_perfil" class="form-label">Foto de Perfil</label>
                            <input type="file" class="form-control" id="foto_perfil" name="foto_perfil" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">Formatos: JPG, PNG, WEBP. Máx: 2MB.</div>
                        </div>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?= htmlspecialchars($nombre ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electronico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($email ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?= htmlspecialchars($telefono ?? '') ?>" 
                                   placeholder="+34 XXX XXX XXX">
                            <div class="form-text">Opcional. Ejemplo: +34 644 403 640</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contrasena <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   minlength="6" required>
                            <div class="form-text">Minimo 6 caracteres.</div>
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