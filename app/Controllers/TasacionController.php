<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Csrf;
use App\Services\MailService;
use Exception;
use Throwable;

/**
 * Controlador de Tasacion
 * Gestiona la vista del formulario y el envio de correos.
 */
class TasacionController
{
    /**
     * Muestra el formulario de tasacion.
     * Ruta: GET /tasacion
     */
    public function index(): void
    {
        // CSS especifico para la tasacion
        $extraCss = '
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                corePlugins: {
                    preflight: false,
                }
            }
        </script>
        <style>
            .navbar .collapse:not(.show) {
                display: none;
                visibility: visible;
            }
            .navbar .collapse.show {
                display: block;
                visibility: visible;
            }
            @media (min-width: 992px) {
                .navbar .collapse {
                    display: flex !important;
                    visibility: visible !important;
                }
            }
        </style>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
        <script src="https://unpkg.com/lucide@latest"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="/assets/css/tasacion.css">
        ';

        // Ocultamos el hero de la landing
        $showHero = false;
        $csrfToken = Csrf::token();

        require VIEW . '/layouts/header.php';
        require VIEW . '/tasacion/formulario.php';
        require VIEW . '/layouts/footer.php';
    }

    /**
     * Procesa el envio del formulario y manda los correos.
     * Ruta: POST /tasacion/enviar
     */
    public function enviar(): void
    {
        // Configuracion de cabeceras para respuesta JSON
        header('Content-Type: application/json');

        // Evitar que errores de PHP rompan el JSON
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        error_reporting(E_ALL);

        try {
            // CSRF basado en header
            $csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!Csrf::validate($csrfHeader)) {
                http_response_code(419);
                echo json_encode(['status' => 'error', 'message' => 'Sesion expirada. Refresca la pagina.']);
                return;
            }

            // Recibir datos JSON
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (!$data) {
                throw new Exception('No se recibieron datos o el JSON es invalido');
            }

            // 1. Sanitizacion estricta
            $email_cliente = trim(filter_var($data['to_email'] ?? '', FILTER_SANITIZE_EMAIL));
            $telefono = trim(strip_tags((string)($data['user_phone'] ?? '')));
            $cp = trim(strip_tags((string)($data['cp'] ?? '')));
            $barrio = trim(strip_tags((string)($data['barrio'] ?? '')));
            $zona = trim(strip_tags((string)($data['zona'] ?? '')));
            $superficie = trim(strip_tags((string)($data['superficie'] ?? '')));
            $precio_min = trim(strip_tags((string)($data['precio_min'] ?? '')));
            $precio_max = trim(strip_tags((string)($data['precio_max'] ?? '')));
            $caracteristicas = trim(strip_tags((string)($data['caracteristicas'] ?? '')));
            $fecha = date('d/m/Y H:i');

            // 2. Validacion estricta
            $errors = [];

            if (empty($email_cliente) || !filter_var($email_cliente, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "El email no es valido.";
            }

            if (empty($telefono)) {
                $errors[] = "El telefono es obligatorio.";
            }

            if (empty($cp) || strlen($cp) < 4) {
                $errors[] = "El codigo postal no es valido.";
            }

            if (empty($barrio)) {
                $errors[] = "El barrio es obligatorio.";
            }

            if (empty($superficie) || !is_numeric($superficie)) {
                $errors[] = "La superficie debe ser un numero valido.";
            }

            // Si hay errores de validacion, devolverlos
            if (!empty($errors)) {
                echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
                return;
            }

            // Configuracion de emails
            $to_agency = Config::get('emails.agency', 'no-responder@example.com');
            $subject_agency = 'Nuevo Lead de Tasacion Online';
            $subject_client = 'Tu valoracion inmobiliaria - Confirmacion';

            // Datos para las plantillas
            $templateData = [
                'fecha' => $fecha,
                'email_cliente' => $email_cliente,
                'telefono' => $telefono,
                'cp' => $cp,
                'barrio' => $barrio,
                'zona' => $zona,
                'superficie' => $superficie,
                'caracteristicas' => $caracteristicas,
                'precio_min' => $precio_min,
                'precio_max' => $precio_max
            ];

            $sent_agency = false;
            $sent_client = false;
            $error_agency = '';
            $error_client = '';

            // --- ENVIAR EMAIL A LA AGENCIA ---
            try {
                MailService::send($to_agency, $subject_agency, [
                    'template' => 'tasacion_agencia',
                    'data' => $templateData
                ]);
                $sent_agency = true;
            } catch (Exception $e) {
                $error_agency = $e->getMessage();
            }

            // --- ENVIAR EMAIL AL CLIENTE ---
            try {
                MailService::send($email_cliente, $subject_client, [
                    'template' => 'tasacion_cliente',
                    'data' => $templateData,
                    'replyTo' => $to_agency
                ]);
                $sent_client = true;
            } catch (Exception $e) {
                $error_client = $e->getMessage();
            }

            // Respuesta al frontend
            if ($sent_agency && $sent_client) {
                echo json_encode(['status' => 'success', 'message' => 'Emails enviados correctamente']);
            } else {
                $msg = "Errores: ";
                if (!$sent_agency) $msg .= "Agencia: $error_agency. ";
                if (!$sent_client) $msg .= "Cliente: $error_client.";
                echo json_encode(['status' => 'error', 'message' => $msg]);
            }

        } catch (Throwable $e) {
            $safeMessage = Config::get('app.debug') ? $e->getMessage() : 'Error procesando la solicitud.';
            echo json_encode(['status' => 'error', 'message' => $safeMessage]);
        }
    }
}
