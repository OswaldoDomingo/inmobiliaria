<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cliente;
use PDO;

final class DemandaController
{
    private Cliente $clientes;

    public function __construct()
    {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador', 'comercial']);

        $this->clientes = new Cliente();
    }

    public function create(): void
    {
        $clienteId = (int)($_GET['cliente_id'] ?? 0);
        $cliente = null;

        if ($clienteId > 0) {
            $cliente = $this->clientes->findById($clienteId);
        }

        // Si no hay cliente válido, quizás redirigir o mostrar error, o permitir crear vacío?
        // El requisito dice "Reciba el cliente_id... consulte modelo... pase a la vista".
        
        require VIEW . '/admin/demandas/form.php';
    }

    // AUTH HELPER METHODS (Duplicated from other controllers for now, should be in BaseController)
    private function requireAuth(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        // Verificar 'user_id' que es lo que fija AuthController
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['user']['id_usuario']) && !isset($_SESSION['id_usuario'])) {
             header('Location: /login'); exit;
        }
    }

    private function requireRole(array $roles): void
    {
        // Verificar 'user_role'
        $rol = $_SESSION['user_role'] ?? $_SESSION['user']['rol'] ?? $_SESSION['rol'] ?? null;
        if (!$rol || !in_array($rol, $roles, true)) {
            http_response_code(403); echo '403 Forbidden'; exit;
        }
    }
}
