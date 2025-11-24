<?php
declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Clase Database
 * Singleton + Devuelve una conexión PDO en cada llamada.
 * 
 * Rutas implicadas:
 *   ROOT   -> inmobiliaria/
 *   CONFIG -> inmobiliaria/config/
 */
final class Database
{
    /** @var Database|null */
    private static ?Database $instancia = null;

    /** @var PDO */
    private PDO $conexion;

    /**
     * Constructor privado
     * Carga los datos de config.php y abre la conexión
     */
    private function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/config.php';
        $db     = $config['db'];

        // DSN
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $db['host'],
            $db['dbname'],
            $db['charset']
        );

        $opciones = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conexion = new PDO(
                $dsn,
                $db['user'],
                $db['pass'],
                $opciones
            );
        } catch (PDOException $e) {
            die("❌ Error de Conexión a la Base de Datos: " . $e->getMessage());
        }
    }

    /**
     * Devuelve una conexión PDO lista para usar
     */
    public static function conectar(): PDO
    {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }

        return self::$instancia->conexion;
    }
}
