<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inmobiliaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/landing.css">
    <!-- Estilos adicionales (opcional) -->
    <?php if (isset($extraCss)) echo $extraCss; ?>
</head>
<body>

    <!-- 1. Navbar + Hero -->
    <header class="mb-4">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="/">
                    <img src="/assets/img/logo.png" alt="Logo" height="38" class="me-2">
                    Inmobiliaria
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#mainNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="/">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="/propiedades">Propiedades</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="/admin/clientes">Clientes</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="#">Vende</a></li>
                        <li class="nav-item"><a class="nav-link fw-bold text-primary" href="/tasacion">Tasador Online</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
                        <li class="nav-item ms-2">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="d-flex align-items-center">
                                    <?php 
                                    $foto = $_SESSION['user_foto'] ?? null;
                                    $imgSrc = $foto ? "/uploads/profiles/" . htmlspecialchars($foto) : "/assets/img/default-user.png";
                                    // Fallback si no existe la imagen por defecto, usar un placeholder o icono
                                    // Pero el usuario pidió "assets/img/default-user.png" o icono bootstrap.
                                    // Usaré un icono de bootstrap si no hay foto para asegurar que se vea algo.
                                    ?>
                                    <?php if ($foto): ?>
                                        <img src="<?= $imgSrc ?>" alt="Perfil" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-none d-lg-block me-3">
                                        <small class="d-block lh-1 fw-bold"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') ?></small>
                                        <small class="d-block lh-1 text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></small>
                                    </div>

                                    <a class="btn btn-outline-primary btn-sm" href="/dashboard">Mi Panel</a>
                                </div>
                            <?php else: ?>
                                <a class="nav-link text-secondary" href="/login"><small>Acceso Profesionales</small></a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Hero (Solo si estamos en home, o lo hacemos condicional) -->
        <?php if (isset($showHero) && $showHero): ?>
        <div class="hero-landing d-flex align-items-center">
            <div class="container text-center text-white">
                <h1 class="display-4 fw-bold mb-0">Inmobiliaria</h1>
            </div>
        </div>
        <?php endif; ?>
    </header>
    
    <main>
