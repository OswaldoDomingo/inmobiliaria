<?php
// Helpers para la vista
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$mode = isset($inmueble) ? 'edit' : 'create';
$inmueble = $inmueble ?? null;
$action = $mode === 'edit' ? '/admin/inmuebles/actualizar' : '/admin/inmuebles/guardar';

$old = $old ?? [];
$errors = $errors ?? [];

// Helper para obtener valor viejo o actual
$val = function(string $k, $default='') use ($old, $inmueble, $mode) {
  if (array_key_exists($k, $old)) return $old[$k];
  if ($mode === 'edit') return is_array($inmueble) ? ($inmueble[$k] ?? $default) : ($inmueble->$k ?? $default);
  return $default;
};

// Helpers para clases de error
$err = fn(string $k) => $errors[$k] ?? null;
$isInvalid = fn(string $k) => $err($k) ? 'is-invalid' : '';

$chk = fn(string $k) => (int)$val($k, 0) === 1 ? 'checked' : '';

// ID del inmueble (solo edit)
$id = 0;
if ($mode === 'edit' && $inmueble) {
    $id = is_array($inmueble) ? (int)$inmueble['id_inmueble'] : (int)$inmueble->id_inmueble;
}

// Título dinámico
$title = $mode === 'edit' ? 'Editar Inmueble' : 'Nuevo Inmueble';
if ($mode === 'create' && !empty($propietarioPre)) {
    $nombreProp = is_object($propietarioPre) 
        ? ($propietarioPre->nombre . ' ' . $propietarioPre->apellidos)
        : ($propietarioPre['nombre'] . ' ' . $propietarioPre['apellidos']);
    $idProp = is_object($propietarioPre) ? $propietarioPre->id_cliente : $propietarioPre['id_cliente'];
    $title = "Nuevo inmueble para " . e($nombreProp);
}

require VIEW . '/layouts/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            
            <!-- Encabezado -->
            <div class="d-flex align-items-center mb-4">
                <a href="/admin/inmuebles" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <h1 class="h3 mb-0"><?= e($title) ?></h1>
            </div>

            <!-- Alertas de Error Global -->
            <?php if (!empty($errors) && empty(array_filter(array_keys($errors), fn($k) => is_string($k)))): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $msg): ?>
                            <li><?= e($msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Tarjeta del Formulario -->
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="post" action="<?= e($action) ?>" class="row g-3">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <?php if ($mode === 'edit'): ?>
                            <input type="hidden" name="id" value="<?= $id ?>">
                        <?php endif; ?>

                        <!-- Sección: Propietario y Comercial -->
                        <div class="col-md-6">
                            <label class="form-label">Propietario *</label>
                            <select name="propietario_id" class="form-select <?= $isInvalid('propietario_id') ?>" required>
                                <option value="">-- Seleccionar Propietario --</option>
                                <?php foreach (($propietarios ?? []) as $p): 
                                    $pid = is_array($p) ? $p['id_cliente'] : $p->id_cliente;
                                    $name = is_array($p) ? ($p['nombre'].' '.$p['apellidos']) : ($p->nombre.' '.$p->apellidos);
                                ?>
                                  <option value="<?= (int)$pid ?>" <?= (int)$val('propietario_id',0)===(int)$pid?'selected':'' ?>>
                                    <?= e($name) ?>
                                  </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($err('propietario_id')): ?>
                                <div class="invalid-feedback"><?= e($err('propietario_id')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Comercial</label>
                             <?php
                                $rolSesion = $_SESSION['user_role'] ?? 'comercial';
                                $esAdminOCoord = in_array($rolSesion, ['admin', 'coordinador'], true);
                                $idUser = (int)($_SESSION['user_id'] ?? 0);
                            ?>

                            <?php if ($esAdminOCoord): ?>
                                <select name="comercial_id" class="form-select">
                                    <option value="">-- Automático (Yo) --</option>
                                    <?php foreach (($comerciales ?? []) as $c): ?>
                                    <option value="<?= (int)$c['id_usuario'] ?>" <?= (int)$val('comercial_id',0)===(int)$c['id_usuario']?'selected':'' ?>>
                                        <?= e($c['nombre']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                             <?php else: ?>
                                <!-- Si es comercial, se asigna automáticamente él mismo, input oculto o disabled visualmente -->
                                <input type="hidden" name="comercial_id" value="<?= $idUser ?>">
                                <input type="text" class="form-control" value="<?= e($_SESSION['user_name'] ?? 'Yo') ?>" disabled readonly>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-12"><hr class="my-2"></div>

                        <!-- Sección: Datos Básicos -->
                        <div class="col-md-4">
                            <label class="form-label">Referencia (Ref) *</label>
                            <input type="text" name="ref" class="form-control <?= $isInvalid('ref') ?>" value="<?= e($val('ref')) ?>" required maxlength="30">
                            <?php if ($err('ref')): ?>
                                <div class="invalid-feedback"><?= e($err('ref')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipo *</label>
                            <select name="tipo" class="form-select <?= $isInvalid('tipo') ?>" required>
                                <?php foreach (['piso','casa','chalet','adosado','duplex','local','oficina','terreno','otros'] as $t): ?>
                                  <option value="<?= e($t) ?>" <?= $val('tipo','piso')===$t?'selected':'' ?>><?= e(ucfirst($t)) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($err('tipo')): ?>
                                <div class="invalid-feedback"><?= e($err('tipo')) ?></div>
                            <?php endif; ?>
                        </div>

                         <div class="col-md-4">
                            <label class="form-label">Operación *</label>
                            <select name="operacion" class="form-select <?= $isInvalid('operacion') ?>" required>
                                <?php foreach (['venta','alquiler','vacacional'] as $op): ?>
                                  <option value="<?= e($op) ?>" <?= $val('operacion','venta')===$op?'selected':'' ?>><?= e(ucfirst($op)) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($err('operacion')): ?>
                                <div class="invalid-feedback"><?= e($err('operacion')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Precio (€) *</label>
                            <input type="text" name="precio" class="form-control <?= $isInvalid('precio') ?>" value="<?= e($val('precio')) ?>" required>
                            <?php if ($err('precio')): ?>
                                <div class="invalid-feedback"><?= e($err('precio')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select <?= $isInvalid('estado') ?>" required>
                                <?php foreach (['borrador','activo','reservado','vendido','retirado'] as $s): ?>
                                  <option value="<?= e($s) ?>" <?= $val('estado','borrador')===$s?'selected':'' ?>><?= e(ucfirst($s)) ?></option>
                                <?php endforeach; ?>
                            </select>
                             <?php if ($err('estado')): ?>
                                <div class="invalid-feedback"><?= e($err('estado')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12"><hr class="my-2"></div>

                        <!-- Sección: Ubicación -->
                        <div class="col-md-6">
                            <label class="form-label">Dirección *</label>
                            <input type="text" name="direccion" class="form-control <?= $isInvalid('direccion') ?>" value="<?= e($val('direccion')) ?>" required>
                             <?php if ($err('direccion')): ?>
                                <div class="invalid-feedback"><?= e($err('direccion')) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Localidad *</label>
                            <input type="text" name="localidad" class="form-control <?= $isInvalid('localidad') ?>" value="<?= e($val('localidad')) ?>" required>
                             <?php if ($err('localidad')): ?>
                                <div class="invalid-feedback"><?= e($err('localidad')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Provincia *</label>
                            <input type="text" name="provincia" class="form-control <?= $isInvalid('provincia') ?>" value="<?= e($val('provincia')) ?>" required>
                             <?php if ($err('provincia')): ?>
                                <div class="invalid-feedback"><?= e($err('provincia')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Código Postal</label>
                            <input type="text" name="cp" class="form-control <?= $isInvalid('cp') ?>" value="<?= e($val('cp')) ?>" maxlength="10">
                             <?php if ($err('cp')): ?>
                                <div class="invalid-feedback"><?= e($err('cp')) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12"><hr class="my-2"></div>

                        <!-- Sección: Detalles -->
                        <div class="col-md-4">
                            <label class="form-label">Superficie (m²)</label>
                            <input type="number" name="superficie" class="form-control" value="<?= e($val('superficie')) ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Habitaciones</label>
                            <input type="number" name="habitaciones" class="form-control" value="<?= e($val('habitaciones')) ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Baños</label>
                            <input type="number" name="banos" class="form-control" value="<?= e($val('banos')) ?>">
                        </div>

                         <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="4"><?= e($val('descripcion')) ?></textarea>
                        </div>

                        <div class="col-12 mt-4">
                             <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="activo" id="checkActivo" value="1" <?= $mode=='create' || $chk('activo') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="checkActivo">Activo (Visible en web)</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="archivado" id="checkArchivado" value="1" <?= $chk('archivado') ?>>
                                <label class="form-check-label" for="checkArchivado">Archivado</label>
                            </div>
                        </div>

                        <div class="col-12 mt-4 d-grid gap-2 d-md-flex justify-content-md-end">
                             <a href="/admin/inmuebles" class="btn btn-outline-secondary me-md-2">Cancelar</a>
                             <button type="submit" class="btn btn-primary px-4">Guardar Inmueble</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>
