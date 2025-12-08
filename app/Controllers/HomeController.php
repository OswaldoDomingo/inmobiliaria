<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Controlador Principal
 * Gestiona la pÃ¡gina de inicio.
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
        // --- LÃ LOGICA DE LA TARJETA ---

        // Preguntamos: ¿Existe la variable 'tarjeta_vista' en la sesión?
        // El sÃ­mbolo '!' significa NO.
        // Traducción: "Si NO está definida la variable 'tarjeta_vista'..."

        $mostrar_tarjeta = false; // Variable para mostrar la tarjeta de temporada o no
        if (!isset($_SESSION['tarjeta_vista'])) {
        // 2. IMPORTANTE: Ponemos el "sello" inmediatamente para la próxima vez
            $_SESSION['tarjeta_vista'] = true;
        } else {
            // Si ya existe la variable, es que ya entró antes
            $mostrar_tarjeta = true;
        }
        require VIEW . '/layouts/header.php';
        require VIEW . '/home.php';
        require VIEW . '/layouts/footer.php';
    }
}
