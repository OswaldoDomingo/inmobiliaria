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
        
        require VIEW . '/layouts/header.php';
        require VIEW . '/home.php';
        require VIEW . '/layouts/footer.php';
    }
}
