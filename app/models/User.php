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
}
