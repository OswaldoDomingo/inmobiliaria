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
        $showHero = true; // Variable para mostrar el hero en el header
        // --- LÓGICA DE LA TARJETA ---

        // Preguntamos: ¿Existe la variable 'tarjeta_vista' en la sesión?
        // El símbolo '!' significa NO.
        // Traducción: "Si NO está definida la variable 'tarjeta_vista'..."
        $mostrar_tarjeta = true; // Variable para mostrar la tarjeta de temporada
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
}
