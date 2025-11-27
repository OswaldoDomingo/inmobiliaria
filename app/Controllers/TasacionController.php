<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Lib\SimpleSMTP;
use Exception;
use Throwable;

/**
 * Controlador de Tasación
 * Gestiona la vista del formulario y el envío de correos.
 */
class TasacionController
{
    /**
     * Muestra el formulario de tasación.
     * Ruta: GET /tasacion
     */
    public function index(): void
    {
        // CSS específico para la tasación
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
            /* FIX: Conflicto Bootstrap vs Tailwind */
            /* Tailwind pone visibility: collapse en .collapse, lo que oculta el menú de Bootstrap */
            .navbar .collapse:not(.show) {
                display: none; /* Comportamiento standard de Bootstrap */
                visibility: visible; /* Sobrescribir Tailwind */
            }
            .navbar .collapse.show {
                display: block;
                visibility: visible;
            }
            /* Asegurar que en desktop se vea */
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

        require VIEW . '/layouts/header.php';
        require VIEW . '/tasacion/formulario.php';
        require VIEW . '/layouts/footer.php';
    }

    /**
     * Procesa el envío del formulario y manda los correos.
     * Ruta: POST /tasacion/enviar
     */
    public function enviar(): void
    {
        // Configuración de cabeceras para respuesta JSON
        header('Content-Type: application/json');

        // Evitar que errores de PHP rompan el JSON
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        error_reporting(E_ALL);

        try {
            // Recibir datos JSON
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);

            if (!$data) {
                throw new Exception('No se recibieron datos o el JSON es inválido');
            }

            // Configuración (Idealmente esto iría en config/config.php o .env)
            $to_agency = 'no-responder@oswaldo.dev'; // Email de la agencia
            $subject_agency = 'Nuevo Lead de Tasación Online';
            $subject_client = 'Tu valoración inmobiliaria - Confirmación';

            // Extraer datos
            $email_cliente = $data['to_email'] ?? '';
            $telefono = $data['user_phone'] ?? '';
            $cp = $data['cp'] ?? '';
            $barrio = $data['barrio'] ?? '';
            $zona = $data['zona'] ?? '';
            $superficie = $data['superficie'] ?? '';
            $precio_min = $data['precio_min'] ?? '';
            $precio_max = $data['precio_max'] ?? '';
            $caracteristicas = $data['caracteristicas'] ?? '';
            $fecha = $data['date'] ?? date('d/m/Y H:i');

            // Validar email
            if (!filter_var($email_cliente, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Dirección de correo inválida');
            }

            // --- EMAIL PARA LA AGENCIA ---
            $message_agency = "
            <html>
            <head>
              <title>Nuevo Lead de Tasación</title>
            </head>
            <body>
              <h2>Nuevo Lead Recibido</h2>
              <p><strong>Fecha:</strong> $fecha</p>
              <h3>Datos del Cliente</h3>
              <ul>
                <li><strong>Email:</strong> $email_cliente</li>
                <li><strong>Teléfono:</strong> $telefono</li>
              </ul>
              <h3>Datos del Inmueble</h3>
              <ul>
                <li><strong>CP:</strong> $cp</li>
                <li><strong>Barrio:</strong> $barrio</li>
                <li><strong>Zona:</strong> $zona</li>
                <li><strong>Superficie:</strong> $superficie m²</li>
                <li><strong>Características:</strong> $caracteristicas</li>
              </ul>
              <h3>Valoración Estimada</h3>
              <p><strong>Rango:</strong> $precio_min - $precio_max</p>
            </body>
            </html>
            ";

            $headers_agency = "MIME-Version: 1.0" . "\r\n";
            $headers_agency .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers_agency .= "From: Tasador Online <no-responder@oswaldo.dev>" . "\r\n";
            $headers_agency .= "Reply-To: $email_cliente" . "\r\n";

            // --- EMAIL PARA EL CLIENTE ---
            $message_client = "
            <html>
            <head>
              <title>Tu Valoración Inmobiliaria</title>
            </head>
            <body>
              <h2>Hola,</h2>
              <p>Gracias por utilizar nuestro tasador online. Aquí tienes el resumen de tu valoración.</p>
              
              <div style='background-color: #f3f4f6; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                <h3 style='color: #4f46e5; margin-top: 0;'>Valoración Estimada</h3>
                <p style='font-size: 24px; font-weight: bold;'>$precio_min - $precio_max</p>
              </div>

              <h3>Detalles de tu inmueble:</h3>
              <ul>
                <li><strong>Ubicación:</strong> $barrio, $zona ($cp)</li>
                <li><strong>Superficie:</strong> $superficie m²</li>
                <li><strong>Extras:</strong> $caracteristicas</li>
              </ul>

              <p>Un agente se pondrá en contacto contigo pronto para validar estos datos y ofrecerte una valoración más precisa.</p>
              
              <p>Atentamente,<br>El equipo de Tasación</p>
            </body>
            </html>
            ";

            $headers_client = "MIME-Version: 1.0" . "\r\n";
            $headers_client .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers_client .= "From: Tasador Online <no-responder@oswaldo.dev>" . "\r\n";
            $headers_client .= "Reply-To: $to_agency" . "\r\n";

            // --- CONFIGURACIÓN SMTP ---
            // TODO: Mover credenciales a .env
            $smtp_host = 'mail.oswaldo.dev'; 
            $smtp_port = 587; 
            $smtp_user = 'no-responder@oswaldo.dev'; 
            $smtp_pass = 'Oswaldo!1963ñ'; 

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
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
