<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/paths.php';

/**
 * Devuelve un array con la configuración de la base de datos
 */
function getDatabaseConfig(): array
{
    return [
        'driver'   => 'mysql',
        'host'     => $_ENV['DB_HOST'] ?? 'localhost',
        'dbname'   => $_ENV['DB_NAME'] ?? '',
        'user'     => $_ENV['DB_USER'] ?? '',
        'password' => $_ENV['DB_PASS'] ?? '',
        'charset'  => 'utf8mb4',
    ];
}
