<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Config;

// Carga manual de PHPMailer (sin Composer)
require_once __DIR__ . '/../Lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../Lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../Lib/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * MailService
 * Servicio centralizado para envío de correos electrónicos usando PHPMailer.
 * 
 * Características:
 * - Soporte SMTP con TLS/SSL
 * - Renderizado de plantillas desde app/Views/emails/
 * - Logging de errores
 * - Configuración centralizada desde Config
 */
class MailService
{
    /**
     * Envía un correo electrónico.
     *
     * @param string $to Email destinatario
     * @param string $subject Asunto del correo
     * @param array<string, mixed> $options Opciones adicionales
     *   - 'body' (string): HTML body directo
     *   - 'template' (string): Nombre de plantilla en app/Views/emails/
     *   - 'data' (array): Datos para la plantilla
     *   - 'from' (string): Email remitente (opcional, usa default)
     *   - 'fromName' (string): Nombre remitente (opcional)
     *   - 'replyTo' (string): Email de respuesta (opcional)
     *   - 'attachments' (array): Array de rutas de archivos adjuntos (opcional)
     * 
     * @return bool True si se envió correctamente
     * @throws Exception Si hay algún error en el envío
     */
    public static function send(string $to, string $subject, array $options = []): bool
    {
        try {
            $mail = new PHPMailer(true); // Enable exceptions

            // ===== CONFIGURACIÓN SMTP =====
            $smtpConfig = Config::get('smtp', []);
            $smtpSecure = $smtpConfig['secure'] ?? 'tls'; // tls, ssl, o vacío

            $mail->isSMTP();
            $mail->Host       = $smtpConfig['host'] ?? '';
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpConfig['user'] ?? '';
            $mail->Password   = $smtpConfig['pass'] ?? '';
            $mail->SMTPSecure = $smtpSecure === 'none' ? '' : $smtpSecure;
            $mail->Port       = (int)($smtpConfig['port'] ?? 587);
            $mail->CharSet    = 'UTF-8';

            // Debug (solo en desarrollo)
            if (Config::get('app.debug', false)) {
                $mail->SMTPDebug = 2; // 0 = off, 1 = client messages, 2 = client and server
                $mail->Debugoutput = function($str, $level) {
                    self::log("SMTP Debug [$level]: $str");
                };
            }

            // ===== REMITENTE =====
            // Obtener email remitente con fallbacks robustos
            $fromEmail = $options['from'] ?? 
                         Config::get('emails.noreply') ?? 
                         Config::get('smtp.user') ?? 
                         '';
            
            // Si aún está vacío, lanzar error claro
            if (empty($fromEmail)) {
                throw new Exception('No se ha configurado un email remitente. Configura SMTP_USER o NOREPLY_EMAIL en .env');
            }
            
            $fromName = $options['fromName'] ?? Config::get('app.name', 'CRM Inmobiliaria');
            $mail->setFrom($fromEmail, $fromName);

            // ===== DESTINATARIO =====
            $mail->addAddress($to);

            // ===== REPLY-TO =====
            if (!empty($options['replyTo'])) {
                $mail->addReplyTo($options['replyTo']);
            }

            // ===== ASUNTO =====
            $mail->Subject = $subject;

            // ===== CUERPO =====
            $mail->isHTML(true);
            
            if (!empty($options['template'])) {
                // Renderizar desde plantilla
                $body = self::renderTemplate($options['template'], $options['data'] ?? []);
            } else {
                // Usar body directo
                $body = $options['body'] ?? '';
            }

            $mail->Body = $body;
            
            // Alternativa en texto plano (opcional pero recomendado)
            $mail->AltBody = strip_tags($body);

            // ===== ADJUNTOS =====
            if (!empty($options['attachments']) && is_array($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    if (file_exists($attachment)) {
                        $mail->addAttachment($attachment);
                    }
                }
            }

            // ===== ENVIAR =====
            $result = $mail->send();
            
            if ($result) {
                self::log("Email enviado correctamente a: $to | Asunto: $subject");
            }

            return $result;

        } catch (Exception $e) {
            // Preferir el mensaje de la excepción si existe, sino ErrorInfo
            $msg = $e->getMessage();
            if (empty($msg) && !empty($mail->ErrorInfo)) {
                $msg = $mail->ErrorInfo;
            }
            
            $errorMsg = "Error enviando email a $to: $msg";
            self::log($errorMsg, 'error');
            throw new Exception($errorMsg);
        }
    }

    /**
     * Renderiza una plantilla de email.
     *
     * @param string $templateName Nombre del archivo de plantilla (sin .php)
     * @param array<string, mixed> $data Datos para pasar a la plantilla
     * @return string HTML renderizado
     * @throws Exception Si la plantilla no existe
     */
    private static function renderTemplate(string $templateName, array $data = []): string
    {
        // Definir VIEW si no está definida
        if (!defined('VIEW')) {
            define('VIEW', __DIR__ . '/../Views');
        }

        $templatePath = VIEW . "/emails/{$templateName}.php";

        if (!file_exists($templatePath)) {
            throw new Exception("Plantilla de email no encontrada: {$templateName}");
        }

        // Extraer variables para la plantilla
        extract($data, EXTR_SKIP);

        // Capturar output
        ob_start();
        include $templatePath;
        $content = ob_get_clean();

        // El layout ya se incluye dentro de las plantillas (legacy mode)
        // Esto evita la duplicación de cabeceras y pies
        return $content;
    }

    /**
     * Registra un mensaje en el log de correos.
     *
     * @param string $message Mensaje a registrar
     * @param string $level Nivel: 'info', 'error', 'debug'
     * @return void
     */
    private static function log(string $message, string $level = 'info'): void
    {
        $logDir = __DIR__ . '/../../logs';
        
        // Crear directorio de logs si no existe
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/mail.log';
        $timestamp = date('Y-m-d H:i:s');
        $levelUpper = strtoupper($level);
        $logMessage = "[$timestamp] [$levelUpper] $message" . PHP_EOL;

        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
}
