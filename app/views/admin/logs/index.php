<?php
// Verificar sesión (aunque el controlador ya lo hace, es buena práctica en vistas admin)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require VIEW . '/layouts/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Auditoría de Seguridad</h1>
        <a href="/dashboard" class="btn btn-secondary">Volver al Dashboard</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Registro de Eventos (Logs)</h5>
            
            <?php if (empty($logs)): ?>
                <div class="alert alert-info">
                    No hay registros de auditoría disponibles aún.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Evento</th>
                                <th>Usuario / Email</th>
                                <th>Dirección IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= htmlspecialchars($log['fecha']) ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = 'bg-secondary';
                                        switch ($log['evento']) {
                                            case 'LOGIN_EXITOSO':
                                                $badgeClass = 'bg-success';
                                                break;
                                            case 'LOGIN_FALLIDO':
                                                $badgeClass = 'bg-danger';
                                                break;
                                            case 'BLOQUEO_CUENTA':
                                                $badgeClass = 'bg-danger text-uppercase fw-bold';
                                                break;
                                            case 'LOGOUT':
                                                $badgeClass = 'bg-info text-dark';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($log['evento']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($log['usuario']) ?></td>
                                    <td><code><?= htmlspecialchars($log['ip']) ?></code></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>
