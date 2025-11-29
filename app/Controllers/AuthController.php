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
            $_SESSION['user_role'] = $user->rol;

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

                $_SESSION['error'] = "Cuenta bloqueada.";
            } else {
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
}