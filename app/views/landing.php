<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>
<link rel="stylesheet" href="/assets/css/landing.css">

</head>
<body>

    <!-- 1. Navbar + Hero -->
<header class="mb-4">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <a class="navbar-brand d-flex align-items-center" href="#">
    <img src="/assets/img/logo.png" alt="Logo" height="38" class="me-2">
</a>
                Inmobiliaria
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Propiedades</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Vende</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Equipo</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero con imagen de fondo + texto centrado -->
    <div class="hero-landing d-flex align-items-center">
        <div class="container text-center text-white">
            <h1 class="display-4 fw-bold mb-0">Inmobiliaria</h1>
        </div>
    </div>
</header>


    <!-- 2. Propiedades destacadas -->
<section id="propiedades" class="py-5">
    <div class="container">
        <h2 class="text-center mb-4 section-title">
            PROPIEDADES DESTACADAS
        </h2>

        <div class="row g-4">
            <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="col-12 col-md-6 col-lg-3">
                    <article class="card propiedad-card h-100">
                        <img src="/assets/img/placeholedr.jpg"
                             class="card-img-top" alt="Propiedad ejemplo">
                        <div class="card-body">
                            <h3 class="h6 card-title mb-1">Chalet en Valencia</h3>
                            <p class="card-text small mb-2">Zona Montcada · 120 m² · 3 hab</p>
                            <p class="fw-bold mb-3">320.000 €</p>
                            <a href="#" class="btn btn-dark w-100 btn-sm">
                                Ver detalle
                            </a>
                        </div>
                    </article>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

        <!-- 3. Bloque valoración -->
<section id="valoracion" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4 section-title">
            CONFÍA TU PROPIEDAD A EXPERTOS
        </h2>

        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="valoracion-box p-4">
                    <form class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">Dirección del inmueble</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Comentarios</label>
                            <textarea class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-dark px-5">
                                Enviar solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</section>

    </main>

    <!-- 4. Footer -->
<footer class="py-4 border-top bg-white">
    <div class="container">
        <div class="row gy-3">
            <div class="col-12 col-md-4">
                <ul class="list-unstyled small mb-0">
                    <li>INICIO</li>
                    <li>CONTACTO</li>
                    <li>QUIÉNES SOMOS</li>
                    <li>TRABAJA CON NOSOTROS</li>
                </ul>
            </div>
            <div class="col-12 col-md-4 text-md-center">
                <p class="small mb-1">
                    C/ Ejemplo 123, Valencia<br>
                    96 000 00 00 · contacto@inmobiliaria.es
                </p>
                <p class="small mb-0">© <?= date('Y') ?> Inmobiliaria · Política de privacidad · Términos de uso</p>
            </div>
            <div class="col-12 col-md-4 text-md-end">
                <ul class="list-unstyled small mb-0">
                    <li>LINKEDIN</li>
                    <li>INSTAGRAM</li>
                    <li>YOUTUBE</li>
                    <li>TIK TOK</li>
                </ul>
            </div>
        </div>
    </div>
</footer>


</body>
</html>