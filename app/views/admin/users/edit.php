<?php require VIEW . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="d-flex align-items-center mb-4">
                <a href="/admin/usuarios" class="btn btn-outline-secondary me-3">&larr; Volver</a>
                <h1 class="h3 mb-0">Editar Usuario #<?= $id_usuario ?></h1>
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

                    <form action="/admin/usuarios/actualizar" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        <input type="hidden" name="id" value="<?= $id_usuario ?>">

                        <div class="mb-3">
                            <label for="foto_perfil" class="form-label">Foto de Perfil</label>
                            <?php if (!empty($user->foto_perfil)): ?>
                                <div class="mb-2">
                                    <img src="/uploads/profiles/<?= htmlspecialchars($user->foto_perfil) ?>" alt="Perfil" class="img-thumbnail" style="max-width: 100px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="foto_perfil" name="foto_perfil" accept="image/jpeg,image/png,image/webp">
                            <div class="form-text">Deja en blanco para mantener la actual. Máx: 2MB.</div>
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
                            <label for="password" class="form-label">Contrasena</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   minlength="6" placeholder="Dejar en blanco para mantener la actual">
                            <div class="form-text">Solo rellena esto si quieres cambiar la contrasena.</div>
                        </div>

                        <div class="mb-3">
                            <label for="rol" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="admin" <?= (isset($rol) && $rol === 'admin') ? 'selected' : '' ?>>Administrador</option>
                                <option value="coordinador" <?= (isset($rol) && $rol === 'coordinador') ? 'selected' : '' ?>>Coordinador</option>
                                <option value="comercial" <?= (isset($rol) && $rol === 'comercial') ? 'selected' : '' ?>>Comercial</option>
                            </select>
                        </div>

                        <hr class="my-4">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>