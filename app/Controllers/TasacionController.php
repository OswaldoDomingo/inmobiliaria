<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Csrf;
use App\Lib\SimpleSMTP;
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
            $telefono = trim(strip_tags($data['user_phone'] ?? ''));
            $cp = trim(strip_tags($data['cp'] ?? ''));
            $barrio = trim(strip_tags($data['barrio'] ?? ''));
            $zona = trim(strip_tags($data['zona'] ?? ''));
            $superficie = trim(strip_tags($data['superficie'] ?? ''));
            $precio_min = trim(strip_tags($data['precio_min'] ?? ''));
            $precio_max = trim(strip_tags($data['precio_max'] ?? ''));
            $caracteristicas = trim(strip_tags($data['caracteristicas'] ?? ''));
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

            // Configuracion
            $to_agency = Config::get('emails.agency', 'no-responder@example.com');
            $from_email = Config::get('emails.noreply', $to_agency);
            $subject_agency = 'Nuevo Lead de Tasacion Online';
            $subject_client = 'Tu valoracion inmobiliaria - Confirmacion';

            // --- EMAIL PARA LA AGENCIA ---
            $message_agency = "
            <html>
            <head>
              <title>Nuevo Lead de Tasacion</title>
            </head>
            <body>
              <h2>Nuevo Lead Recibido</h2>
              <p><strong>Fecha:</strong> $fecha</p>
              <h3>Datos del Cliente</h3>
              <ul>
                <li><strong>Email:</strong> " . htmlspecialchars($email_cliente) . "</li>
                <li><strong>Telefono:</strong> " . htmlspecialchars($telefono) . "</li>
              </ul>
              <h3>Datos del Inmueble</h3>
              <ul>
                <li><strong>CP:</strong> " . htmlspecialchars($cp) . "</li>
                <li><strong>Barrio:</strong> " . htmlspecialchars($barrio) . "</li>
                <li><strong>Zona:</strong> " . htmlspecialchars($zona) . "</li>
                <li><strong>Superficie:</strong> " . htmlspecialchars($superficie) . " m2</li>
                <li><strong>Caracteristicas:</strong> " . htmlspecialchars($caracteristicas) . "</li>
              </ul>
              <h3>Valoracion Estimada</h3>
              <p><strong>Rango:</strong> " . htmlspecialchars($precio_min) . " - " . htmlspecialchars($precio_max) . "</p>
            </body>
            </html>
            ";

            $headers_agency = "MIME-Version: 1.0" . "\r\n";
            $headers_agency .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers_agency .= "From: Tasador Online <{$from_email}>" . "\r\n";
            $headers_agency .= "Reply-To: $email_cliente" . "\r\n";

            // --- EMAIL PARA EL CLIENTE ---
            $message_client = "
            <html>
            <head>
              <title>Tu Valoracion Inmobiliaria</title>
            </head>
            <body>
              <h2>Hola,</h2>
              <p>Gracias por utilizar nuestro tasador online. Aqui tienes el resumen de tu valoracion.</p>
              
              <div style='background-color: #f3f4f6; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                <h3 style='color: #4f46e5; margin-top: 0;'>Valoracion Estimada</h3>
                <p style='font-size: 24px; font-weight: bold;'>" . htmlspecialchars($precio_min) . " - " . htmlspecialchars($precio_max) . "</p>
              </div>

              <h3>Detalles de tu inmueble:</h3>
              <ul>
                <li><strong>Ubicacion:</strong> " . htmlspecialchars($barrio) . ", " . htmlspecialchars($zona) . " (" . htmlspecialchars($cp) . ")</li>
                <li><strong>Superficie:</strong> " . htmlspecialchars($superficie) . " m2</li>
                <li><strong>Extras:</strong> " . htmlspecialchars($caracteristicas) . "</li>
              </ul>

              <p>Un agente se pondra en contacto contigo pronto para validar estos datos y ofrecerte una valoracion mas precisa.</p>
              
              <p>Atentamente,<br>El equipo de Tasacion</p>
            </body>
            </html>
            ";

            $headers_client = "MIME-Version: 1.0" . "\r\n";
            $headers_client .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers_client .= "From: Tasador Online <{$from_email}>" . "\r\n";
            $headers_client .= "Reply-To: $to_agency" . "\r\n";

            // --- CONFIGURACION SMTP ---
            $smtpConfig = Config::get('smtp', []);
            $smtp_host = $smtpConfig['host'] ?? '';
            $smtp_port = (int)($smtpConfig['port'] ?? 587);
            $smtp_user = $smtpConfig['user'] ?? '';
            $smtp_pass = $smtpConfig['pass'] ?? '';

            $smtp = new SimpleSMTP($smtp_host, $smtp_port, $smtp_user, $smtp_pass);

            $sent_agency = false;
            $sent_client = false;
            $error_agency = '';
            $error_client = '';

            // Enviar a la agencia
            try {
                $smtp->send($to_agency, $subject_agency, $message_agency, $headers_agency);
                $sent_agency = true;
            } catch (Exception $e) {
                $error_agency = $e->getMessage();
            }

            // Enviar al cliente
            try {
                $smtp->send($email_cliente, $subject_client, $message_client, $headers_client);
                $sent_client = true;
            } catch (Exception $e) {
                $error_client = $e->getMessage();
            }

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
