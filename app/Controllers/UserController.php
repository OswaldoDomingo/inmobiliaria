<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Models\User;

/**
 * Controlador de Usuarios
 * Gestion CRUD de usuarios (Solo Admin).
 */
class UserController
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
     * Lista todos los usuarios.
     * GET /admin/usuarios
     */
    public function index(): void
    {
        $userModel = new User();
        $users = $userModel->getAll();
        $csrfToken = Csrf::token();

        require VIEW . '/admin/users/index.php';
    }

    /**
     * Muestra el formulario de creacion.
     * GET /admin/usuarios/nuevo
     */
    public function create(): void
    {
        $csrfToken = Csrf::token();
        require VIEW . '/admin/users/create.php';
    }

    /**
     * Guarda un nuevo usuario.
     * POST /admin/usuarios/guardar
     */
    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/usuarios');
            exit;
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $errors = ["Sesion expirada. Vuelve a intentarlo."];
            $csrfToken = Csrf::token();
            require VIEW . '/admin/users/create.php';
            return;
        }

        // 1. Sanitizacion
        $nombre = trim(strip_tags($_POST['nombre'] ?? ''));
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $password = $_POST['password'] ?? '';
        $rol = trim(strip_tags($_POST['rol'] ?? ''));

        $errors = [];

        // 2. Validacion
        if (empty($nombre)) {
            $errors[] = "El nombre es obligatorio.";
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es valido.";
        }

        if (strlen($password) < 6) {
            $errors[] = "La contrasena debe tener al menos 6 caracteres.";
        }

        $validRoles = ['admin', 'coordinador', 'comercial'];
        if (!in_array($rol, $validRoles, true)) {
            $errors[] = "El rol seleccionado no es valido.";
        }

        // Verificar unicidad del email
        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $errors[] = "El email ya esta registrado.";
        }

        // Si hay errores, volver al formulario
        if (!empty($errors)) {
            $csrfToken = Csrf::token();
            require VIEW . '/admin/users/create.php';
            return;
        }

        // 3. Guardado
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'nombre'        => $nombre,
            'email'         => $email,
            'password_hash' => $passwordHash,
            'rol'           => $rol,
            'activo'        => 1
        ];

        if ($userModel->create($data)) {
            header('Location: /admin/usuarios?msg=created');
            exit;
        } else {
            $errors[] = "Error al guardar en la base de datos.";
            $csrfToken = Csrf::token();
            require VIEW . '/admin/users/create.php';
        }
    }

    /**
     * Muestra el formulario de edicion.
     * GET /admin/usuarios/editar?id={id}
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            header('Location: /admin/usuarios');
            exit;
        }

        $userModel = new User();
        $user = $userModel->findById((int)$id);

        if (!$user) {
            header('Location: /admin/usuarios?error=notfound');
            exit;
        }

        // Preparamos variables para la vista
        $nombre = $user->nombre;
        $email = $user->email;
        $rol = $user->rol;
        $id_usuario = $user->id_usuario;
        $csrfToken = Csrf::token();

        require VIEW . '/admin/users/edit.php';
    }

    /**
     * Actualiza un usuario existente.
     * POST /admin/usuarios/actualizar
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/usuarios');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            header('Location: /admin/usuarios');
            exit;
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            $errors = ["Sesion expirada. Vuelve a intentarlo."];
            $id_usuario = (int)$id;
            $csrfToken = Csrf::token();
            require VIEW . '/admin/users/edit.php';
            return;
        }

        // 1. Sanitizacion
        $nombre = trim(strip_tags($_POST['nombre'] ?? ''));
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $password = $_POST['password'] ?? '';
        $rol = trim(strip_tags($_POST['rol'] ?? ''));

        $errors = [];

        // 2. Validacion
        if (empty($nombre)) {
            $errors[] = "El nombre es obligatorio.";
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El email no es valido.";
        }

        // Contrasena opcional en edicion
        if (!empty($password) && strlen($password) < 6) {
            $errors[] = "La contrasena debe tener al menos 6 caracteres.";
        }

        $validRoles = ['admin', 'coordinador', 'comercial'];
        if (!in_array($rol, $validRoles, true)) {
            $errors[] = "El rol seleccionado no es valido.";
        }

        // Verificar unicidad del email (excluyendo al propio usuario)
        $userModel = new User();
        $existingUser = $userModel->findByEmail($email);
        if ($existingUser && (int)$existingUser->id_usuario !== (int)$id) {
            $errors[] = "El email ya esta registrado por otro usuario.";
        }

        // Si hay errores, volver al formulario
        if (!empty($errors)) {
            $id_usuario = $id; 
            $csrfToken = Csrf::token();
            require VIEW . '/admin/users/edit.php';
            return;
        }

        // 3. Preparar datos
        $data = [
            'nombre' => $nombre,
            'email'  => $email,
            'rol'    => $rol
        ];

        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($userModel->update((int)$id, $data)) {
            header('Location: /admin/usuarios?msg=updated');
            exit;
        } else {
            $errors[] = "Error al actualizar en la base de datos.";
            $id_usuario = $id;
            $csrfToken = Csrf::token();
            require VIEW . '/admin/users/edit.php';
        }
    }

    /**
     * Da de baja a un usuario (Soft Delete).
     * POST /admin/usuarios/baja
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/usuarios');
            exit;
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            header('Location: /admin/usuarios?error=csrf');
            exit;
        }

        $id = $_POST['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            header('Location: /admin/usuarios');
            exit;
        }

        // Proteccion anti-suicidio
        if ((int)$id === (int)$_SESSION['user_id']) {
            header('Location: /admin/usuarios?error=selfdelete');
            exit;
        }

        $userModel = new User();
        if ($userModel->softDelete((int)$id)) {
            header('Location: /admin/usuarios?msg=deleted');
            exit;
        } else {
            header('Location: /admin/usuarios?error=db');
            exit;
        }
    }

    /**
     * Cambia el estado de bloqueo de un usuario.
     * POST /admin/usuarios/cambiar-bloqueo
     */
    public function toggleBlock(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/usuarios');
            exit;
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            header('Location: /admin/usuarios?error=csrf');
            exit;
        }

        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null; // 1 para bloquear, 0 para desbloquear

        if (!$id || !is_numeric($id) || !is_numeric($status)) {
            header('Location: /admin/usuarios');
            exit;
        }

        // Proteccion: No bloquearse a uno mismo
        if ((int)$id === (int)$_SESSION['user_id']) {
            header('Location: /admin/usuarios?error=selfblock');
            exit;
        }

        $userModel = new User();
        if ($userModel->toggleBlock((int)$id, (int)$status)) {
            $msg = ((int)$status === 1) ? 'blocked' : 'unblocked';
            header("Location: /admin/usuarios?msg=$msg");
            exit;
        } else {
            header('Location: /admin/usuarios?error=db');
            exit;
        }
    }
}