<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Demanda
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::conectar();
    }

    /**
     * Listado paginado de demandas con control por rol
     * 
     * @param int $userId ID del usuario actual
     * @param string $rol Rol del usuario ('admin', 'coordinador', 'comercial')
     * @param array $filtros Filtros (tipo_operacion, estado, comercial_id, etc.)
     * @param int $page Página actual
     * @param int $perPage Registros por página
     * @return array ['data' => [...], 'total' => int, 'page' => int, 'perPage' => int]
     */
    public function paginateAdmin(int $userId, string $rol, array $filtros = [], int $page = 1, int $perPage = 20): array
    {
        $isAdminOrCoord = in_array($rol, ['admin', 'coordinador'], true);
        
        // Base query
        $sql = "SELECT d.*, 
                       c.nombre AS cliente_nombre, 
                       c.apellidos AS cliente_apellidos,
                       u.nombre AS comercial_nombre
                FROM demandas d
                INNER JOIN clientes c ON d.cliente_id = c.id_cliente
                LEFT JOIN usuarios u ON d.comercial_id = u.id_usuario
                WHERE 1=1";
        
        $params = [];
        
        // Control por rol: comerciales solo ven demandas de sus clientes
        if (!$isAdminOrCoord) {
            $sql .= " AND c.usuario_id = :user_id";
            $params[':user_id'] = $userId;
        }
        
        // Filtro por tipo de operación
        if (!empty($filtros['tipo_operacion'])) {
            $sql .= " AND d.tipo_operacion = :tipo_operacion";
            $params[':tipo_operacion'] = $filtros['tipo_operacion'];
        }
        
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND d.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }
        
        // Filtro por comercial (solo para admin/coordinador)
        if ($isAdminOrCoord && !empty($filtros['comercial_id'])) {
            $sql .= " AND d.comercial_id = :comercial_id";
            $params[':comercial_id'] = (int)$filtros['comercial_id'];
        }
        
        // Contar total
        $countSql = "SELECT COUNT(*) FROM (" . $sql . ") AS subquery";
        $stmtCount = $this->pdo->prepare($countSql);
        $stmtCount->execute($params);
        $total = (int)$stmtCount->fetchColumn();
        
        // Ordenar y paginar
        $sql .= " ORDER BY d.fecha_alta DESC";
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decodificar JSON de características
        foreach ($rows as &$row) {
            $row['caracteristicas'] = json_decode($row['caracteristicas'] ?? '[]', true) ?: [];
        }
        
        return [
            'data' => $rows,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * Obtener demanda por ID
     */
    public function findById(int $id): ?object
    {
        $sql = "SELECT d.*, 
                       c.nombre AS cliente_nombre, 
                       c.apellidos AS cliente_apellidos,
                       u.nombre AS comercial_nombre
                FROM demandas d
                INNER JOIN clientes c ON d.cliente_id = c.id_cliente
                LEFT JOIN usuarios u ON d.comercial_id = u.id_usuario
                WHERE d.id_demanda = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$row) {
            return null;
        }
        
        // Decodificar JSON de características
        $row->caracteristicas = json_decode($row->caracteristicas ?? '[]', true) ?: [];
        
        return $row;
    }

    /**
     * Crear nueva demanda
     */
    public function create(array $data): bool
    {
        $sql = "INSERT INTO demandas (
                    cliente_id, comercial_id, tipo_operacion,
                    rango_precio_min, rango_precio_max,
                    superficie_min, habitaciones_min, banos_min,
                    zonas, caracteristicas, estado, activo, archivado
                ) VALUES (
                    :cliente_id, :comercial_id, :tipo_operacion,
                    :rango_precio_min, :rango_precio_max,
                    :superficie_min, :habitaciones_min, :banos_min,
                    :zonas, :caracteristicas, :estado, :activo, :archivado
                )";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':cliente_id', $data['cliente_id'], PDO::PARAM_INT);
        $stmt->bindValue(':comercial_id', $data['comercial_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':tipo_operacion', $data['tipo_operacion']);
        $stmt->bindValue(':rango_precio_min', $data['rango_precio_min'] ?? null);
        $stmt->bindValue(':rango_precio_max', $data['rango_precio_max'] ?? null);
        $stmt->bindValue(':superficie_min', $data['superficie_min'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':habitaciones_min', $data['habitaciones_min'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':banos_min', $data['banos_min'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':zonas', $data['zonas'] ?? null);
        $stmt->bindValue(':caracteristicas', json_encode($data['caracteristicas'] ?? [], JSON_UNESCAPED_UNICODE));
        $stmt->bindValue(':estado', $data['estado'] ?? 'activa');
        $stmt->bindValue(':activo', $data['activo'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':archivado', $data['archivado'] ?? 0, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Actualizar demanda existente
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE demandas SET
                    cliente_id = :cliente_id,
                    comercial_id = :comercial_id,
                    tipo_operacion = :tipo_operacion,
                    rango_precio_min = :rango_precio_min,
                    rango_precio_max = :rango_precio_max,
                    superficie_min = :superficie_min,
                    habitaciones_min = :habitaciones_min,
                    banos_min = :banos_min,
                    zonas = :zonas,
                    caracteristicas = :caracteristicas,
                    estado = :estado,
                    activo = :activo,
                    archivado = :archivado
                WHERE id_demanda = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':cliente_id', $data['cliente_id'], PDO::PARAM_INT);
        $stmt->bindValue(':comercial_id', $data['comercial_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':tipo_operacion', $data['tipo_operacion']);
        $stmt->bindValue(':rango_precio_min', $data['rango_precio_min'] ?? null);
        $stmt->bindValue(':rango_precio_max', $data['rango_precio_max'] ?? null);
        $stmt->bindValue(':superficie_min', $data['superficie_min'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':habitaciones_min', $data['habitaciones_min'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':banos_min', $data['banos_min'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':zonas', $data['zonas'] ?? null);
        $stmt->bindValue(':caracteristicas', json_encode($data['caracteristicas'] ?? [], JSON_UNESCAPED_UNICODE));
        $stmt->bindValue(':estado', $data['estado'] ?? 'activa');
        $stmt->bindValue(':activo', $data['activo'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':archivado', $data['archivado'] ?? 0, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Borrar demanda (borrado real, las FK CASCADE eliminan cruces)
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM demandas WHERE id_demanda = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Obtener demandas de un cliente específico
     */
    public function getByCliente(int $clienteId): array
    {
        $sql = "SELECT d.*, u.nombre AS comercial_nombre
                FROM demandas d
                LEFT JOIN usuarios u ON d.comercial_id = u.id_usuario
                WHERE d.cliente_id = :cliente_id
                ORDER BY d.fecha_alta DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':cliente_id', $clienteId, PDO::PARAM_INT);
        $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decodificar JSON de características
        foreach ($rows as &$row) {
            $row['caracteristicas'] = json_decode($row['caracteristicas'] ?? '[]', true) ?: [];
        }
        
        return $rows;
    }
}


