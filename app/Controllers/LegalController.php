<?php
declare(strict_types=1);

namespace App\Controllers;

/**
 * Controlador para paginas legales temporales (RGPD, Cookies, etc.).
 * Cada metodo carga una vista con contenido provisional y el layout comun.
 */
class LegalController
{
    public function avisoLegal(): void
    {
        require VIEW . '/layouts/header.php';
        require VIEW . '/legal/aviso_legal.php';
        require VIEW . '/layouts/footer.php';
    }

    public function privacidad(): void
    {
        require VIEW . '/layouts/header.php';
        require VIEW . '/legal/privacidad.php';
        require VIEW . '/layouts/footer.php';
    }

    public function cookies(): void
    {
        require VIEW . '/layouts/header.php';
        require VIEW . '/legal/cookies.php';
        require VIEW . '/layouts/footer.php';
    }
}
