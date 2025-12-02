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

    /**
     * Registra una ruta GET.
     *
     * @param string $uri La URI de la ruta (ej: '/contacto').
     * @param callable|array $action La acción a ejecutar (Closure o [Controlador, método]).
     */
    public function get(string $uri, callable|array $action): void
    {
        $this->register('GET', $uri, $action);
    }

    /**
     * Registra una ruta POST.
     *
     * @param string $uri La URI de la ruta.
     * @param callable|array $action La acción a ejecutar.
     */
    public function post(string $uri, callable|array $action): void
    {
        $this->register('POST', $uri, $action);
    }

    /**
     * Método interno para registrar rutas.
     */
    private function register(string $method, string $uri, callable|array $action): void
    {
        // Normalizamos la URI: aseguramos que empiece por '/' y quitamos slashes finales (salvo si es solo '/')
        // Opcional: dependerá de si quieres ser estricto con '/ruta' vs '/ruta/'
        // Por ahora lo guardamos tal cual, asumiendo que el usuario define '/ruta'.
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

        // Limpieza de la URI:
        // 1. Parsear URL para separar path de query string (?id=1)
        $uriPath = parse_url($uri, PHP_URL_PATH);

        // 2. Decodificar caracteres URL (ej: %20 -> espacio) por seguridad y consistencia
        $uriPath = rawurldecode($uriPath);
        
        // 3. (Opcional) Si el proyecto está en una subcarpeta (ej: /inmobiliaria/public), 
        // podría necesitar limpiar el prefijo. 
        // Asumiremos que el VirtualHost apunta a public/ o que gestionamos rutas relativas.
        // Si está accediendo como localhost/inmobiliaria/public/index.php, la URI será /inmobiliaria/public/
        // Para este MVP, asumiremos que las rutas se definen completas o que ajustamos la base.
        // NOTA: Si se usa XAMPP en subcarpeta, la URI vendrá con '/inmobiliaria/public'.
        // Vamos a intentar detectar el script name para hacerlo dinámico o pedir configuración.
        
        // SOLUCIÓN ROBUSTA PARA SUBCARPETAS:
        // Restamos el directorio del script actual a la URI solicitada.
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']); // ej: /inmobiliaria/public
        
        // Si estamos en la raíz, scriptDir será '/' o '\' (windows).
        if ($scriptDir !== '/' && $scriptDir !== '\\') {
            if (str_starts_with($uriPath, $scriptDir)) {
                $uriPath = substr($uriPath, strlen($scriptDir));
            }
        }
        
        // Aseguramos que empiece por /
        if ($uriPath === '' || !str_starts_with($uriPath, '/')) {
            $uriPath = '/' . $uriPath;
        }

        // Buscamos coincidencia
        if (isset($this->routes[$method][$uriPath])) {
            $action = $this->routes[$method][$uriPath];

            // Si es un Closure (función anónima)
            if (is_callable($action)) {
                call_user_func($action);
                return;
            }

            // Si es un array [Controlador, Método]
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

        // Si no encontramos ruta: 404
        $this->handle404();
    }

    /**
     * Manejo básico de error 404.
     */
    private function handle404(): void
    {
        http_response_code(404);
        echo "<h1>404 Not Found</h1>";
        echo "<p>La página que buscas no existe.</p>";
        // Aquí podrías hacer: require VIEW . '/404.php';
    }
}
