<?php
// Verificación de seguridad: Si no hay sesión, redirigir al login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

require VIEW . '/layouts/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Panel de Control</h1>
                <a href="/logout" class="btn btn-outline-danger">Cerrar Sesión</a>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <?php
                    $rol = $_SESSION['user_role'] ?? 'usuario';
                    $nombre = $_SESSION['user_name'] ?? 'Usuario';
                    
                    // Mensaje personalizado según el rol
                    switch ($rol) {
                        case 'admin':
                            echo "<h3 class='card-title text-primary'>Hola Admin $nombre</h3>";
                            echo "<p class='card-text'>Tienes acceso total al sistema. Puedes gestionar usuarios y propiedades.</p>";
                            echo '<div class="mt-3">';
                            echo '<a href="/admin/usuarios" class="btn btn-primary"><i class="bi bi-people"></i> Gestionar Usuarios</a>';
                            echo '</div>';
                            break;
                        case 'coordinador':
                            echo "<h3 class='card-title text-success'>Hola Coordinador $nombre</h3>";
                            echo "<p class='card-text'>Puedes ver todos los registros y supervisar la actividad.</p>";
                            break;
                        case 'comercial':
                            echo "<h3 class='card-title text-info'>Hola Comercial $nombre</h3>";
                            echo "<p class='card-text'>Aquí puedes gestionar tus propiedades y clientes asignados.</p>";
                            break;
                        default:
                            echo "<h3 class='card-title'>Bienvenido $nombre</h3>";
                            break;
                    }
                    ?>
                    
                    <hr>
                    
                    <div class="mt-4">
                        <h4>Tus Datos</h4>
                        <div class="d-flex align-items-center mb-3">
                            <?php 
                            $foto = $_SESSION['user_foto'] ?? null;
                            $imgSrc = $foto ? "/uploads/profiles/" . htmlspecialchars($foto) : "";
                            ?>
                            <?php if ($foto): ?>
                                <img src="<?= $imgSrc ?>" alt="Perfil" class="rounded-circle me-3" style="width: 64px; height: 64px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width: 64px; height: 64px; font-size: 1.5rem;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <p class="mb-1"><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user_email'] ?? 'No disponible') ?></p>
                                <p class="mb-0"><strong>Rol:</strong> <?= ucfirst($rol) ?></p>
                            </div>
                        </div>
                        <ul>
                            <li><strong>ID:</strong> <?= $_SESSION['user_id'] ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require VIEW . '/layouts/footer.php'; ?>
