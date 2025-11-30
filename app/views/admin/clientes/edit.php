<?php require VIEW . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="/admin/clientes" class="btn btn-outline-secondary me-3">&larr; Volver</a>
                <h1 class="h3 mb-0">Editar Cliente #<?= (int)$cliente->id_cliente ?></h1>
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
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>
