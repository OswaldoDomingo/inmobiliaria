<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Models\User;

/**
 * Controlador de Autenticacion
 * Gestiona Login, Logout y validacion de credenciales.
 */
class AuthController
{
    /**
     * Muestra el formulario de login.
     * GET /login
     */
    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Si ya esta logueado, redirigir al dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

        $csrfToken = Csrf::token();

        require VIEW . '/auth/login.php';
    }

    /**
     * Procesa el formulario de login.
     * POST /login
     */
    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error'] = "Sesion expirada. Vuelve a intentarlo.";
            header('Location: /login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error'] = "Email y contrasena son obligatorios.";
            header('Location: /login');
            exit;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        // Si el usuario no existe, error generico
        if (!$user) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error'] = "Credenciales incorrectas.";
            header('Location: /login');
            exit;
        }

        // Paso A: Verificar Bloqueo Previo
        if ((int)$user->cuenta_bloqueada === 1) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error'] = "Su cuenta ha sido bloqueada por seguridad. Contacte con el administrador.";
            header('Location: /login');
            exit;
        }

        // Paso B: Verificar Credenciales
        if (password_verify($password, $user->password_hash)) {
            // Verificar si el usuario esta activo (logica existente)
            if ((int)$user->activo === 0 || (isset($user->archivado) && (int)$user->archivado === 1)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['error'] = "Usuario desactivado. Contacta con el administrador.";
                header('Location: /login');
                exit;
            }

            // Resetea intentos_fallidos a 0
            $pdo = \App\Core\Database::conectar();
            $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = 0 WHERE id_usuario = :id");
            $stmt->execute([':id' => $user->id_usuario]);

            // Inicia sesion normalmente
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Regenerar ID de sesion por seguridad
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user->id_usuario;
            $_SESSION['user_name'] = $user->nombre;
            $_SESSION['user_email'] = $user->email;
            $_SESSION['user_foto'] = $user->foto_perfil;
            $_SESSION['user_foto'] = $user->foto_perfil;
            $_SESSION['user_role'] = $user->rol;

            $this->registrarLog('LOGIN_EXITOSO', $email);

            header('Location: /dashboard');
            exit;
        } else {
            // Contrasena INCORRECTA
            $pdo = \App\Core\Database::conectar();
            
            // Incrementa el contador intentos_fallidos en +1
            $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE id_usuario = :id");
            $stmt->execute([':id' => $user->id_usuario]);

            // Obtener el nuevo valor de intentos
            $stmt = $pdo->prepare("SELECT intentos_fallidos FROM usuarios WHERE id_usuario = :id");
            $stmt->execute([':id' => $user->id_usuario]);
            $intentos = $stmt->fetchColumn();

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Comprobacion de Limite
            if ($intentos >= 3) {
                // Actualiza cuenta_bloqueada = 1
                $stmt = $pdo->prepare("UPDATE usuarios SET cuenta_bloqueada = 1 WHERE id_usuario = :id");
                $stmt->execute([':id' => $user->id_usuario]);

                $this->registrarLog('BLOQUEO_CUENTA', $email);
                $_SESSION['error'] = "Cuenta bloqueada.";
            } else {
                $this->registrarLog('LOGIN_FALLIDO', $email);
                $_SESSION['error'] = "Credenciales incorrectas.";
            }

            header('Location: /login');
            exit;
        }
    }

    /**
     * Cierra la sesion del usuario.
     * GET /logout
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Registrar logout antes de destruir sesion
        $email = $_SESSION['user_email'] ?? 'Desconocido';
        $this->registrarLog('LOGOUT', $email);

        // Destruir todas las variables de sesion
        $_SESSION = [];

        // Borrar la cookie de sesion si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destruir la sesion
        session_destroy();

        header('Location: /');
        exit;
    }

    /**
     * Registra un evento en el archivo de logs.
     * 
     * @param string $evento Tipo de evento (LOGIN_EXITOSO, LOGIN_FALLIDO, etc.)
     * @param string $email Email del usuario asociado al evento
     */
    private function registrarLog(string $evento, string $email): void
    {
        $logDir = ROOT . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $file = $logDir . '/auth.log';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $date = date('Y-m-d H:i:s');
        
        // Formato: FECHA|IP|EVENTO|EMAIL
        $logEntry = "$date|$ip|$evento|$email" . PHP_EOL;

        file_put_contents($file, $logEntry, FILE_APPEND);
    }
}