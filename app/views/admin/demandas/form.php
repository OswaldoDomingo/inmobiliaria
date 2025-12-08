<?php require VIEW . '/layouts/header.php'; ?>

<?php
$isEdit = isset($demanda) && isset($demanda->id_demanda);
$pageTitle = $isEdit ? 'Editar Demanda #' . $demanda->id_demanda : 'Nueva Demanda';
$actionUrl = $isEdit ? '/admin/demandas/actualizar' : '/admin/demandas/guardar';

// Preparar valores para el formulario
$clienteId = $old['cliente_id'] ?? ($cliente->id_cliente ?? 0);
$nombreCliente = '';
if ($cliente) {
    $nombreCliente = is_object($cliente) 
        ? ($cliente->nombre . ' ' . $cliente->apellidos) 
        : ($cliente['nombre'] . ' ' . $cliente['apellidos']);
}

$caracteristicasActuales = $old['caracteristicas'] ?? ($demanda->caracteristicas ?? []);
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <a href="<?= htmlspecialchars($returnTo ?: '/admin/demandas') ?>" class="btn btn-outline-secondary me-3">&larr; Volver</a>
                <h1 class="h3 mb-0"><?= htmlspecialchars($pageTitle) ?></h1>
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

                    <form action="<?= htmlspecialchars($actionUrl) ?>" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                        <input type="hidden" name="return_to" value="<?= htmlspecialchars($returnTo ?? '') ?>">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?= (int)$demanda->id_demanda ?>">
                        <?php endif; ?>

                        <div class="row g-3">
                            <!-- Cliente -->
                            <div class="col-md-12">
                                <label class="form-label">Cliente *</label>
                                <?php if ($clienteId > 0 && $nombreCliente): ?>
                                    <!-- Cliente prefijado (viene de ficha de cliente) -->
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($nombreCliente) ?>" readonly>
                                    <input type="hidden" name="cliente_id" value="<?= (int)$clienteId ?>">
                                <?php else: ?>
                                    <select name="cliente_id" class="form-select" required>
                                        <option value="">Selecciona un cliente</option>
                                        <?php foreach ($clientes ?? [] as $cli): ?>
                                            <?php 
                                                $cliId = is_object($cli) ? $cli->id_cliente : $cli['id_cliente'];
                                                $cliNombre = is_object($cli) ? $cli->nombre : $cli['nombre'];
                                                $cliApellidos = is_object($cli) ? $cli->apellidos : $cli['apellidos'];
                                                $selected = (int)$cliId === (int)$clienteId ? 'selected' : '';
                                            ?>
                                            <option value="<?= (int)$cliId ?>" <?= $selected ?>>
                                                <?= htmlspecialchars($cliNombre . ' ' . $cliApellidos) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>

                            <!-- Tipo de operación -->
                            <div class="col-md-6">
                                <label class="form-label">Tipo de Operación *</label>
                                <select name="tipo_operacion" class="form-select" required>
                                    <option value="">Selecciona...</option>
                                    <?php 
                                        $tipoActual = $old['tipo_operacion'] ?? ($demanda->tipo_operacion ?? '');
                                        $tipos = ['compra' => 'Compra', 'alquiler' => 'Alquiler', 'vacacional' => 'Vacacional'];
                                        foreach ($tipos as $val => $label): 
                                    ?>
                                        <option value="<?= $val ?>" <?= $tipoActual === $val ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select">
                                    <?php 
                                        $estadoActual = $old['estado'] ?? ($demanda->estado ?? 'activa');
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

                            <!-- Rango de Precio -->
                            <div class="col-md-6">
                                <label class="form-label">Precio Mínimo (€)</label>
                                <input type="number" name="rango_precio_min" class="form-control" 
                                       value="<?= htmlspecialchars($old['rango_precio_min'] ?? ($demanda->rango_precio_min ?? '')) ?>"
                                       step="1" min="0" placeholder="Ej: 100000">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Precio Máximo (€)</label>
                                <input type="number" name="rango_precio_max" class="form-control" 
                                       value="<?= htmlspecialchars($old['rango_precio_max'] ?? ($demanda->rango_precio_max ?? '')) ?>"
                                       step="1" min="0" placeholder="Ej: 250000">
                            </div>

                            <!-- Características del inmueble -->
                            <div class="col-md-4">
                                <label class="form-label">Superficie Mínima (m²)</label>
                                <input type="number" name="superficie_min" class="form-control" 
                                       value="<?= htmlspecialchars($old['superficie_min'] ?? ($demanda->superficie_min ?? '')) ?>"
                                       min="0" placeholder="Ej: 80">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Habitaciones Mínimas</label>
                                <input type="number" name="habitaciones_min" class="form-control" 
                                       value="<?= htmlspecialchars($old['habitaciones_min'] ?? ($demanda->habitaciones_min ?? '')) ?>"
                                       min="0" max="20" placeholder="Ej: 2">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Baños Mínimos</label>
                                <input type="number" name="banos_min" class="form-control" 
                                       value="<?= htmlspecialchars($old['banos_min'] ?? ($demanda->banos_min ?? '')) ?>"
                                       min="0" max="10" placeholder="Ej: 1">
                            </div>

                            <!-- Zonas -->
                            <div class="col-12">
                                <label class="form-label">Zonas de Interés</label>
                                <textarea name="zonas" rows="2" class="form-control" 
                                          placeholder="Ej: Valencia centro, Ruzafa, Campanar..."><?= htmlspecialchars($old['zonas'] ?? ($demanda->zonas ?? '')) ?></textarea>
                                <small class="text-muted">Zonas geográficas donde busca el cliente</small>
                            </div>

                            <!-- Características (checkboxes → JSON) -->
                            <div class="col-12">
                                <label class="form-label">Características Deseadas</label>
                                <div class="row g-2">
                                    <?php 
                                        $availableCaracteristicas = [
                                            'garaje' => 'Garaje',
                                            'piscina' => 'Piscina',
                                            'ascensor' => 'Ascensor',
                                            'terraza' => 'Terraza',
                                            'amueblado' => 'Amueblado',
                                            'trastero' => 'Trastero',
                                            'jardin' => 'Jardín'
                                        ];
                                        foreach ($availableCaracteristicas as $key => $label): 
                                            $checked = in_array($key, $caracteristicasActuales, true) ? 'checked' : '';
                                    ?>
                                        <div class="col-md-3 col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       name="caracteristica_<?= $key ?>" 
                                                       id="car_<?= $key ?>" <?= $checked ?>>
                                                <label class="form-check-label" for="car_<?= $key ?>">
                                                    <?= $label ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <?= $isEdit ? 'Actualizar Demanda' : 'Crear Demanda' ?>
                            </button>
                            <a href="<?= htmlspecialchars($returnTo ?: '/admin/demandas') ?>" class="btn btn-outline-secondary">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>

