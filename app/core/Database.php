<?php
// app/core/Database.php

class Database {
    // Guardaremos la instancia única aquí
    private static $instancia = null;
    private $conexion;

    // Constructor privado: Nadie puede hacer "new Database()" desde fuera
    private function __construct() {
        try {
            // Cadena de conexión (DSN)
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            
            // Opciones avanzadas de PDO
            $opciones = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Si falla, lanza error visible
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, // Devuelve datos como objetos ($user->nombre)
                PDO::ATTR_EMULATE_PREPARES => false, // Seguridad real contra inyecciones SQL
            ];

            $this->conexion = new PDO($dsn, DB_USER, DB_PASS, $opciones);
            
        } catch (PDOException $e) {
            // Si falla la conexión, matamos el proceso y mostramos mensaje
            die("❌ Error de Conexión a BD: " . $e->getMessage());
        }
    }

    // Método estático para obtener la conexión
    public static function conectar() {
        if (self::$instancia === null) {
            self::$instancia = new Database();
        }
        return self::$instancia->conexion;
    }
}