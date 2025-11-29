<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Config
 * Almacena la configuración cargada al inicio y permite acceder a ella de forma segura.
 */
final class Config
{
    /** @var array<string, mixed> */
    private static array $data = [];

    /**
     * Inicializa la configuración (se llama una sola vez desde el front controller).
     *
     * @param array<string, mixed> $config
     */
    public static function init(array $config): void
    {
        self::$data = $config;
    }

    /**
     * Obtiene un valor por clave con notación punto, ej: app.base_url
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if ($key === '') {
            return self::$data;
        }

        $segments = explode('.', $key);
        $value = self::$data;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
