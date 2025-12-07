<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Inmueble;
use App\Models\Cliente;
use App\Models\User;
use App\Core\Database;
use PDO;

final class InmuebleController
{
    private Inmueble $inmuebles;
    private Cliente $clientes;

    public function __construct()
    {
        $this->requireAuth();
        $this->requireRole(['admin', 'coordinador', 'comercial']);

        $this->inmuebles = new Inmueble();
        $this->clientes  = new Cliente();
    }

    public function index(): void
    {
        $filters = [
            'ref'           => trim((string)($_GET['ref'] ?? '')),
            'tipo'          => trim((string)($_GET['tipo'] ?? '')),
            'estado'        => trim((string)($_GET['estado'] ?? '')),
            'operacion'     => trim((string)($_GET['operacion'] ?? '')),
            'propietario_id'=> trim((string)($_GET['propietario_id'] ?? '')),
            'localidad'     => trim((string)($_GET['localidad'] ?? '')),
        ];

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;

        $result = $this->inmuebles->paginateAdmin($filters, $page, $perPage);
        $propietarios = $this->clientes->listForSelect();
        $csrfToken = $this->csrfToken();

        require VIEW . '/admin/inmuebles/index.php';
    }

    public function create(): void
    {
        $propietarioId = (int)($_GET['propietario_id'] ?? 0);
        $propietarioPre = null;
        
        if ($propietarioId > 0) {
            $propietarioPre = $this->clientes->findById($propietarioId);
        }

        $propietarios = $this->clientes->listForSelect();
        $comerciales  = $this->getComerciales(); // Helper
        $csrfToken = $this->csrfToken();
        $errors = [];
        $old = [];

        // Pre-fill propietario_id if passed
        if ($propietarioId > 0) {
            $old['propietario_id'] = $propietarioId;
        }

        require VIEW . '/admin/inmuebles/form.php';
    }

    public function store(): void
    {
        $this->ensurePost();
        if (!$this->csrfValidate($_POST['csrf_token'] ?? '')) {
            $this->redirect('/admin/inmuebles?error=csrf');
        }

        [$data, $errors] = $this->validateInput($_POST);

        // propietario existe
        if (empty($errors['propietario_id'])) {
            $prop = $this->clientes->findById((int)$data['propietario_id']);
            if (!$prop) {
                $errors['propietario_id'] = 'Propietario no válido.';
            }
        }

        // ref única
        if (empty($errors['ref'])) {
            $existing = $this->inmuebles->findByRef($data['ref']);
            if ($existing) {
                $errors['ref'] = 'La referencia (ref) ya existe.';
            }
        }

        if ($errors) {
            $propietarioId = (int)($data['propietario_id'] ?? 0);
            $propietarioPre = null;
            if ($propietarioId > 0) {
                $propietarioPre = $this->clientes->findById($propietarioId);
            }

            $propietarios = $this->clientes->listForSelect();
            $comerciales  = $this->getComerciales();
            $csrfToken = $this->csrfToken();
            $old = $data;
            require VIEW . '/admin/inmuebles/form.php';
            return;
        }

        // comercial_id: si no viene, por defecto el logado
        if (!isset($data['comercial_id']) || $data['comercial_id'] === null) {
            $data['comercial_id'] = $this->currentUserId();
        }

        $ok = $this->inmuebles->create($data);
        $this->redirect($ok ? '/admin/inmuebles?msg=created' : '/admin/inmuebles?error=db');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/admin/inmuebles?error=badid');
        }

        $inmueble = $this->inmuebles->findById($id);
        if (!$inmueble) {
            $this->redirect('/admin/inmuebles?error=notfound');
        }

        $propietarios = $this->clientes->listForSelect();
        $comerciales  = $this->getComerciales();
        $csrfToken = $this->csrfToken();
        $errors = [];
        $old = $this->rowToArray($inmueble);

        require VIEW . '/admin/inmuebles/form.php';
    }

    public function update(): void
    {
        $this->ensurePost();
        if (!$this->csrfValidate($_POST['csrf_token'] ?? '')) {
            $this->redirect('/admin/inmuebles?error=csrf');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/admin/inmuebles?error=badid');
        }

        $current = $this->inmuebles->findById($id);
        if (!$current) {
            $this->redirect('/admin/inmuebles?error=notfound');
        }

        [$data, $errors] = $this->validateInput($_POST);

        // propietario existe
        if (empty($errors['propietario_id'])) {
            $prop = $this->clientes->findById((int)$data['propietario_id']);
            if (!$prop) {
                $errors['propietario_id'] = 'Propietario no válido.';
            }
        }

        // ref única (ignorando el propio)
        if (empty($errors['ref'])) {
            $existing = $this->inmuebles->findByRef($data['ref']);
            if ($existing) {
                $existingId = (int)$this->getField($existing, 'id_inmueble');
                if ($existingId !== $id) {
                    $errors['ref'] = 'La referencia (ref) ya existe.';
                }
            }
        }

        if ($errors) {
            $propietarios = $this->clientes->listForSelect();
            $comerciales  = $this->getComerciales();
            $csrfToken = $this->csrfToken();
            $inmueble = $current;
            $old = $data;
            require VIEW . '/admin/inmuebles/form.php';
            return;
        }

        $ok = $this->inmuebles->update($id, $data);
        $this->redirect($ok ? '/admin/inmuebles?msg=updated' : '/admin/inmuebles?error=db');
    }

    public function delete(): void
    {
        $this->ensurePost();
        if (!$this->csrfValidate($_POST['csrf_token'] ?? '')) {
            $this->redirect('/admin/inmuebles?error=csrf');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/admin/inmuebles?error=badid');
        }

        $ok = $this->inmuebles->delete($id);
        $this->redirect($ok ? '/admin/inmuebles?msg=deleted' : '/admin/inmuebles?error=db');
    }

    // -----------------------------
    // Helpers
    // -----------------------------
    private function validateInput(array $src): array
    {
        $errors = [];

        $ref       = trim((string)($src['ref'] ?? ''));
        $tipo      = trim((string)($src['tipo'] ?? '')); // lowercase
        $operacion = trim((string)($src['operacion'] ?? ''));
        $estado    = trim((string)($src['estado'] ?? ''));

        // Enums reales
        $allowedTipo = ['piso','casa','chalet','adosado','duplex','local','oficina','terreno','otros'];
        $allowedEstado = ['borrador','activo','reservado','vendido','retirado'];
        $allowedOperacion = ['venta','alquiler','vacacional'];

        if ($ref === '' || mb_strlen($ref) > 20) $errors['ref'] = 'Ref obligatoria (máx 20).';
        if (!in_array($tipo, $allowedTipo, true)) $errors['tipo'] = 'Tipo no válido.';
        if (!in_array($operacion, $allowedOperacion, true)) $errors['operacion'] = 'Operación no válida.';
        if (!in_array($estado, $allowedEstado, true)) $errors['estado'] = 'Estado no válido.';

        $precio = $this->toMoney($src['precio'] ?? null);
        if ($precio === null || $precio <= 0) {
            $errors['precio'] = 'Precio requerido y > 0.';
        }

        $propietarioId = (int)($src['propietario_id'] ?? 0);
        if ($propietarioId <= 0) $errors['propietario_id'] = 'Propietario obligatorio.';

        $cp = trim((string)($src['cp'] ?? ''));
        if ($cp !== '' && !preg_match('/^\d{5}$/', $cp)) {
            $errors['cp'] = 'CP no válido (5 dígitos).';
        }

        // Flags activo/archivado (checkboxes)
        $activo = isset($src['activo']) ? 1 : 0;
        $archivado = isset($src['archivado']) ? 1 : 0;
        
        // Campos NOT NULL
        $direccion = trim((string)($src['direccion'] ?? ''));
        $localidad = trim((string)($src['localidad'] ?? ''));
        $provincia = trim((string)($src['provincia'] ?? ''));
        
        if ($direccion === '') $errors['direccion'] = 'Dirección obligatoria.';
        if ($localidad === '') $errors['localidad'] = 'Localidad obligatoria.';
        if ($provincia === '') $errors['provincia'] = 'Provincia obligatoria.';
        
        // Campos opcionales nuevos
        $superficie = trim((string)($src['superficie'] ?? ''));
        $habitaciones = trim((string)($src['habitaciones'] ?? ''));
        $banos = trim((string)($src['banos'] ?? ''));
        $descripcion = trim((string)($src['descripcion'] ?? ''));

        $data = [
            'ref'            => $ref,
            'direccion'      => $direccion,
            'localidad'      => $localidad,
            'provincia'      => $provincia,
            'cp'             => $cp ?: null,
            'tipo'           => $tipo,
            'operacion'      => $operacion,
            'precio'         => $precio,
            'estado'         => $estado,
            'activo'         => $activo,
            'archivado'      => $archivado,
            'propietario_id' => $propietarioId,
            'superficie'     => $superficie === '' ? null : (int)$superficie,
            'habitaciones'   => $habitaciones === '' ? null : (int)$habitaciones,
            'banos'          => $banos === '' ? null : (int)$banos,
            'descripcion'    => $descripcion === '' ? null : $descripcion,
        ];

        // comercial_id opcional
        if (isset($src['comercial_id']) && $src['comercial_id'] !== '') {
            $data['comercial_id'] = (int)$src['comercial_id'];
        } else {
            $data['comercial_id'] = null;
        }

        return [$data, $errors];
    }

    private function toMoney(mixed $v): ?float
    {
        if ($v === null) return null;
        $s = str_replace(',', '.', trim((string)$v));
        if ($s === '') return null;
        if (!is_numeric($s)) return null;
        return (float)$s;
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
        // AuthController usa 'user_id'
        $uid = $_SESSION['user_id'] ?? $_SESSION['user']['id_usuario'] ?? $_SESSION['id_usuario'] ?? null;
        return $uid ? (int)$uid : null;
    }

    private function currentUserRole(): ?string
    {
        // AuthController usa 'user_role'
        $rol = $_SESSION['user_role'] ?? $_SESSION['user']['rol'] ?? $_SESSION['rol'] ?? null;
        return $rol ? (string)$rol : null;
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

    private function getField(object|array $row, string $key): mixed
    {
        return is_array($row) ? ($row[$key] ?? null) : ($row->$key ?? null);
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
}
