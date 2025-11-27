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
