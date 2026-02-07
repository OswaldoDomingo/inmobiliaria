<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Csrf;
use App\Services\MailService;
use App\Models\Inmueble;
use App\Models\User; // Para obtener datos del comercial si hiciera falta lógica extra
use Exception;
use Throwable;

class ContactController
{
    private const LOG_FILE = 'storage/logs/contacto.log';
    private const MOTIVOS_VALIDOS = ['info', 'compra', 'venta', 'alquiler'];
    
    private Inmueble $inmuebleModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->inmuebleModel = new Inmueble();
    }

    /**
     * Muestra el formulario de contacto.
     * GET /contacto
     */
    public function index(): void
    {
        // 1. Generar token CSRF
        $csrfToken = Csrf::token();
        
        // 2. Recuperar errores y datos previos de sesión (si existen por validación fallida)
        $errors = $_SESSION['contact_errors'] ?? [];
        $old = $_SESSION['contact_old'] ?? [];
        
        // Limpiar sesión después de recuperar (para que no persistan en siguientes cargas)
        unset($_SESSION['contact_errors'], $_SESSION['contact_old']);
        
        // 3. Procesar inmueble si viene por parámetro
        $idInmueble = (int)($_GET['id_inmueble'] ?? 0);
        $inmueble = null;

        if ($idInmueble > 0) {
            $inmuebleData = $this->inmuebleModel->findById($idInmueble);
            
            // Convertir a array para compatibilidad con vistas y evitar error stdClass
            if ($inmuebleData) {
                $inmuebleData = (array)$inmuebleData;
                
                // Validar que el inmueble es público y activo
                if (($inmuebleData['estado'] ?? '') === 'activo' && 
                    (int)($inmuebleData['activo'] ?? 0) === 1 && 
                    (int)($inmuebleData['archivado'] ?? 0) === 0
                ) {
                    $inmueble = $inmuebleData;
                }
            }
        }

        // 4. Manejar prefill de mensaje según motivo (GET parameter)
        // IMPORTANTE: Solo aplicar si NO hay mensaje previo del usuario
        $motivo = strtolower(trim((string)($_GET['motivo'] ?? '')));
        
        // Solo prefill si motivo es válido Y el mensaje está vacío (no pisar datos del usuario)
        if (in_array($motivo, self::MOTIVOS_VALIDOS, true) && empty($old['mensaje'])) {
            $mensajesPorMotivo = [
                'info' => 'Hola, me gustaría recibir más información sobre vuestros servicios. Gracias.',
                'compra' => 'Hola, estoy interesado/a en comprar una propiedad. ¿Podríais ayudarme? Gracias.',
                'venta' => 'Hola, me gustaría vender mi propiedad. ¿Podríais contactarme? Gracias.',
                'alquiler' => 'Hola, estoy buscando una propiedad en alquiler. ¿Podríais ayudarme? Gracias.',
            ];
            
            $old['mensaje'] = $mensajesPorMotivo[$motivo];
        }

        require VIEW . '/layouts/header.php';
        require VIEW . '/contacto/form.php';
        require VIEW . '/layouts/footer.php';
    }

    /**
     * Procesa el envío del formulario.
     * POST /contacto/enviar
     */
    public function enviar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /contacto');
            exit;
        }

        // 1. CSRF Check
        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $this->logContacto('CSRF_INVALID', ['ip' => $_SERVER['REMOTE_ADDR']]);
            // Mensaje genérico para no dar pistas, o recargar con error
            $errors = ['general' => 'Error de seguridad. Por favor, recarga la página e inténtalo de nuevo.'];
            $old = $_POST;
            $csrfToken = Csrf::token(); // Regenerar o usar nuevo
            require VIEW . '/layouts/header.php';
            require VIEW . '/contacto/form.php';
            require VIEW . '/layouts/footer.php';
            return;
        }

        // 2. Honeypot Check
        if (!empty($_POST['website'])) {
            $this->logContacto('SPAM_HONEYPOT', [
                'ip' => $_SERVER['REMOTE_ADDR'],
                'honeypot_content' => $_POST['website']
            ]);
            // Simular éxito para despistar al bot
            // O mostrar error genérico. El usuario pidió "mensaje genérico o éxito silencioso".
            // Vamos a redigir a éxito silencioso para confundir al bot.
            require VIEW . '/layouts/header.php';
            require VIEW . '/contacto/exito.php';
            require VIEW . '/layouts/footer.php';
            return;
        }

        // 3. Rate Limit (30 segundos)
        $lastContact = $_SESSION['ultimo_contacto'] ?? 0;
        if (time() - $lastContact < 30) {
            $this->logContacto('RATE_LIMIT', ['ip' => $_SERVER['REMOTE_ADDR']]);
            $errors = ['general' => 'Por favor, espera unos segundos antes de enviar otro mensaje.'];
            $old = $_POST;
            $csrfToken = Csrf::token();
            
            // Si había inmueble, intentamos recuperarlo para que no desaparezca del formulario
            $inmueble = null;
            $idInmueble = (int)($_POST['id_inmueble'] ?? 0);
            if ($idInmueble > 0) {
                 $inmuebleObj = $this->inmuebleModel->findById($idInmueble); // Re-fetch para mostrar info
                 if ($inmuebleObj) {
                     $inmueble = (array)$inmuebleObj;
                 }
            }

            require VIEW . '/layouts/header.php';
            require VIEW . '/contacto/form.php';
            require VIEW . '/layouts/footer.php';
            return;
        }

        // 4. Sanitización y Validación
        $input = [
            'nombre' => trim(strip_tags($_POST['nombre'] ?? '')),
            'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
            'telefono' => trim(strip_tags($_POST['telefono'] ?? '')),
            'mensaje' => trim(strip_tags($_POST['mensaje'] ?? '')),
            'politica_privacidad' => (int)($_POST['politica_privacidad'] ?? 0),
            'id_inmueble' => (int)($_POST['id_inmueble'] ?? 0),
            'motivo' => strtolower(trim((string)($_POST['motivo'] ?? ''))),
        ];

        $errors = [];

        // Nombre
        if (empty($input['nombre']) || strlen($input['nombre']) < 3 || strlen($input['nombre']) > 100) {
            $errors['nombre'] = "El nombre debe tener entre 3 y 100 caracteres.";
        }

        // Email
        if (empty($input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "El correo electrónico no es válido.";
        }

        // Teléfono
        if (empty($input['telefono'])) {
            $errors['telefono'] = "El teléfono es obligatorio.";
        } elseif (!preg_match('/^[0-9+\s\-]{6,20}$/', $input['telefono'])) {
            $errors['telefono'] = "El teléfono solo puede contener números, espacios y los símbolos + y -.";
        }

        // Mensaje (max 1000)
        if (strlen($input['mensaje']) > 1000) {
            $errors['mensaje'] = "El mensaje no puede superar los 1000 caracteres.";
        }

        // Privacidad
        if ($input['politica_privacidad'] !== 1) {
            $errors['politica_privacidad'] = "Debes aceptar la política de privacidad para enviar el formulario.";
        }

        // Inmueble (validación extra si se manipula el ID)
        $inmueble = null;
        if ($input['id_inmueble'] > 0) {
            $inmuebleData = $this->inmuebleModel->findById($input['id_inmueble']);
            if ($inmuebleData) {
                $inmueble = (array)$inmuebleData; // Se usará para el email y la vista si hay error
            } else {
                // Si mandan un ID que no existe, lo ignoramos o lo logueamos. 
                // Mejor limpiarlo para no enviar datos falsos.
                $input['id_inmueble'] = 0;
            }
        }

        // Si hay errores de validación
        if (!empty($errors)) {
            $this->logContacto('VALIDATION_ERROR', array_keys($errors));
            
            // Guardar errores y datos en sesión para que index() los recupere
            $_SESSION['contact_errors'] = $errors;
            $_SESSION['contact_old'] = $input;
            
            // Redirigir a index() para que muestre el formulario con errores
            // Mantener parámetros GET si existen (id_inmueble, motivo)
            $queryParams = [];
            
            if ($input['id_inmueble'] > 0) {
                $queryParams['id_inmueble'] = $input['id_inmueble'];
            }
            
            // Validar y añadir motivo si es válido
            if (!empty($input['motivo']) && in_array($input['motivo'], self::MOTIVOS_VALIDOS, true)) {
                $queryParams['motivo'] = $input['motivo'];
            }
            
            $redirectUrl = '/contacto' . (!empty($queryParams) ? '?' . http_build_query($queryParams) : '');
            header("Location: $redirectUrl");
            exit;
        }

        // 5. Todo OK - Preparar envío
        $this->logContacto('FORM_OK', [
            'email' => $input['email'],
            'telefono' => $input['telefono'],
            'id_inmueble' => $input['id_inmueble']
        ]);

        $_SESSION['ultimo_contacto'] = time();

        // Datos para el email
        $templateData = [
            'fecha' => date('d/m/Y H:i'),
            'nombre' => $input['nombre'],
            'email' => $input['email'],
            'telefono' => $input['telefono'],
            'mensaje' => $input['mensaje'],
            'inmueble' => $inmueble // Pasamos todo el objeto/array del inmueble
        ];

        $toAgencia = Config::get('emails.agency', 'info@inmobiliaria.example.com');
        $subject = 'Nuevo Contacto desde la Web';
        
        // Copia a comercial si existe
        $ccEmails = [];
        if ($inmueble) {
            $idComercial = (int)($inmueble['comercial_id'] ?? 0);
            if ($idComercial > 0) {
                // Necesitamos el email del comercial. El modelo Inmueble suele hacer JOIN con usuarios.
                // Si el findById ya trae datos del comercial (nombre, email), perfecto.
                // Revisando Inmueble.php, el findById hace fetchAll pero parece ser un select simple o join básico.
                // Vamos a intentar obtener el email del usuario si no está en el array inmueble.
                if (!empty($inmueble['email_comercial'])) {
                    $ccEmails[] = $inmueble['email_comercial'];
                } else {
                    // Si no viene en el join, lo buscamos
                    $userModel = new User();
                    $comercial = $userModel->findById($idComercial);
                    if ($comercial && !empty($comercial->email)) {
                        $ccEmails[] = $comercial->email;
                    }
                }
            }
        }

        // Intento de envío
        try {
            // Enviar a la agencia principal
            // NOTA: MailService::send no soporta CC nativo en array de opciones según el análisis previo,
            // pero podemos enviar un segundo correo o modificar MailService. 
            // Para no tocar MailService (riesgo), enviaremos dos correos si hay comercial,
            // o si MailService soporta array en 'to' (generalmente PHPMailer wrap lo soporta, pero MailService::send tiene tipo string $to).
            // Solución segura: Enviar principal a Agencia, y si hay comercial, enviar otro aviso a él.
            
            // 1. A la Agencia
            MailService::send($toAgencia, $subject, [
                'template' => 'contacto_agencia',
                'data' => $templateData,
                'replyTo' => $input['email']
            ]);

            // 2. Al Comercial (si aplica)
            if (!empty($ccEmails)) {
                foreach ($ccEmails as $cc) {
                    try {
                        MailService::send($cc, "COPIA: $subject", [
                            'template' => 'contacto_agencia',
                            'data' => $templateData,
                            'replyTo' => $input['email']
                        ]);
                    } catch (Exception $e) {
                        // Si falla la copia al comercial, solo loguear, no fallar todo
                        $this->logContacto('SMTP_WARNING', ['msg' => 'Fallo envío copia comercial: ' . $e->getMessage()]);
                    }
                }
            }

            // 3. Al Cliente (Auto-respuesta)
            try {
                MailService::send($input['email'], 'Hemos recibido tu consulta', [
                    'template' => 'contacto_cliente',
                    'data' => $templateData
                ]);
                $this->logContacto('AUTO_REPLY_SENT', ['to' => $input['email']]);
            } catch (Exception $e) {
                // Si falla el auto-reply, no queremos mostrar error al usuario ni bloquear el éxito del form
                $this->logContacto('SMTP_WARNING', ['msg' => 'Fallo auto-reply cliente: ' . $e->getMessage()]);
            }

            $this->logContacto('EMAIL_SENT', ['to' => $toAgencia, 'cc' => implode(',', $ccEmails)]);

            // Vista de Éxito
            require VIEW . '/layouts/header.php';
            require VIEW . '/contacto/exito.php';
            require VIEW . '/layouts/footer.php';

        } catch (Exception $e) {
            $this->logContacto('SMTP_ERROR', ['msg' => $e->getMessage()]);
            
            // Mensaje amable user-facing
            $errorMessage = "Ha habido un problema al enviar tu solicitud. Por favor, inténtalo de nuevo más tarde o contacta con nosotros por teléfono o correo electrónico.";
            
            // Mostrar formulario de nuevo con el error global (no perder datos del usuario)
            $errors = ['general' => $errorMessage];
            $old = $input;
            $csrfToken = Csrf::token();
            
            require VIEW . '/layouts/header.php';
            require VIEW . '/contacto/form.php';
            require VIEW . '/layouts/footer.php';
        }
    }

    /**
     * Sistema de log personalizado para Contacto
     */
    private function logContacto(string $status, array $context = []): void
    {
        $logPath = ROOT . '/' . self::LOG_FILE;
        $dir = dirname($logPath);
        
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
        
        // Contexto a string
        $contextStr = json_encode($context, JSON_UNESCAPED_UNICODE);

        $line = "[$timestamp] [$ip] [$status] $contextStr | UA: $ua" . PHP_EOL;

        @file_put_contents($logPath, $line, FILE_APPEND | LOCK_EX);
    }
}
