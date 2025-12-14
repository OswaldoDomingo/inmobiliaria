<?php if (isset($showHero) && $showHero === true): ?>
    <?php include __DIR__ . '/partials/hero.php'; ?>
<?php endif; ?>

<?php if (isset($mostrar_tarjeta) && $mostrar_tarjeta === true): ?>
    <?php include __DIR__ . '/temporada/plantilla.php'; ?>
<?php endif; ?>

<!-- 2. Propiedades destacadas - Carrusel -->
<section id="propiedades" class="py-5">
    <div class="container">
        <h2 class="text-center mb-4 section-title">
            PROPIEDADES DESTACADAS
        </h2>

        <?php if (!empty($carouselInmuebles)): ?>
            <div class="carousel-wrapper position-relative">
                <!-- Botón Anterior -->
                <button id="carousel-prev" class="carousel-nav carousel-nav-prev" aria-label="Anterior">
                    <i class="bi bi-arrow-left"></i>
                </button>

                <!-- Contenedor del carrusel -->
                <div id="carousel-container" class="carousel-container">
                    <?php foreach ($carouselInmuebles as $inmueble): ?>
                        <div class="carousel-item">
                            <?php require __DIR__ . '/partials/inmueble_card.php'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Botón Siguiente -->
                <button id="carousel-next" class="carousel-nav carousel-nav-next" aria-label="Siguiente">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                No hay propiedades destacadas disponibles en este momento.
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Estilos movidos a /assets/css/landing.css -->

<!-- Script del carrusel -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('carousel-container');
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');
    
    if (!container || !prevBtn || !nextBtn) return;
    
    // Calcular ancho de desplazamiento
    function getScrollAmount() {
        const firstItem = container.querySelector('.carousel-item');
        if (!firstItem) return 0;
        
        const itemWidth = firstItem.getBoundingClientRect().width;
        const gap = parseFloat(getComputedStyle(container).gap) || 0;
        return itemWidth + gap;
    }
    
    // Actualizar estado de los botones
    function updateButtons() {
        const scrollLeft = container.scrollLeft;
        const scrollWidth = container.scrollWidth;
        const clientWidth = container.clientWidth;
        
        // Verificar si hay overflow
        const hasOverflow = scrollWidth > clientWidth;
        
        if (!hasOverflow) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
            return;
        }
        
        // Mostrar botones por defecto
        prevBtn.style.display = 'flex';
        nextBtn.style.display = 'flex';
        
        // Debug
        // console.log('Scroll:', scrollLeft, 'Client:', clientWidth, 'Total:', scrollWidth);

        // Ocultar botón prev si está al inicio (con margen de tolerancia)
        if (scrollLeft <= 10) {
            prevBtn.style.display = 'none';
        }
        
        // Ocultar botón next si está al final (con margen de tolerancia)
        // Usamos Math.ceil o un margen pequeño para evitar decimales
        if (Math.ceil(scrollLeft + clientWidth) >= scrollWidth - 5) {
            nextBtn.style.display = 'none';
        }
    }
    
    // Navegación
    prevBtn.addEventListener('click', function() {
        const scrollAmount = getScrollAmount();
        container.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
    });
    
    nextBtn.addEventListener('click', function() {
        const scrollAmount = getScrollAmount();
        container.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    });
    
    // Actualizar botones al hacer scroll
    container.addEventListener('scroll', updateButtons);
    
    // Actualizar botones al redimensionar (con debounce)
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateButtons, 150);
    });
    
    // Inicializar
    updateButtons();
});
</script>

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
