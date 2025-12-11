<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modelo Usuario
 * Gestiona la interacción con la tabla 'usuarios'.
 */
class User
{
    /**
     * Busco un usuario por su email.
     *
     * @param string $email
     * @return object|null Devuelve el objeto usuario o null si no existe.
     */
    public function findByEmail(string $email): ?object
    {
        $pdo = Database::conectar();
        
        $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    /**
     * Obtengo todos los usuarios.
     *
     * @return array
     */
    public function getAll(): array
    {
        $pdo = Database::conectar();
        $sql = "SELECT id_usuario, nombre, email, rol, activo, cuenta_bloqueada, foto_perfil FROM usuarios ORDER BY id_usuario DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Creo un nuevo usuario.
     *
     * @param array $data Datos del usuario (nombre, email, password_hash, rol, activo)
     * @return bool True si se creó correctamente.
     */
    public function create(array $data): bool
    {
        $pdo = Database::conectar();
        
        $sql = "INSERT INTO usuarios (nombre, email, telefono, password_hash, rol, foto_perfil, activo) 
                VALUES (:nombre, :email, :telefono, :password_hash, :rol, :foto_perfil, :activo)";
        
        $stmt = $pdo->prepare($sql);
        
        return $stmt->execute([
            ':nombre'        => $data['nombre'],
            ':email'         => $data['email'],
            ':telefono'      => $data['telefono'] ?? null,
            ':password_hash' => $data['password_hash'],
            ':rol'           => $data['rol'],
            ':foto_perfil'   => $data['foto_perfil'] ?? null,
            ':activo'        => $data['activo'] ?? 1
        ]);
    }
    /**
     * Busco un usuario por su ID.
     *
     * @param int $id
     * @return object|null
     */
    public function findById(int $id): ?object
    {
        $pdo = Database::conectar();
        $sql = "SELECT * FROM usuarios WHERE id_usuario = :id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Actualizo los datos de un usuario.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $pdo = Database::conectar();
        
        // Construcción dinámica de la query según si hay password o no
        $fields = "nombre = :nombre, email = :email, telefono = :telefono, rol = :rol";
        $params = [
            ':id'       => $id,
            ':nombre'   => $data['nombre'],
            ':email'    => $data['email'],
            ':telefono' => $data['telefono'] ?? null,
            ':rol'      => $data['rol']
        ];

        if (array_key_exists('foto_perfil', $data)) {
            $fields .= ", foto_perfil = :foto_perfil";
            $params[':foto_perfil'] = $data['foto_perfil'];
        }

        if (!empty($data['password_hash'])) {
            $fields .= ", password_hash = :password_hash";
            $params[':password_hash'] = $data['password_hash'];
        }

        $sql = "UPDATE usuarios SET $fields WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        
        return $stmt->execute($params);
    }

    /**
     * Doy de baja lógica a un usuario (Soft Delete).
     *
     * @param int $id
     * @return bool
     */
    public function softDelete(int $id): bool
    {
        $pdo = Database::conectar();
        // activo = 0, archivado = 1, fecha_baja = NOW()
        
        $sql = "UPDATE usuarios SET activo = 0, archivado = 1, fecha_baja = NOW() WHERE id_usuario = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    /**
     * Bloqueo o desbloqueo un usuario.
     *
     * @param int $id ID del usuario
     * @param int $status 1 para bloquear, 0 para desbloquear
     * @return bool
     */
    public function toggleBlock(int $id, int $status): bool
    {
        $pdo = Database::conectar();
        
        if ($status === 0) {
            // Desbloquear: Resetear cuenta_bloqueada y intentos_fallidos
            $sql = "UPDATE usuarios SET cuenta_bloqueada = 0, intentos_fallidos = 0 WHERE id_usuario = :id";
        } else {
            // Bloquear: Solo poner cuenta_bloqueada = 1
            $sql = "UPDATE usuarios SET cuenta_bloqueada = 1 WHERE id_usuario = :id";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Obtiene comerciales/coordinadores activos para asignación de clientes.
     *
     * @return array
     */
    public function getComercialesActivos(): array
    {
        $pdo = Database::conectar();
        $sql = "SELECT id_usuario, nombre
                FROM usuarios
                WHERE rol IN ('comercial', 'coordinador')
                  AND activo = 1
                  AND (archivado IS NULL OR archivado = 0)
                ORDER BY nombre ASC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Obtiene el coordinador general para fallback cuando no hay comercial asignado.
     *
     * @return object|null
     */
    public function getCoordinadorGeneral(): ?object
    {
        $pdo = Database::conectar();
        $sql = "SELECT id_usuario, nombre, email, telefono
                FROM usuarios
                WHERE es_coordinador_general = 1
                  AND activo = 1
                  AND (archivado IS NULL OR archivado = 0)
                LIMIT 1";
        $stmt = $pdo->query($sql);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
