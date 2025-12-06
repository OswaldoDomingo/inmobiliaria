<?php
/**
 * Carga las variables del archivo .env en $_ENV y getenv()
 */

$rootPath = dirname(__DIR__);
$envFile  = $rootPath . DIRECTORY_SEPARATOR . '.env';

if (!file_exists($envFile)) {
    throw new RuntimeException('Archivo .env no encontrado en la raíz del proyecto');
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    // Ignorar comentarios
    if (str_starts_with(trim($line), '#')) {
        continue;
    }

    // Formato KEY=VALUE
    $parts = explode('=', $line, 2);
    if (count($parts) !== 2) {
        continue;
    }

    $key   = trim($parts[0]);
    $value = trim($parts[1]);

    // Quitar comillas si las hay
    $value = trim($value, "\"'");

    $_ENV[$key] = $value;
    putenv("$key=$value");
}
// Ahora las variables de entorno están disponibles en $_ENV y getenv()