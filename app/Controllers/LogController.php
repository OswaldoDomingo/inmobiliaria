<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;

/**
 * Controlador de Logs
 * Visualización de registros de auditoría.
 */
class LogController
{
    public function __construct()
    {
        // Seguridad: Solo admin puede acceder
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            die("<h1>403 Forbidden</h1><p>No tienes permisos para acceder a esta seccion.</p>");
        }
    }

    /**
     * Muestra el listado de logs.
     * GET /admin/logs
     */
    public function index(): void
    {
        $logFile = ROOT . '/logs/auth.log';
        $logs = [];

        if (file_exists($logFile)) {
            // Leer archivo en un array (cada línea es un elemento)
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            if ($lines !== false) {
                // Invertir para ver los más recientes primero
                $lines = array_reverse($lines);

                foreach ($lines as $line) {
                    // Parsear línea: FECHA|IP|EVENTO|EMAIL
                    $parts = explode('|', $line);
                    
                    if (count($parts) >= 4) {
                        $logs[] = [
                            'fecha'   => $parts[0],
                            'ip'      => $parts[1],
                            'evento'  => $parts[2],
                            'usuario' => $parts[3]
                        ];
                    }
                }
            }
        }

        require VIEW . '/admin/logs/index.php';
    }
}
