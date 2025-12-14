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
                    <i class="bi bi-chevron-left"></i>
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
                    <i class="bi bi-chevron-right"></i>
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

<!-- Estilos del carrusel -->
<style>
.section-title {
    font-family: system-ui, -apple-system, sans-serif !important;
    font-weight: 700 !important;
    text-transform: uppercase; /* Optional: ensures it matches the caps in the request if desired, but user typed caps */
}

.carousel-wrapper {
    position: relative;
    padding: 0 50px;
    margin-bottom: 2rem;
}

.carousel-container {
    /* Estrategia CSS Robusta */
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    overflow-x: auto !important;
    overflow-y: hidden !important;
    white-space: nowrap !important; /* Forzar línea única */
    
    scroll-snap-type: x mandatory;
    gap: 20px;
    padding: 20px 5px;
    
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* IE/Edge */
    
    width: 100% !important;
    height: auto !important;
    align-items: stretch !important;
}

.carousel-container::-webkit-scrollbar {
    display: none; /* Chrome/Safari */
}

.carousel-item {
    /* Forzar comportamiento de bloque en línea si flex falla */
    display: inline-block !important; 
    vertical-align: top !important;
    white-space: normal !important; /* Reset para el contenido interno */
    
    /* Dimensiones fijas estrictas */
    flex: 0 0 280px !important;
    width: 280px !important;
    min-width: 280px !important;
    max-width: 280px !important;
    
    margin-right: 20px; /* Fallback para gap */
    scroll-snap-align: start;
}

.carousel-card {
    display: flex !important;
    flex-direction: column;
    height: 100%;
}

.carousel-card-img {
    height: 200px;
    object-fit: cover;
    width: 100%;
}

.carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #dee2e6;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.carousel-nav:hover:not(:disabled) {
    background: #212529;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.carousel-nav:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.carousel-nav-prev {
    left: 0;
}

.carousel-nav-next {
    right: 0;
}

.carousel-nav i {
    font-size: 1.2rem;
}

/* Responsive */
@media (min-width: 576px) {
    .carousel-item {
        width: 300px;
    }
}

@media (min-width: 992px) {
    .carousel-item {
        width: 320px;
    }
}

@media (max-width: 575px) {
    .carousel-wrapper {
        padding: 0 40px;
    }
    
    .carousel-nav {
        width: 35px;
        height: 35px;
    }
    
    .carousel-nav i {
        font-size: 1rem;
    }
}
</style>

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
        
        prevBtn.style.display = 'flex';
        nextBtn.style.display = 'flex';
        
        // Deshabilitar botón prev si está al inicio
        prevBtn.disabled = scrollLeft <= 1;
        
        // Deshabilitar botón next si está al final
        nextBtn.disabled = scrollLeft + clientWidth >= scrollWidth - 1;
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
