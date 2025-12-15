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
     * Listo todos los usuarios.
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
     * Muestro el formulario de creacion.
     * GET /admin/usuarios/nuevo
     */
    public function create(): void
    {
        $csrfToken = Csrf::token();
        require VIEW . '/admin/users/create.php';
    }

    /**
     * Guardo un nuevo usuario.
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

        // 1. Sanitizo
        $nombre = trim(strip_tags($_POST['nombre'] ?? ''));
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $telefono = trim(strip_tags($_POST['telefono'] ?? ''));
        $password = $_POST['password'] ?? '';
        $rol = trim(strip_tags($_POST['rol'] ?? ''));

        $errors = [];

        // 2. Valido
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

        // Verifico unicidad del email
        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $errors[] = "El email ya esta registrado.";
        }

        // Si hay errores, vuelvo al formulario
        if (!empty($errors)) {
            $csrfToken = Csrf::token();
            require VIEW . '/admin/users/create.php';
            return;
        }

        try {
            // 3. Subo la Imagen - Llamar a handleFileUpload cuando hay archivo y error != UPLOAD_ERR_NO_FILE
            $foto_perfil = null;
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE) {
                $foto_perfil = $this->handleFileUpload($_FILES['foto_perfil']);
            }

            // 4. Guardo en Base de Datos
            $data = [
                'nombre'        => $nombre,
                'email'         => $email,
                'telefono'      => !empty($telefono) ? $telefono : null,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'rol'           => $rol,
                'foto_perfil'   => $foto_perfil,
                'activo'        => 1
            ];

            if ($userModel->create($data)) {
                header('Location: /admin/usuarios');
                exit;
            } else {
                $errors[] = "Error al guardar en la base de datos.";
                $csrfToken = Csrf::token();
                require VIEW . '/admin/users/create.php';
            }

        } catch (\Exception $e) {
            // Capturar excepciones y mostrar error inline sin redirigir
            $errors[] = $e->getMessage();
            $csrfToken = Csrf::token();
            // Mantener datos del formulario: $nombre, $email, $telefono, $password, $rol ya están definidos
            require VIEW . '/admin/users/create.php';
            return;
        }
    }

    /**
     * Muestro el formulario de edicion.
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

        // Preparo variables para la vista
        $nombre = $user->nombre;
        $email = $user->email;
        $telefono = $user->telefono ?? '';
        $rol = $user->rol;
        $id_usuario = $user->id_usuario;
        $csrfToken = Csrf::token();

        require VIEW . '/admin/users/edit.php';
    }

    /**
     * Actualizo un usuario existente.
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

        // 1. Sanitizo
        $nombre = trim(strip_tags($_POST['nombre'] ?? ''));
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $telefono = trim(strip_tags($_POST['telefono'] ?? ''));
        $password = $_POST['password'] ?? '';
        $rol = trim(strip_tags($_POST['rol'] ?? ''));

        $errors = [];

        // 2. Valido
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

        // Verifico unicidad del email (excluyendo al propio usuario)
        $userModel = new User();
        $existingUser = $userModel->findByEmail($email);
        if ($existingUser && (int)$existingUser->id_usuario !== (int)$id) {
            $errors[] = "El email ya esta registrado por otro usuario.";
        }

        // Si hay errores, vuelvo al formulario
        if (!empty($errors)) {
            $user = (object)[
                'id_usuario' => $id,
                'nombre' => $nombre,
                'email' => $email,
                'rol' => $rol
            ];
            $csrfToken = Csrf::token(); // Ensure CSRF token is available for the form
            require VIEW . '/admin/users/edit.php';
            return;
        }

        try {
            // Preparo datos para actualizar
            $data = [
                'nombre'   => $nombre,
                'email'    => $email,
                'telefono' => !empty($telefono) ? $telefono : null,
                'rol'      => $rol
            ];

            // Si se escribió contraseña, la actualizo
            if (!empty($password)) {
                $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            // Manejo de Imagen - Llamar a handleFileUpload cuando hay archivo y error != UPLOAD_ERR_NO_FILE
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] !== UPLOAD_ERR_NO_FILE) {
                $newFoto = $this->handleFileUpload($_FILES['foto_perfil']);
                
                if ($newFoto) {
                    $data['foto_perfil'] = $newFoto;

                    // Borro foto antigua si existe SOLO DESPUÉS de que el update sea exitoso
                    $currentUser = $userModel->findById((int)$id);
                    if ($currentUser && !empty($currentUser->foto_perfil)) {
                        $oldFile = ROOT . '/public/uploads/profiles/' . $currentUser->foto_perfil;
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }
                }
            }

            if ($userModel->update((int)$id, $data)) {
                header('Location: /admin/usuarios?msg=updated');
                exit;
            } else {
                $errors[] = "Error al actualizar en la base de datos.";
                $user = $userModel->findById((int)$id); // Re-fetch user to populate form
                $csrfToken = Csrf::token(); // Ensure CSRF token is available for the form
                require VIEW . '/admin/users/edit.php';
            }

        } catch (\Exception $e) {
            // Capturar excepciones y mostrar error inline sin redirigir
            $errors[] = $e->getMessage();
            
            // Re-fetch user para tener foto_perfil y otros datos de BBDD
            $user = $userModel->findById((int)$id);
            
            // Preparar todas las variables necesarias para la vista
            $id_usuario = (int)$id;
            // $nombre, $email, $telefono, $rol ya están definidos con los valores del POST
            $csrfToken = Csrf::token();
            
            // Renderizar la vista directamente con el error
            require VIEW . '/admin/users/edit.php';
            return;
        }
    }

    /**
     * Doy de baja a un usuario (Soft Delete).
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
     * Cambio el estado de bloqueo de un usuario.
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
    /**
     * Manejo la subida de archivos de forma segura.
     * 
     * @param array $file Archivo desde $_FILES
     * @return string|null Nombre del archivo guardado o null si no se subió nada
     * @throws \Exception Si hay error de validación o subida
     */
    private function handleFileUpload(array $file): ?string
    {
        // 1. Verifico si se subió un archivo
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        // 2. Verifico errores de subida
        if ($file['error'] !== UPLOAD_ERR_OK) {
            // Mapeo errores comunes a mensajes amigables
            if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
                throw new \Exception("La imagen es demasiado pesada. Máximo 2MB.");
            }
            throw new \Exception("Error al subir el archivo. Código: " . $file['error']);
        }

        // 3. Valido tamaño (Max 2MB) - Defensa extra
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new \Exception("La imagen es demasiado pesada. Máximo 2MB.");
        }

        // 4. Valido tipo MIME real
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!in_array($mime, $allowedMimes)) {
            throw new \Exception("Formato de imagen no permitido. Solo JPG, PNG o WEBP.");
        }

        // 5. Genero nombre único y seguro
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('profile_', true) . '.' . $ext;

        // 6. Creo directorio si no existe
        $uploadDir = ROOT . '/public/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new \Exception("No se pudo crear el directorio de subidas.");
            }
        }

        // 7. Muevo archivo
        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            throw new \Exception("Error al guardar la imagen en el servidor.");
        }

        return $filename;
    }
}