<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Controlador Principal
 * Gestiona la página de inicio.
 */
class HomeController
{
    /**
     * Muestra la landing page.
     * Ruta: GET /
     */
    public function index(): void
    {
        // Variables del banner principal
        $showHero = true;
        $heroTitle = "Encuentra tu hogar ideal";
        $heroSubTitle = "Miles de propiedades te esperan en Valencia";
        $heroImage = "https://picsum.photos/1920/600";
        
        // Carrusel de propiedades destacadas
        $carouselInmuebles = \App\Models\Inmueble::getHomeCarousel(6, true);
        
        // --- LA LOGICA DE LA TARJETA ---

        // Preguntamos: ¿Existe la variable 'tarjeta_vista' en la sesión?
        // El símbolo '!' significa NO.
        // Traducción: "Si NO está definida la variable 'tarjeta_vista'..."

        $mostrar_tarjeta = false; // Variable para mostrar la tarjeta de temporada o no
        if (!isset($_SESSION['tarjeta_vista'])) {
        // 2. IMPORTANTE: Ponemos el "sello" inmediatamente para la próxima vez
            $_SESSION['tarjeta_vista'] = true;
        } else {
            // Si ya existe la variable, es que ya entró antes
            $mostrar_tarjeta = false;
        }
        require VIEW . '/layouts/header.php';
        require VIEW . '/home.php';
        require VIEW . '/layouts/footer.php';
    }

    public function vende(): void
{
    $telefono = "96 000 00 00";
    $email = "contacto@inmobiliariaejemplo.luc";
    $direccion = "C/ Ejemplo 123, Valencia";
    $mapsUrl = "https://www.google.com/maps?q=" . urlencode($direccion);

    // Imágenes (asegúrate de tenerlas en /public/assets/img/vende/)
    $imgPlan = "/assets/img/vende/planificacion-ventas.png";
    $imgVisita = "/assets/img/vende/inicio-visita.png";
    $imgOnline = "/assets/img/vende/contacto-internet.png";

    require VIEW . '/layouts/header.php';
    require VIEW . '/vende/index.php';
    require VIEW . '/layouts/footer.php';
}


}
