<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Models\Cliente;
use App\Models\User;

class ClienteController
{
    private Cliente $clienteModel;
    private User $userModel;
    private \App\Models\Inmueble $inmuebleModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->clienteModel = new Cliente();
        $this->userModel = new User();
        $this->inmuebleModel = new \App\Models\Inmueble();
    }

    public function index(): void
    {
        $rol = $this->getRol();
        $uid = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
        $clientes = $this->clienteModel->getAll($uid, $rol);
        $csrfToken = Csrf::token();

        require VIEW . '/admin/clientes/index.php';
    }

    public function create(): void
    {
        $rol = $this->getRol();
        $comerciales = $this->isAdminOrCoordinador($rol) ? $this->userModel->getComercialesActivos() : [];
        $csrfToken = Csrf::token();
        require VIEW . '/admin/clientes/create.php';
    }

    public function store(): void
    {
        $this->ensurePost();

        $rol = $this->getRol();
        $data = $this->sanitize($_POST);
        $data['usuario_id'] = $this->resolverAsignacionUsuario($rol, $_POST);
        $comerciales = $this->isAdminOrCoordinador($rol) ? $this->userModel->getComercialesActivos() : [];

        if ($this->clienteModel->findByDni($data['dni'])) {
            $errors = ["El DNI ya existe."];
            $csrfToken = Csrf::token();
            require VIEW . '/admin/clientes/create.php';
            return;
        }

        if ($this->clienteModel->create($data)) {
            header('Location: /admin/clientes?msg=created');
            exit;
        }

        $errors = ["No se pudo guardar el cliente."];
        $csrfToken = Csrf::token();
        require VIEW . '/admin/clientes/create.php';
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $cliente = $this->clienteModel->findById($id);

        if (!$cliente) {
            header('Location: /admin/clientes?error=notfound');
            exit;
        }

        $rol = $this->getRol();
        $comerciales = $this->isAdminOrCoordinador($rol) ? $this->userModel->getComercialesActivos() : [];
        
        // Obtener inmuebles del cliente
        $inmueblesCliente = $this->inmuebleModel->getByPropietario($id);

        $csrfToken = Csrf::token();
        require VIEW . '/admin/clientes/edit.php';
    }

    public function update(): void
    {
        $this->ensurePost();

        $id = (int)($_POST['id'] ?? 0);
        $cliente = $this->clienteModel->findById($id);

        if (!$cliente) {
            header('Location: /admin/clientes?error=notfound');
            exit;
        }

        $rol = $this->getRol();
        $comerciales = $this->isAdminOrCoordinador($rol) ? $this->userModel->getComercialesActivos() : [];
        $data = $this->sanitize($_POST);
        $data['usuario_id'] = $this->resolverAsignacionUsuario($rol, $_POST, (int)($cliente->usuario_id ?? 0));

        $existing = $this->clienteModel->findByDni($data['dni']);
        if ($existing && (int)$existing->id_cliente !== $id) {
            $errors = ["El DNI ya existe."];
            $csrfToken = Csrf::token();
            require VIEW . '/admin/clientes/edit.php';
            return;
        }

        if ($this->clienteModel->update($id, $data)) {
            header('Location: /admin/clientes?msg=updated');
            exit;
        }

        $errors = ["No se pudo actualizar el cliente."];
        $csrfToken = Csrf::token();
        require VIEW . '/admin/clientes/edit.php';
    }

    public function delete(): void
    {
        $this->ensurePost();

        $id = (int)($_POST['id'] ?? 0);

        if ($this->clienteModel->delete($id)) {
            header('Location: /admin/clientes?msg=deleted');
        } else {
            header('Location: /admin/clientes?error=has_properties');
        }
        exit;
    }

    private function ensurePost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/clientes');
            exit;
        }

        if (!Csrf::validate($_POST['csrf_token'] ?? '')) {
            header('Location: /admin/clientes?error=csrf');
            exit;
        }
    }

    private function sanitize(array $input): array
    {
        $clean = [];
        $clean['nombre']    = trim(strip_tags($input['nombre'] ?? ''));
        $clean['apellidos'] = trim(strip_tags($input['apellidos'] ?? ''));
        $clean['dni']       = trim(strip_tags($input['dni'] ?? '')) ?: null;
        $clean['email']     = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL) ?: null;
        $clean['telefono']  = trim(strip_tags($input['telefono'] ?? '')) ?: null;
        $clean['direccion'] = trim(strip_tags($input['direccion'] ?? '')) ?: null;
        $clean['notas']     = trim($input['notas'] ?? '') ?: null;
        $clean['activo']    = isset($input['activo']) ? (int)$input['activo'] : 1;
        return $clean;
    }

    private function getRol(): string
    {
        return $_SESSION['user_role'] ?? ($_SESSION['rol'] ?? 'comercial');
    }

    private function isAdminOrCoordinador(string $rol): bool
    {
        return in_array($rol, ['admin', 'coordinador'], true);
    }

    private function resolverAsignacionUsuario(string $rol, array $input, ?int $usuarioActual = null): ?int
    {
        if ($this->isAdminOrCoordinador($rol)) {
            return isset($input['usuario_id']) ? (int)$input['usuario_id'] : $usuarioActual;
        }

        if ($usuarioActual !== null) {
            return $usuarioActual;
        }

        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }
}
