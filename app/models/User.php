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
    /**
     * Busca un usuario por su ID.
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
     * Actualiza los datos de un usuario.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $pdo = Database::conectar();
        
        // Construcción dinámica de la query según si hay password o no
        $fields = "nombre = :nombre, email = :email, rol = :rol";
        $params = [
            ':id'     => $id,
            ':nombre' => $data['nombre'],
            ':email'  => $data['email'],
            ':rol'    => $data['rol']
        ];

        if (!empty($data['password_hash'])) {
            $fields .= ", password_hash = :password_hash";
            $params[':password_hash'] = $data['password_hash'];
        }

        $sql = "UPDATE usuarios SET $fields WHERE id_usuario = :id";
        $stmt = $pdo->prepare($sql);
        
        return $stmt->execute($params);
    }

    /**
     * Da de baja lógica a un usuario (Soft Delete).
     *
     * @param int $id
     * @return bool
     */
    public function softDelete(int $id): bool
    {
        $pdo = Database::conectar();
        // activo = 0, archivado = 1, fecha_baja = NOW() (si existiera la columna, si no solo activo/archivado)
        // Asumiendo que no tenemos fecha_baja en la estructura inicial dada por el usuario, 
        // pero el usuario pidió "pone fecha_baja = NOW()". 
        // Si la columna no existe, fallará. Asumiré que existe o que debo añadirla.
        // El usuario dijo: "Ya tengo la tabla usuarios creada... id_usuario, nombre, email, password_hash, rol, activo".
        // NO mencionó 'archivado' ni 'fecha_baja' en la estructura inicial, pero en el requerimiento 2 dice:
        // "Actualiza la columna activo = 0 y archivado = 1 y pone fecha_baja = NOW()."
        // Voy a asumir que las columnas existen o que debo intentar usarlas.
        // Si falla, el usuario me lo dirá. Pero para prevenir, usaré solo activo si falla.
        // Mencionó "aprovechar las columnas activo y archivado que ya tenemos".
        
        $sql = "UPDATE usuarios SET activo = 0, archivado = 1 WHERE id_usuario = :id";
        // Nota: Si fecha_baja existe, sería: , fecha_baja = NOW()
        // Voy a arriesgarme a incluir fecha_baja porque lo pidió explícitamente.
        // Si da error, lo corregiremos.
        // Espera, revisando el prompt inicial: "Contexto de Datos... activo (tinyint)". No mencionó archivado ni fecha_baja.
        // Pero en este prompt dice: "aprovechar las columnas activo y archivado que ya tenemos".
        // Haré la query con activo y archivado. fecha_baja lo omitiré si no estoy seguro, pero lo pidió.
        // Mejor: UPDATE usuarios SET activo = 0, archivado = 1 ...
        
        // CORRECCIÓN: El usuario dijo "pone fecha_baja = NOW()".
        // Voy a asumir que la columna existe.
        
        $sql = "UPDATE usuarios SET activo = 0, archivado = 1, fecha_baja = NOW() WHERE id_usuario = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}
