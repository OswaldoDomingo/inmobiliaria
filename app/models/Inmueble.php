<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

/**
 * Modelo Inmueble
 */
class Inmueble
{
    public function paginateAdmin(int $userId, string $rol, array $filters, int $page, int $perPage): array
    {
        $pdo = Database::conectar();

        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $offset = ($page - 1) * $perPage;

        $params = [];
        $whereSql = $this->buildFilterWhere($filters, $params, false);
        
        // Control por rol: comerciales solo ven inmuebles de SUS clientes
        $isComercial = !in_array($rol, ['admin', 'coordinador'], true);
        if ($isComercial) {
            // Añadir filtro: el cliente del inmueble debe pertenecer al comercial
            $whereSql .= " AND c.usuario_id = :comercial_user_id";
            $params[':comercial_user_id'] = $userId;
        }

        $countSql = "SELECT COUNT(*) FROM inmuebles i 
                     JOIN clientes c ON i.propietario_id = c.id_cliente
                     {$whereSql}";
        $countStmt = $pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        $sql = "SELECT i.*, 
                       c.nombre AS propietario_nombre, c.apellidos AS propietario_apellidos,
                       u.nombre AS comercial_nombre
                FROM inmuebles i
                JOIN clientes c ON i.propietario_id = c.id_cliente
                LEFT JOIN usuarios u ON i.comercial_id = u.id_usuario
                {$whereSql}
                ORDER BY i.fecha_alta DESC, i.id_inmueble DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $data = $stmt->fetchAll() ?: [];

        return [
            'data'    => $data,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
        ];
    }

    public function paginatePublic(array $filters, int $page, int $perPage): array
    {
        $pdo = Database::conectar();

        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $offset = ($page - 1) * $perPage;

        $params = [];
        $whereSql = $this->buildFilterWhere($filters, $params, true);

        $countSql = "SELECT COUNT(*) FROM inmuebles i 
                     JOIN clientes c ON i.propietario_id = c.id_cliente
                     {$whereSql}";
        $countStmt = $pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetchColumn();

        $sql = "SELECT i.*
                FROM inmuebles i
                {$whereSql}
                ORDER BY i.fecha_alta DESC, i.id_inmueble DESC
                LIMIT {$perPage} OFFSET {$offset}";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $data = $stmt->fetchAll() ?: [];

        return [
            'data'    => $data,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
        ];
    }

    public function findById(int $id): ?object
    {
        $pdo = Database::conectar();
        $sql = "SELECT i.*,
                       c.nombre AS propietario_nombre, c.apellidos AS propietario_apellidos,
                       u.nombre AS comercial_nombre, u.email AS comercial_email, u.telefono AS comercial_telefono
                FROM inmuebles i
                JOIN clientes c ON i.propietario_id = c.id_cliente
                LEFT JOIN usuarios u ON i.comercial_id = u.id_usuario
                WHERE i.id_inmueble = :id
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByRef(string $ref): ?object
    {
        $pdo = Database::conectar();
        $sql = "SELECT i.*,
                       c.nombre AS propietario_nombre, c.apellidos AS propietario_apellidos,
                       u.nombre AS comercial_nombre, u.email AS comercial_email, u.telefono AS comercial_telefono
                FROM inmuebles i
                JOIN clientes c ON i.propietario_id = c.id_cliente
                LEFT JOIN usuarios u ON i.comercial_id = u.id_usuario
                WHERE i.ref = :ref
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':ref', $ref, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool
    {
        $pdo = Database::conectar();
        $sql = "INSERT INTO inmuebles (
                    ref, direccion, localidad, provincia, cp,
                    tipo, operacion, precio,
                    superficie, habitaciones, banos, descripcion, imagen,
                    estado, activo, archivado,
                    propietario_id, comercial_id
                ) VALUES (
                    :ref, :direccion, :localidad, :provincia, :cp,
                    :tipo, :operacion, :precio,
                    :superficie, :habitaciones, :banos, :descripcion, :imagen,
                    :estado, :activo, :archivado,
                    :propietario_id, :comercial_id
                )";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':ref'            => $data['ref'],
            ':direccion'      => $data['direccion'] ?? null,
            ':localidad'      => $data['localidad'] ?? null,
            ':provincia'      => $data['provincia'] ?? null,
            ':cp'             => $data['cp'] ?? null,
            ':tipo'           => $data['tipo'],
            ':operacion'      => $data['operacion'],
            ':precio'         => $data['precio'] !== null ? (float)$data['precio'] : null,
            ':superficie'     => $data['superficie'] ?? null,
            ':habitaciones'   => $data['habitaciones'] ?? null,
            ':banos'          => $data['banos'] ?? null,
            ':descripcion'    => $data['descripcion'] ?? null,
            ':imagen'         => $data['imagen'] ?? null,
            ':estado'         => $data['estado'],
            ':activo'         => (int)($data['activo'] ?? 1),
            ':archivado'      => (int)($data['archivado'] ?? 0),
            ':propietario_id' => (int)$data['propietario_id'],
            ':comercial_id'   => $data['comercial_id'] !== null ? (int)$data['comercial_id'] : null,
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $pdo = Database::conectar();
        $sql = "UPDATE inmuebles SET
                    ref = :ref,
                    direccion = :direccion,
                    localidad = :localidad,
                    provincia = :provincia,
                    cp = :cp,
                    tipo = :tipo,
                    operacion = :operacion,
                    precio = :precio,
                    superficie = :superficie,
                    habitaciones = :habitaciones,
                    banos = :banos,
                    descripcion = :descripcion,
                    imagen = :imagen,
                    estado = :estado,
                    activo = :activo,
                    archivado = :archivado,
                    propietario_id = :propietario_id,
                    comercial_id = :comercial_id
                WHERE id_inmueble = :id";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            ':id'             => $id,
            ':ref'            => $data['ref'],
            ':direccion'      => $data['direccion'] ?? null,
            ':localidad'      => $data['localidad'] ?? null,
            ':provincia'      => $data['provincia'] ?? null,
            ':cp'             => $data['cp'] ?? null,
            ':tipo'           => $data['tipo'],
            ':operacion'      => $data['operacion'],
            ':precio'         => $data['precio'] !== null ? (float)$data['precio'] : null,
            ':superficie'     => $data['superficie'] ?? null,
            ':habitaciones'   => $data['habitaciones'] ?? null,
            ':banos'          => $data['banos'] ?? null,
            ':descripcion'    => $data['descripcion'] ?? null,
            ':imagen'         => $data['imagen'] ?? null,
            ':estado'         => $data['estado'],
            ':activo'         => (int)($data['activo'] ?? 1),
            ':archivado'      => (int)($data['archivado'] ?? 0),
            ':propietario_id' => (int)$data['propietario_id'],
            ':comercial_id'   => $data['comercial_id'] !== null ? (int)$data['comercial_id'] : null,
        ]);
    }

    public function delete(int $id): bool
    {
        $pdo = Database::conectar();
        try {
            $stmt = $pdo->prepare('DELETE FROM inmuebles WHERE id_inmueble = :id');
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException) {
            return false;
        }
    }

    private function buildFilterWhere(array $filters, array &$params, bool $onlyPublished = false): string
    {
        $conditions = ['1=1'];

        if (!empty($filters['ref'])) {
            $conditions[] = 'i.ref LIKE :ref';
            $params[':ref'] = '%' . $filters['ref'] . '%';
        }
        if (!empty($filters['tipo'])) {
            $conditions[] = 'i.tipo = :tipo';
            $params[':tipo'] = $filters['tipo'];
        }
        if (!empty($filters['operacion'])) {
            $conditions[] = 'i.operacion = :operacion';
            $params[':operacion'] = $filters['operacion'];
        }
        if (!empty($filters['localidad'])) {
            $conditions[] = 'i.localidad LIKE :localidad';
            $params[':localidad'] = '%' . $filters['localidad'] . '%';
        }
        if (!empty($filters['precio_min'])) {
            $conditions[] = 'i.precio >= :precio_min';
            $params[':precio_min'] = (float)$filters['precio_min'];
        }
        if (!empty($filters['precio_max'])) {
            $conditions[] = 'i.precio <= :precio_max';
            $params[':precio_max'] = (float)$filters['precio_max'];
        }
        if (array_key_exists('m2_min', $filters) && $filters['m2_min'] !== null) {
            $conditions[] = 'i.superficie >= :m2_min';
            $params[':m2_min'] = (int)$filters['m2_min'];
        }
        if (!empty($filters['propietario_id'])) {
            $conditions[] = 'i.propietario_id = :propietario_id';
            $params[':propietario_id'] = (int)$filters['propietario_id'];
        }

        if (!$onlyPublished) {
            if (!empty($filters['estado'])) {
                $conditions[] = 'i.estado = :estado';
                $params[':estado'] = $filters['estado'];
            }
        } else {
            // Para vista pública: solo mostrar inmuebles activos
            $conditions[] = "i.activo = 1";
        }

        return 'WHERE ' . implode(' AND ', $conditions);
    }
    /**
     * Obtiene inmuebles publicables aleatorios para el carrusel de la home.
     * 
     * @param int $limit Número de inmuebles a obtener (1-12)
     * @param bool $stableDaily Si true, mantiene orden aleatorio estable durante el día
     * @return array Array de inmuebles con campos mínimos para cards
     */
    public static function getHomeCarousel(int $limit = 6, bool $stableDaily = true): array
    {
        // Validar y limitar el parámetro limit
        $limit = max(1, min(12, $limit));
        
        $pdo = Database::conectar();
        
        // Filtro publicable: solo mostrar inmuebles activos
        $whereSql = 'WHERE i.activo = 1';
        
        // Determinar el ORDER para aleatorización
        $orderBy = $stableDaily 
            ? 'ORDER BY RAND(TO_DAYS(CURDATE()))' 
            : 'ORDER BY RAND()';
        
        // Query con campos mínimos necesarios para las cards
        $sql = "SELECT 
                    i.id_inmueble,
                    i.ref,
                    i.tipo,
                    i.operacion,
                    i.localidad,
                    i.provincia,
                    i.precio,
                    i.habitaciones,
                    i.banos,
                    i.superficie,
                    i.imagen
                FROM inmuebles i
                {$whereSql}
                {$orderBy}
                LIMIT {$limit}";
        
        try {
            $stmt = $pdo->query($sql);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result ?: [];
        } catch (\PDOException $e) {
            error_log("Error en getHomeCarousel: " . $e->getMessage());
            return [];
        }
    }

    public function getByPropietario(int $propietarioId): array
    {
        $pdo = Database::conectar();
        $sql = "SELECT id_inmueble, ref, direccion, localidad, operacion, precio, estado
                FROM inmuebles
                WHERE propietario_id = :propietario_id
                ORDER BY fecha_alta DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':propietario_id', $propietarioId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}


