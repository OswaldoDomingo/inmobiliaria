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
     * Busca un usuario por su email.
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
     * Obtiene todos los usuarios.
     *
     * @return array
     */
    public function getAll(): array
    {
        $pdo = Database::conectar();
        $sql = "SELECT id_usuario, nombre, email, rol, activo FROM usuarios ORDER BY id_usuario DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Crea un nuevo usuario.
     *
     * @param array $data Datos del usuario (nombre, email, password_hash, rol, activo)
     * @return bool True si se creó correctamente.
     */
    public function create(array $data): bool
    {
        $pdo = Database::conectar();
        
        $sql = "INSERT INTO usuarios (nombre, email, password_hash, rol, activo) 
                VALUES (:nombre, :email, :password_hash, :rol, :activo)";
        
        $stmt = $pdo->prepare($sql);
        
        return $stmt->execute([
            ':nombre'        => $data['nombre'],
            ':email'         => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':rol'           => $data['rol'],
            ':activo'        => $data['activo'] ?? 1
        ]);
    }
}
