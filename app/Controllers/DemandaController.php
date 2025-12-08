<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Demanda;
use App\Models\Cliente;
use App\Models\User;
use App\Core\Database;
use PDO;

final class DemandaController
{
    private Demanda $demandas;
    private Cliente $clientes;
    private User $usuarios;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador', 'comercial']);

        $this->demandas = new Demanda();
        $this->clientes = new Cliente();
        $this->usuarios = new User();
    }

    public function index(): void
    {
        $filtros = [
            'tipo_operacion' => trim((string)($_GET['tipo_operacion'] ?? '')),
            'estado' => trim((string)($_GET['estado'] ?? '')),
            'comercial_id' => trim((string)($_GET['comercial_id'] ?? '')),
        ];

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;

        $userId = $this->currentUserId();
        $rol = $this->currentUserRole();

        $result = $this->demandas->paginateAdmin($userId, $rol, $filtros, $page, $perPage);
        
        $comerciales = $this->isAdminOrCoordinador($rol) ? $this->getComerciales() : [];
        $csrfToken = $this->csrfToken();

        require VIEW . '/admin/demandas/index.php';
    }

    public function create(): void
    {
        $clienteId = (int)($_GET['cliente_id'] ?? 0);
        $cliente = null;
        $rol = $this->currentUserRole();
        $userId = $this->currentUserId();

        if ($clienteId > 0) {
            $cliente = $this->clientes->findById($clienteId);
            
            // Verificar permisos: comercial solo puede crear demandas para sus clientes
            if (!$this->isAdminOrCoordinador($rol)) {
                if (!$cliente || (int)$cliente->usuario_id !== $userId) {
                    $this->redirect('/admin/demandas?error=forbidden');
                }
            }
        }

        $returnTo = $this->validateReturnTo($_GET['return_to'] ?? null);
        
        // Comerciales solo ven sus clientes, admin/coordinador ven todos
        if ($this->isAdminOrCoordinador($rol)) {
            $rol = $this->currentUserRole();
            $userId = $this->currentUserId();
            if ($this->isAdminOrCoordinador($rol)) {
                $clientes = $this->clientes->listForSelect();
            } else {
                $clientes = $this->getClientesDelComercial($userId);
            }
        } else {
            $clientes = $this->getClientesDelComercial($userId);
        }
        
        $comerciales = $this->getComerciales();
        $csrfToken = $this->csrfToken();
        $errors = [];
        $old = [];

        if ($clienteId > 0) {
            $old['cliente_id'] = $clienteId;
        }

        require VIEW . '/admin/demandas/form.php';
    }

    public function store(): void
    {
        $this->ensurePost();

        $returnTo = $this->validateReturnTo($_POST['return_to'] ?? $_GET['return_to'] ?? null);

        if (!$this->csrfValidate($_POST['csrf_token'] ?? '')) {
            $this->redirect('/admin/demandas?error=csrf');
        }

        [$data, $errors] = $this->validateInput($_POST);

        // Verificar que el cliente existe y pertenece al comercial (si es comercial)
        if (empty($errors['cliente_id'])) {
            $cliente = $this->clientes->findById((int)$data['cliente_id']);
            if (!$cliente) {
                $errors['cliente_id'] = 'Cliente no válido.';
            } else {
                // Control por rol: comercial solo puede crear demandas para sus clientes
                if (!$this->isAdminOrCoordinador($this->currentUserRole())) {
                    if ((int)$cliente->usuario_id !== $this->currentUserId()) {
                        $errors['cliente_id'] = 'No tienes permiso para crear demandas para este cliente.';
                    }
                }
            }
        }

        if ($errors) {
            $clienteId = (int)($data['cliente_id'] ?? 0);
            $cliente = $clienteId > 0 ? $this->clientes->findById($clienteId) : null;
            $rol = $this->currentUserRole();
            $userId = $this->currentUserId();
            if ($this->isAdminOrCoordinador($rol)) {
                $clientes = $this->clientes->listForSelect();
            } else {
                $clientes = $this->getClientesDelComercial($userId);
            }
            $comerciales = $this->getComerciales();
            $csrfToken = $this->csrfToken();
            $old = $data;
            require VIEW . '/admin/demandas/form.php';
            return;
        }

        // Asignar comercial_id según reglas de negocio
        $rol = $this->currentUserRole();
        if ($this->isAdminOrCoordinador($rol)) {
            // Admin/coordinador: heredar del cliente
            $cliente = $this->clientes->findById((int)$data['cliente_id']);
            $data['comercial_id'] = $cliente->usuario_id ?? null;
        } else {
            // Comercial: forzar el ID del usuario logueado
            $data['comercial_id'] = $this->currentUserId();
        }

        $ok = $this->demandas->create($data);

        if ($ok) {
            $destination = $returnTo ?: '/admin/demandas';
            $destination = $this->addQueryParam($destination, 'msg', 'created');
            $this->redirect($destination);
        } else {
            $this->redirect('/admin/demandas?error=db');
        }
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/admin/demandas?error=badid');
        }

        $demanda = $this->demandas->findById($id);
        if (!$demanda) {
            $this->redirect('/admin/demandas?error=notfound');
        }

        $rol = $this->currentUserRole();
        $userId = $this->currentUserId();

        // Control por rol: comercial solo puede editar demandas de sus clientes
        if (!$this->isAdminOrCoordinador($rol)) {
            $cliente = $this->clientes->findById((int)$demanda->cliente_id);
            if (!$cliente || (int)$cliente->usuario_id !== $userId) {
                $this->redirect('/admin/demandas?error=forbidden');
            }
        }

        $returnTo = $this->validateReturnTo($_GET['return_to'] ?? null);
        $cliente = $this->clientes->findById((int)$demanda->cliente_id);
        
        // Comerciales solo ven sus clientes, admin/coordinador ven todos
        if ($this->isAdminOrCoordinador($rol)) {
            $rol = $this->currentUserRole();
            $userId = $this->currentUserId();
            if ($this->isAdminOrCoordinador($rol)) {
                $clientes = $this->clientes->listForSelect();
            } else {
                $clientes = $this->getClientesDelComercial($userId);
            }
        } else {
            $clientes = $this->getClientesDelComercial($userId);
        }
        
        $comerciales = $this->getComerciales();
        $csrfToken = $this->csrfToken();
        $errors = [];
        $old = $this->rowToArray($demanda);

        require VIEW . '/admin/demandas/form.php';
    }

    public function update(): void
    {
        $this->ensurePost();

        if (!$this->csrfValidate($_POST['csrf_token'] ?? '')) {
            $this->redirect('/admin/demandas?error=csrf');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/admin/demandas?error=badid');
        }

        $current = $this->demandas->findById($id);
        if (!$current) {
            $this->redirect('/admin/demandas?error=notfound');
        }

        $rol = $this->currentUserRole();
        $userId = $this->currentUserId();

        // Control por rol: comercial solo puede editar demandas de sus clientes
        if (!$this->isAdminOrCoordinador($rol)) {
            $cliente = $this->clientes->findById((int)$current->cliente_id);
            if (!$cliente || (int)$cliente->usuario_id !== $userId) {
                $this->redirect('/admin/demandas?error=forbidden');
            }
        }

        $returnTo = $this->validateReturnTo($_POST['return_to'] ?? null);

        [$data, $errors] = $this->validateInput($_POST);

        // Verificar que el cliente existe
        if (empty($errors['cliente_id'])) {
            $cliente = $this->clientes->findById((int)$data['cliente_id']);
            if (!$cliente) {
                $errors['cliente_id'] = 'Cliente no válido.';
            }
        }

        if ($errors) {
            $demanda = $current;
            $cliente = $this->clientes->findById((int)$current->cliente_id);
            $rol = $this->currentUserRole();
            $userId = $this->currentUserId();
            if ($this->isAdminOrCoordinador($rol)) {
                $clientes = $this->clientes->listForSelect();
            } else {
                $clientes = $this->getClientesDelComercial($userId);
            }
            $comerciales = $this->getComerciales();
            $csrfToken = $this->csrfToken();
            $old = $data;
            require VIEW . '/admin/demandas/form.php';
            return;
        }

        // Asignar comercial_id según reglas de negocio (mismo que en store)
        $rol = $this->currentUserRole();
        if ($this->isAdminOrCoordinador($rol)) {
            $cliente = $this->clientes->findById((int)$data['cliente_id']);
            $data['comercial_id'] = $cliente->usuario_id ?? null;
        } else {
            $data['comercial_id'] = $this->currentUserId();
        }

        $ok = $this->demandas->update($id, $data);

        $destination = $returnTo ?: '/admin/demandas';
        $destination = $this->addQueryParam($destination, 'msg', 'updated');

        $this->redirect($ok ? $destination : '/admin/demandas?error=db');
    }

    public function delete(): void
    {
        $this->ensurePost();
        
        if (!$this->csrfValidate($_POST['csrf_token'] ?? '')) {
            $this->redirect('/admin/demandas?error=csrf');
        }

        // Solo admin/coordinador
        if (!$this->isAdminOrCoordinador($this->currentUserRole())) {
            $this->redirect('/admin/demandas?error=forbidden');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/admin/demandas?error=badid');
        }

        $ok = $this->demandas->delete($id);
        $this->redirect($ok ? '/admin/demandas?msg=deleted' : '/admin/demandas?error=db');
    }

    // -----------------------------
    // Helpers de validación
    // -----------------------------
    private function validateInput(array $src): array
    {
        $errors = [];

        $clienteId = (int)($src['cliente_id'] ?? 0);
        if ($clienteId <= 0) {
            $errors['cliente_id'] = 'Cliente obligatorio.';
        }

        $tipoOperacion = trim((string)($src['tipo_operacion'] ?? ''));
        $allowedTipos = ['compra', 'alquiler', 'vacacional'];
        if (!in_array($tipoOperacion, $allowedTipos, true)) {
            $errors['tipo_operacion'] = 'Tipo de operación no válido.';
        }

        $precioMin = $this->toMoney($src['rango_precio_min'] ?? null);
        $precioMax = $this->toMoney($src['rango_precio_max'] ?? null);

        // Validar que min <= max si ambos están presentes
        if ($precioMin !== null && $precioMax !== null && $precioMin > $precioMax) {
            $errors['rango_precio_max'] = 'El precio máximo no puede ser menor que el mínimo.';
        }

        $superficieMin = trim((string)($src['superficie_min'] ?? ''));
        $habitacionesMin = trim((string)($src['habitaciones_min'] ?? ''));
        $banosMin = trim((string)($src['banos_min'] ?? ''));
        $zonas = trim((string)($src['zonas'] ?? ''));

        $estado = trim((string)($src['estado'] ?? 'activa'));
        $allowedEstados = ['activa', 'en_gestion', 'pausada', 'archivada'];
        if (!in_array($estado, $allowedEstados, true)) {
            $errors['estado'] = 'Estado no válido.';
        }

        // Características (checkboxes → array de strings)
        $caracteristicas = [];
        $availableCaracteristicas = ['garaje', 'piscina', 'ascensor', 'terraza', 'amueblado', 'trastero', 'jardin'];
        foreach ($availableCaracteristicas as $car) {
            if (isset($src['caracteristica_' . $car])) {
                $caracteristicas[] = $car;
            }
        }

        $data = [
            'cliente_id' => $clienteId,
            'tipo_operacion' => $tipoOperacion,
            'rango_precio_min' => $precioMin,
            'rango_precio_max' => $precioMax,
            'superficie_min' => $superficieMin === '' ? null : (int)$superficieMin,
            'habitaciones_min' => $habitacionesMin === '' ? null : (int)$habitacionesMin,
            'banos_min' => $banosMin === '' ? null : (int)$banosMin,
            'zonas' => $zonas === '' ? null : $zonas,
            'caracteristicas' => $caracteristicas, // siempre array, nunca null
            'estado' => $estado,
            'activo' => isset($src['activo']) ? 1 : 0,
            'archivado' => isset($src['archivado']) ? 1 : 0,
        ];

        return [$data, $errors];
    }

    // -----------------------------
    // Helpers copiados de InmuebleController
    // -----------------------------
    private function toMoney(mixed $v): ?int
    {
        if ($v === null) return null;
        $s = str_replace(',', '.', trim((string)$v));
        if ($s === '') return null;
        if (!is_numeric($s)) return null;
        return (int)$s;
    }

    private function ensurePost(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            exit;
        }
    }

    private function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    private function requireAuth(): void
    {
        $uid = $this->currentUserId();
        if (!$uid) $this->redirect('/login');
    }

    private function requireRole(array $roles): void
    {
        $rol = $this->currentUserRole();
        if (!$rol || !in_array($rol, $roles, true)) {
            http_response_code(403);
            echo '403 Forbidden';
            exit;
        }
    }

    private function currentUserId(): ?int
    {
        $uid = $_SESSION['user_id'] ?? $_SESSION['user']['id_usuario'] ?? $_SESSION['id_usuario'] ?? null;
        return $uid ? (int)$uid : null;
    }

    private function currentUserRole(): ?string
    {
        $rol = $_SESSION['user_role'] ?? $_SESSION['user']['rol'] ?? $_SESSION['rol'] ?? null;
        return $rol ? (string)$rol : null;
    }

    private function isAdminOrCoordinador(string $rol): bool
    {
        return in_array($rol, ['admin', 'coordinador'], true);
    }

    private function csrfToken(): string
    {
        $cls = \App\Core\Csrf::class;
        if (method_exists($cls, 'token'))    return (string)$cls::token();
        if (method_exists($cls, 'generate')) return (string)$cls::generate();
        if (method_exists($cls, 'getToken')) return (string)$cls::getToken();
        if (empty($_SESSION['_csrf'])) $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['_csrf'];
    }

    private function csrfValidate(string $token): bool
    {
        $cls = \App\Core\Csrf::class;
        if (method_exists($cls, 'validate')) return (bool)$cls::validate($token);
        if (method_exists($cls, 'check'))    return (bool)$cls::check($token);
        if (method_exists($cls, 'verify'))   return (bool)$cls::verify($token);
        return hash_equals((string)($_SESSION['_csrf'] ?? ''), (string)$token);
    }

    private function rowToArray(object|array $row): array
    {
        return is_array($row) ? $row : get_object_vars($row);
    }

    private function getComerciales(): array
    {
        try {
            $pdo = Database::conectar();
            $stmt = $pdo->query("SELECT id_usuario, nombre FROM usuarios WHERE activo = 1 ORDER BY nombre ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function validateReturnTo(?string $url): ?string
    {
        if (!$url || trim($url) === '') return null;
        
        $url = trim($url);
        
        if (!str_starts_with($url, '/admin/')) return null;
        if (preg_match('#^(https?:)?//#i', $url)) return null;
        
        return $url;
    }

    private function addQueryParam(string $url, string $key, string $value): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . urlencode($key) . '=' . urlencode($value);
    }

    /**
     * Obtiene solo los clientes asignados a un comercial específico
     */
    private function getClientesDelComercial(int $comercialId): array
    {
        try {
            $pdo = Database::conectar();
            $sql = "SELECT id_cliente, nombre, apellidos
                    FROM clientes
                    WHERE usuario_id = :comercial_id
                    ORDER BY nombre ASC, apellidos ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':comercial_id', $comercialId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ) ?: [];
        } catch (\Throwable $e) {
            return [];
        }
    }
}




