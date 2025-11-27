<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;

/**
 * Controlador de Autenticación
 * Gestiona Login, Logout y validación de credenciales.
 */
class AuthController
{
    /**
     * Muestra el formulario de login.
     * GET /login
     */
    public function login(): void
    {
        // Si ya está logueado, redirigir al dashboard
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }

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

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            // Podrías guardar error en sesión flash
            echo "Email y contraseña son obligatorios.";
            return;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user->password_hash)) {
            // Login correcto
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user->id_usuario;
            $_SESSION['user_name'] = $user->nombre;
            $_SESSION['user_role'] = $user->rol;

            header('Location: /dashboard');
            exit;
        } else {
            // Login incorrecto
            echo "Credenciales incorrectas.";
            // O redirigir con error: header('Location: /login?error=1');
        }
    }

    /**
     * Cierra la sesión del usuario.
     * GET /logout
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Destruir todas las variables de sesión
        $_SESSION = [];

        // Borrar la cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destruir la sesión
        session_destroy();

        header('Location: /');
        exit;
    }
}
