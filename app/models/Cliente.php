<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Cliente
{
    /**
     * Obtiene clientes según rol.
     * Admin/Coordinador: todos; Comercial: solo los asignados.
     */
    public function getAll(?int $usuarioId = null, string $rol = 'admin'): array
    {
        $pdo = Database::conectar();

        if (in_array($rol, ['admin', 'coordinador'], true)) {
            $sql = "SELECT c.*, u.nombre AS comercial_nombre, u.email AS comercial_email
                    FROM clientes c
                    LEFT JOIN usuarios u ON c.usuario_id = u.id_usuario
                    ORDER BY c.id_cliente DESC";
            return $pdo->query($sql)->fetchAll() ?: [];
        }

        $sql = "SELECT c.*, u.nombre AS comercial_nombre, u.email AS comercial_email
                FROM clientes c
                LEFT JOIN usuarios u ON c.usuario_id = u.id_usuario
                WHERE c.usuario_id = :uid
                ORDER BY c.id_cliente DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':uid', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    /**
     * Inserta un cliente.
     */
    public function create(array $data): bool
    {
        $pdo = Database::conectar();
        $sql = "INSERT INTO clientes (nombre, apellidos, dni, email, telefono, direccion, notas, usuario_id, activo)
                VALUES (:nombre, :apellidos, :dni, :email, :telefono, :direccion, :notas, :usuario_id, :activo)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':nombre'     => $data['nombre'],
            ':apellidos'  => $data['apellidos'],
            ':dni'        => $data['dni'] ?? null,
            ':email'      => $data['email'] ?? null,
            ':telefono'   => $data['telefono'] ?? null,
            ':direccion'  => $data['direccion'] ?? null,
            ':notas'      => $data['notas'] ?? null,
            ':usuario_id' => $data['usuario_id'] ?? null,
            ':activo'     => $data['activo'] ?? 1,
        ]);
    }

    public function findById(int $id): ?object
    {
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE id_cliente = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $pdo = Database::conectar();
        $sql = "UPDATE clientes SET
                    nombre = :nombre,
                    apellidos = :apellidos,
                    dni = :dni,
                    email = :email,
                    telefono = :telefono,
                    direccion = :direccion,
                    notas = :notas,
                    usuario_id = :usuario_id,
                    activo = :activo
                WHERE id_cliente = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':nombre'     => $data['nombre'],
            ':apellidos'  => $data['apellidos'],
            ':dni'        => $data['dni'] ?? null,
            ':email'      => $data['email'] ?? null,
            ':telefono'   => $data['telefono'] ?? null,
            ':direccion'  => $data['direccion'] ?? null,
            ':notas'      => $data['notas'] ?? null,
            ':usuario_id' => $data['usuario_id'] ?? null,
            ':activo'     => $data['activo'] ?? 1,
            ':id'         => $id,
        ]);
    }

    /**
     * Elimina un cliente; devuelve false si hay violación de FK (tiene inmuebles).
     */
    public function delete(int $id): bool
    {
        $pdo = Database::conectar();
        try {
            $stmt = $pdo->prepare("DELETE FROM clientes WHERE id_cliente = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException) {
            return false;
        }
    }

    public function findByDni(?string $dni): ?object
    {
        if ($dni === null || $dni === '') {
            return null;
        }
        $pdo = Database::conectar();
        $stmt = $pdo->prepare("SELECT * FROM clientes WHERE dni = :dni LIMIT 1");
        $stmt->bindValue(':dni', $dni, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Lista básica de clientes para selects de propietario.
     * Devuelve id_cliente, nombre y apellidos ordenados alfabéticamente.
     */
    public function listForSelect(?int $usuarioId = null, string $rol = 'admin'): array
    {
        $pdo = Database::conectar();

        // 1. Roles privilegiados: Ven todos
        if (in_array($rol, ['admin', 'coordinador'], true)) {
            $sql = "SELECT id_cliente, nombre, apellidos
                    FROM clientes
                    WHERE activo = 1
                    ORDER BY nombre ASC, apellidos ASC";
            return $pdo->query($sql)->fetchAll() ?: [];
        }

        // 2. Comercial (o cualquier otro rol no admin): Solo sus clientes
        // Se asume comportamiento restrictivo por defecto
        if ($rol === 'comercial' || true) {
             $sql = "SELECT id_cliente, nombre, apellidos
                    FROM clientes
                    WHERE usuario_id = :uid AND activo = 1
                    ORDER BY nombre ASC, apellidos ASC";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':uid', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll() ?: [];
        }
        
        return [];
    }
}
