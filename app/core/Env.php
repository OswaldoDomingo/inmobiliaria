<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Env
 * Carga sencilla de variables de entorno desde un fichero .env estilo KEY="value".
 * No depende de librerías externas y no pisa variables ya definidas en el entorno.
 */
final class Env
{
    /**
     * Carga las variables del fichero .env en $_ENV y getenv() si no existían.
     *
     * @param string $path Ruta absoluta al fichero .env
     */
    public static function load(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            // Soporta KEY=VAL o KEY="VAL"
            $parts = explode('=', $trimmed, 2);
            if (count($parts) !== 2) {
                continue;
            }

            [$key, $value] = $parts;
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            // Quitar comillas envolventes
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            // No pisar variables ya definidas por el sistema
            if (getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}
