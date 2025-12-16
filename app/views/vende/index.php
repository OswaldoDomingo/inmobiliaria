<?php
// Espera: $telefono, $email, $direccion, $mapsUrl, $imgPlan, $imgVisita, $imgOnline
?>

<!-- 1) Planificación de ventas -->
<section class="vende-section vende-border bg-white">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-12 col-lg-5">
        <div class="vende-kicker mb-2">Proceso de venta</div>
        <h1 class="display-6 fw-semibold mb-3">Planificación de Ventas</h1>
        <p class="text-muted mb-0" style="max-width: 44ch;">
          Nuestro equipo de marketing y administración trabaja conjuntamente para crear una planificación
          de ventas detallada que incluye fechas de publicación, campañas promocionales y estrategias de cierre.
        </p>
      </div>

      <div class="col-12 col-lg-7">
        <div class="vende-media vende-media--plan">
          <img src="<?= htmlspecialchars($imgPlan, ENT_QUOTES, 'UTF-8') ?>" class="vende-img-cover"
               alt="Asesor inmobiliario hablando con una clienta">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- 2) Oficina (estamos aquí) -->
<section class="vende-section bg-white">
  <div class="container">
    <div class="row align-items-center g-5">

      <div class="col-12 col-lg-6 order-2 order-lg-1">
        <div class="vende-media vende-media--visita">
          <img src="<?= htmlspecialchars($imgVisita, ENT_QUOTES, 'UTF-8') ?>" class="vende-img-cover"
               alt="Visita y asesoramiento en la venta de una vivienda">
        </div>
      </div>

      <div class="col-12 col-lg-6 order-1 order-lg-2">
        <div class="vende-kicker mb-2">Estamos aquí</div>
        <h2 class="h3 fw-semibold mb-2">Vende tu vivienda con nosotros</h2>
        <p class="text-muted mb-4">
          Te acompañamos en todo el proceso: valoración, reportaje, publicación y gestión de visitas.
          Si lo prefieres, puedes pasar por la oficina y te asesoramos en persona.
        </p>

        <div class="card vende-card mb-4">
          <div class="card-body">
            <h3 class="h6 fw-semibold mb-3">Oficina</h3>

            <div class="mb-2">
              <small class="text-muted d-block">Teléfono</small>
              <a href="tel:<?= htmlspecialchars(preg_replace('/\s+/', '', $telefono), ENT_QUOTES, 'UTF-8') ?>"
                 class="text-decoration-none">
                <?= htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8') ?>
              </a>
            </div>

            <div class="mb-2">
              <small class="text-muted d-block">Email</small>
              <a href="mailto:<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" class="text-decoration-none">
                <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>
              </a>
            </div>

            <div>
              <small class="text-muted d-block">Dirección</small>
              <a href="<?= htmlspecialchars($mapsUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"
                 class="text-decoration-none">
                <?= htmlspecialchars($direccion, ENT_QUOTES, 'UTF-8') ?>
              </a>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- 3) Contacto + tasador (remate) -->
<section class="vende-section vende-border bg-white">
  <div class="container">
    <div class="vende-cta" style="height: 320px;">
      <img src="<?= htmlspecialchars($imgOnline, ENT_QUOTES, 'UTF-8') ?>" class="vende-img-cover"
           alt="Contacto online y tasación">

      <div class="vende-cta-content">
        <div class="container">
          <div class="row">
            <div class="col-12 col-lg-7 text-white">
              <div class="vende-kicker text-white-50 mb-2">Tasador y contacto</div>
              <h2 class="h2 fw-semibold mb-2">Calcula una estimación y hablemos</h2>
              <p class="text-white-50 mb-4" style="max-width: 56ch;">
                Puedes escribirnos desde el formulario o usar nuestro tasador online para estimar el precio de tu vivienda
                en la provincia de Valencia. Si encaja, nos pondremos en contacto contigo.
              </p>

              <div class="d-flex flex-column flex-sm-row gap-2">
                <a class="btn btn-light btn-lg" href="/tasacion">Usar tasador online</a>
                <a class="btn btn-outline-light btn-lg" href="/contacto">Escríbenos</a>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>
