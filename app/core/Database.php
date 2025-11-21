<?php
// app/core/Database.php

class Database
{
    // Instancia única de la clase
    private static ?Database $instancia = null;

    // Conexión PDO
    private \PDO $conexion;

    // Constructor privado
    private function __construct()
    {
        // Cargar config.php (local/servidor)
        $config = require dirname(__DIR__, 2) . '/config/config.php';
        $db     = $config['db'];

        // DSN de conexión
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $db['host'],
            $db['dbname'],
            $db['charset']
        );

        $opciones = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->conexion = new \PDO(
                $dsn,
                $db['user'],
                $db['pass'],
                $opciones
            );
        } catch (\PDOException $e) {
            die("❌ Error de Conexión a BD: " . $e->getMessage());
        }
    }

    // Método estático para obtener la conexión
    public static function conectar(): \PDO
    {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }

        return self::$instancia->conexion;
    }
}
