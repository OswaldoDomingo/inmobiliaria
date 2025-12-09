<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Inmueble;

final class InmueblePublicController
{
    private Inmueble $inmuebles;

    public function __construct()
    {
        $this->inmuebles = new Inmueble();
    }

    public function index(): void
    {
        $filters = [
            'localidad'   => trim((string)($_GET['localidad'] ?? '')),
            'tipo'        => trim((string)($_GET['tipo'] ?? '')),
            'precio_min'  => trim((string)($_GET['precio_min'] ?? '')),
            'precio_max'  => trim((string)($_GET['precio_max'] ?? '')),
            'operacion'   => trim((string)($_GET['operacion'] ?? '')),
        ];

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $result = $this->inmuebles->paginatePublic($filters, $page, $perPage);

        // Header and Footer inclusion handled by View or Main Controller logic?
        // User asked to match Public layout. 
        // In previous steps I saw HomeController including header/footer manually.
        // I will do the same here to ensure it looks good.
        
        require VIEW . '/layouts/header.php';
        require VIEW . '/propiedades/index.php';
        require VIEW . '/layouts/footer.php';
    }

    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/propiedades');
            return;
        }

        $inmueble = $this->inmuebles->findById($id);
        if (!$inmueble) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        // Validación extra de seguridad (publicable)
        // estado='activo' AND activo=1 AND archivado=0
        $estado    = is_array($inmueble) ? ($inmueble['estado'] ?? '') : ($inmueble->estado ?? '');
        $activo    = is_array($inmueble) ? (int)($inmueble['activo'] ?? 0) : (int)($inmueble->activo ?? 0);
        $archivado = is_array($inmueble) ? (int)($inmueble['archivado'] ?? 0) : (int)($inmueble->archivado ?? 0);

        if ($estado !== 'activo' || $activo !== 1 || $archivado !== 0) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        require VIEW . '/layouts/header.php';
        require VIEW . '/propiedades/show.php';
        require VIEW . '/layouts/footer.php';
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
