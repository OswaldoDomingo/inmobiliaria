<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Clase Router
 * Gestiona el registro y despacho de rutas de la aplicación.
 */
class Router
{
    /**
     * Almacena las rutas registradas.
     * Estructura: ['GET' => ['/uri' => callback], 'POST' => ...]
     * @var array<string, array<string, callable|array>>
     */
    private array $routes = [];

    public function get(string $uri, callable|array $action): void
    {
        $this->register('GET', $uri, $action);
    }

    public function post(string $uri, callable|array $action): void
    {
        $this->register('POST', $uri, $action);
    }

    /**
     * Método interno para registrar rutas.
     * Normaliza para que '/ruta' y '/ruta/' sean equivalentes.
     */
    private function register(string $method, string $uri, callable|array $action): void
    {
        $uri = $this->normalizePath($uri);
        $this->routes[$method][$uri] = $action;
    }

    /**
     * Despacha la petición actual.
     * Lee $_SERVER['REQUEST_URI'] y $_SERVER['REQUEST_METHOD'].
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';

        // 1) Parsear URL para separar path de query string (?id=1)
        $uriPath = (string) parse_url($uri, PHP_URL_PATH);

        // 2) Decodificar caracteres URL (ej: %20 -> espacio)
        $uriPath = rawurldecode($uriPath);

        // 3) Quitar prefijo de subcarpeta si aplica (ej: /inmobiliaria/public)
        // OJO: En Windows dirname devuelve \ pero REQUEST_URI usa /
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']); 
        // Normalizamos separadores a /
        $scriptDir = str_replace('\\', '/', $scriptDir);

        if ($scriptDir !== '/') {
            // Aseguramos que termine en / para evitar coincidencia parcial errónea
            // pero si uriPath empieza exactamente con scriptDir, lo quitamos
            if (str_starts_with($uriPath, $scriptDir)) {
                $uriPath = substr($uriPath, strlen($scriptDir));
            } elseif (str_starts_with($uriPath, rtrim($scriptDir, '/'))) {
                 // Caso donde scriptDir es /foo/bar/ y uriPath es /foo/bar
                 $uriPath = substr($uriPath, strlen(rtrim($scriptDir, '/')));
            }
        }

        // 4) Normalizar path final (slash inicial + quitar trailing slash)
        $uriPath = $this->normalizePath($uriPath);
        
        // DEBUG: Logging
        // error_log("Router Dispatch: URI Raw: $uri");
        // error_log("Router Dispatch: ScriptDir: $scriptDir");
        // error_log("Router Dispatch: URI Path: $uriPath");

        // 5) Buscar coincidencia
        if (isset($this->routes[$method][$uriPath])) {
            $action = $this->routes[$method][$uriPath];

            // Closure
            if (is_callable($action)) {
                call_user_func($action);
                return;
            }

            // [Controlador, Método]
            if (is_array($action) && count($action) === 2) {
                [$controllerClass, $methodName] = $action;

                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $methodName)) {
                        $controller->$methodName();
                        return;
                    }
                }
            }
        }

        $this->handle404();
    }

    /**
     * Normaliza rutas:
     * - asegura slash inicial
     * - elimina slash final (excepto si es "/")
     */
    private function normalizePath(string $path): string
    {
        $path = trim($path);

        if ($path === '') {
            return '/';
        }

        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        if ($path !== '/') {
            $path = rtrim($path, '/');
            if ($path === '') $path = '/';
        }

        return $path;
    }

    private function handle404(): void
    {
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "<p>La página que buscas no existe.</p>";
    }
}
