<?php
declare(strict_types=1);

namespace App\Core;

/**
 * CSRF Helper
 * Genera y valida tokens CSRF almacenados en sesión.
 */
final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    /**
     * Devuelve el token actual o genera uno nuevo.
     */
    public static function token(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Valida el token recibido.
     *
     * @param string|null $token
     */
    public static function validate(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($token) || empty($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        return hash_equals($_SESSION[self::SESSION_KEY], $token);
    }
}
