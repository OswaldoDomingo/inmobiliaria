<?php
// app/core/Database.php

// 2 niveles hacia arriba: /inmobiliaria
require_once dirname(__DIR__, 2) . '/config/database.php';

class Database
{
    // Instancia única de PDO
    private static $instancia = null;
    private $conexion;

    // Constructor privado: Nadie puede hacer "new Database()" desde fuera
    private function __construct()
    {
        try {
            // Obtenemos la configuración desde config/database.php (que a su vez lee .env)
            $config = getDatabaseConfig();

            // Cadena de conexión (DSN)
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['dbname'],
                $config['charset']
            );

            // Opciones avanzadas de PDO
            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // errores como excepciones
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,         // $fila->campo
                PDO::ATTR_EMULATE_PREPARES   => false,                  // seguridad extra en prepares
            ];

            $this->conexion = new PDO(
                $dsn,
                $config['user'],
                $config['password'],
                $opciones
            );

        } catch (PDOException $e) {
            // Si falla la conexión, matamos el proceso y mostramos mensaje
            die("❌ Error de Conexión a BD: " . $e->getMessage());
        }
    }

    // Método estático para obtener la conexión
    public static function conectar()
    {
        if (self::$instancia === null) {
            self::$instancia = new Database();
        }

        return self::$instancia->conexion;
    }
}
