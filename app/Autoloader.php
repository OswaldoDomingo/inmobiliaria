<?php
declare(strict_types=1);

namespace App;

/**
 * Autoloader sencillo estilo PSR-4 para el namespace raíz "App"
 *
 * Convención:
 *   App\Core\Router       -> app/Core/Router.php
 *   App\Models\Inmueble   -> app/Models/Inmueble.php
 *   App\Controllers\Home  -> app/Controllers/Home.php
 */
final class Autoloader
{
    /**
     * Directorio base del proyecto (raíz, donde están app/, config/, public/, etc.)
     */
    private static string $baseDir;

    /**
     * Registra el autoloader en SPL
     *
     * @param string|null $baseDir Directorio base del proyecto
     */
    public static function register(?string $baseDir = null): void
    {
        // Si no se pasa, asumimos que Autoloader.php está en app/
        // y la raíz del proyecto está un nivel por encima.
        self::$baseDir = $baseDir ?? dirname(__DIR__);

        spl_autoload_register([self::class, 'loadClass']);
    }

    /**
     * Carga una clase de la aplicación siguiendo convención PSR-4 simple
     *
     * @param string $class Nombre completo de la clase (FQCN), ej: App\Controllers\HomeController
     */
    private static function loadClass(string $class): void
    {
        $prefix = 'App\\';
        $prefixLength = strlen($prefix);

        // Solo manejamos clases que empiezan por nuestro namespace raíz
        if (strncmp($class, $prefix, $prefixLength) !== 0) {
            return;
        }

        // Quitamos el prefijo App\ → Controllers\HomeController
        $relativeClass = substr($class, $prefixLength);

        // Sustituimos "\" por "/" y construimos la ruta del fichero
        // App\Controllers\HomeController -> app/Controllers/HomeController.php
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass);
        $file = self::$baseDir . '/app/' . $path . '.php';

        if (file_exists($file)) {
            require_once $file;
            return;
        }

        // FALLBACK: Intentar con directorios en minúsculas (para compatibilidad Linux/Windows)
        // Ejemplo: App\Core\Router -> app/core/Router.php
        // Ejemplo: App\Controllers\Home -> app/controllers/Home.php
        $parts = explode('\\', $relativeClass);
        $className = array_pop($parts);
        $dirs = array_map('strtolower', $parts);
        $pathLowerDirs = implode(DIRECTORY_SEPARATOR, $dirs);
        
        $fileLowerDirs = self::$baseDir . '/app/' . $pathLowerDirs . '/' . $className . '.php';
        
        if (file_exists($fileLowerDirs)) {
            require_once $fileLowerDirs;
            return;
        }

        // Si llegamos aquí, no se encontró el archivo.
        // error_log("Autoloader: No se encontró $class en $file ni en $fileLowerDirs");
    }
}
