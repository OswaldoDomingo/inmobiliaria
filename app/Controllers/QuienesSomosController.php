<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Controlador para la página pública "Quiénes somos".
 * Muestra información sobre la inmobiliaria, misión, valores y zona de trabajo.
 */
class QuienesSomosController
{
    public function index(): void
    {
        require VIEW . '/layouts/header.php';
        require VIEW . '/quienes_somos/index.php';
        require VIEW . '/layouts/footer.php';
    }
}
