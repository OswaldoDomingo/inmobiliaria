<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    
                    <h2 class="card-title text-center mb-4">Contacta con nosotros</h2>
                    
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger mb-4">
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Información del Inmueble (si aplica) -->
                    <?php if (isset($inmueble) && is_array($inmueble)): ?>
                        <div class="alert alert-light border border-primary mb-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 text-primary">
                                    <i class="bi bi-house-door-fill fs-2"></i>
                                </div>
                                <div class="ms-3">
                                    <h5 class="alert-heading mb-1 text-primary">Consulta sobre: <?= htmlspecialchars($inmueble['ref'] ?? '') ?></h5>
                                    <p class="mb-0 text-muted">
                                        <?= htmlspecialchars(ucfirst($inmueble['tipo'] ?? '')) ?> en <?= htmlspecialchars($inmueble['localidad'] ?? '') ?> 
                                        <?php if (!empty($inmueble['precio'])): ?>
                                            - <strong><?= number_format((float)$inmueble['precio'], 0, ',', '.') ?> €</strong>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="/contacto/enviar" method="POST" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        
                        <?php if (isset($inmueble['id_inmueble'])): ?>
                            <input type="hidden" name="id_inmueble" value="<?= htmlspecialchars($inmueble['id_inmueble']) ?>">
                        <?php endif; ?>
                        
                        <?php 
                        // Mantener motivo con preferencia: $old['motivo'] > $_GET['motivo']
                        // Normalizar siempre para evitar fallos si viene con mayúsculas/espacios
                        $motivoRaw = (string)($old['motivo'] ?? ($_GET['motivo'] ?? ''));
                        $motivoValue = strtolower(trim($motivoRaw));
                        $motivosValidos = ['info', 'compra', 'venta', 'alquiler'];
                        if (in_array($motivoValue, $motivosValidos, true)): 
                        ?>
                            <input type="hidden" name="motivo" value="<?= htmlspecialchars($motivoValue, ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>

                        <!-- Honeypot -->
                        <div style="display:none;">
                            <label for="website">Website</label>
                            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-12">
                                <label for="nombre" class="form-label">Nombre completo <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" 
                                       required 
                                       minlength="3" 
                                       maxlength="100">
                                <?php if (isset($errors['nombre'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['nombre']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                <input type="tel" 
                                       class="form-control <?= isset($errors['telefono']) ? 'is-invalid' : '' ?>" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="<?= htmlspecialchars($old['telefono'] ?? '') ?>" 
                                       pattern="^[0-9+\s\-]{6,20}$" 
                                       title="Introduce un teléfono válido (solo números, espacios, + y -)."
                                       required>
                                <?php if (isset($errors['telefono'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['telefono']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                       id="email" 
                                       name="email" 
                                       value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                                       required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Mensaje -->
                            <div class="col-12">
                                <label for="mensaje" class="form-label">¿En qué podemos ayudarte?</label>
                                <textarea class="form-control <?= isset($errors['mensaje']) ? 'is-invalid' : '' ?>" 
                                          id="mensaje" 
                                          name="mensaje" 
                                          rows="4" 
                                          maxlength="1000"><?= htmlspecialchars($old['mensaje'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                <?php if (isset($errors['mensaje'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['mensaje']) ?></div>
                                <?php endif; ?>
                                <div class="form-text">Máximo 1000 caracteres.</div>
                            </div>

                            <!-- Política de Privacidad -->
                            <div class="col-12 mt-3">
                                <div class="form-check">
                                    <input class="form-check-input <?= isset($errors['politica_privacidad']) ? 'is-invalid' : '' ?>" 
                                           type="checkbox" 
                                           name="politica_privacidad" 
                                           value="1" 
                                           id="privacyCheck" 
                                           required 
                                           <?= !empty($old['politica_privacidad']) ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="privacyCheck">
                                        He leído y acepto la <a href="/legal/privacidad" target="_blank" class="text-decoration-none">política de privacidad</a>.
                                    </label>
                                    <?php if (isset($errors['politica_privacidad'])): ?>
                                        <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['politica_privacidad']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Botón Enviar -->
                            <div class="col-12 mt-4 text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-send me-2"></i> Enviar Consulta
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Datos de contacto directos -->
            <div class="text-center mt-5 text-muted">
                <p class="mb-1">También puedes contactarnos directamente:</p>
                <p class="mb-0">
                    <i class="bi bi-telephone me-1"></i> <a href="tel:+34900000000" class="text-decoration-none text-muted">+34 900 000 000</a> 
                    <span class="mx-2">|</span> 
                    <i class="bi bi-envelope me-1"></i> <a href="mailto:info@inmobiliaria.example.com" class="text-decoration-none text-muted">info@inmobiliaria.example.com</a>
                </p>
            </div>

        </div>
    </div>
</div>
