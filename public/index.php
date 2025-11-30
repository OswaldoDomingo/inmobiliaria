<?php
declare(strict_types=1);

use App\Core\Config;
use App\Core\Env;
use App\Core\Router;
use PDOException;

// ===============================
// 0. Constantes de rutas
// ===============================
define('ROOT', dirname(__DIR__));
define('APP', ROOT . '/app');
define('CONFIG', ROOT . '/config');
define('VIEW', APP . '/views');

// ===============================
// 1. Cargar entorno (.env) antes de la configuración
// ===============================
require_once APP . '/Autoloader.php';
\App\Autoloader::register(ROOT);

Env::load(ROOT . '/.env');

// ===============================
// 2. Cargar configuración centralizada
// ===============================
$config = require CONFIG . '/config.php';
Config::init($config);

// ===============================
// 3. Configuración de errores según entorno
// ===============================
$debug = (bool)Config::get('app.debug', false);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('display_startup_errors', $debug ? '1' : '0');
error_reporting($debug ? E_ALL : E_ALL & ~E_NOTICE & ~E_STRICT);

set_exception_handler(function (\Throwable $e) use ($debug) {
    if ($e instanceof PDOException) {
        http_response_code(500);
        error_log('DB ERROR: ' . $e->getMessage());
        echo '<h1>Error de sistema</h1><p>No se ha podido completar la operacion.</p>';
        return;
    }
    // Re-lanzar si estamos en debug para ver trazas
    if ($debug) {
        throw $e;
    }
    http_response_code(500);
    error_log($e->getMessage());
    echo '<h1>Error de sistema</h1><p>Intentalo de nuevo mas tarde.</p>';
});

// ===============================
// 4. Seguridad de sesión (cookies endurecidas)
// ===============================
$secureCookies = Config::get('env') === 'production';
session_set_cookie_params([
    'httponly' => true,
    'secure'   => $secureCookies,
    'samesite' => 'Lax',
    'path'     => '/',
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ===============================
// 5. Inicializar Router y Definir Rutas
// ===============================

$router = new Router();

// Ruta Raíz (Landing Page)
use App\Controllers\HomeController;

$router->get('/', [HomeController::class, 'index']);

// Ruta de prueba para verificar 404 u otras páginas
$router->get('/prueba', function () {
    echo "<h1>¡El Router funciona!</h1>";
});

// ===============================
// RUTAS DE TASACIÓN
// ===============================
use App\Controllers\TasacionController;

$router->get('/tasacion', [TasacionController::class, 'index']);
$router->post('/tasacion/enviar', [TasacionController::class, 'enviar']);

// ===============================
// RUTAS DE AUTENTICACIÓN
// ===============================
use App\Controllers\AuthController;

$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'authenticate']);
$router->get('/logout', [AuthController::class, 'logout']);

// ===============================
// RUTAS DE ADMINISTRACIÓN
// ===============================
$router->get('/dashboard', function() {
    require VIEW . '/admin/dashboard.php';
});

// ===============================
// RUTAS DE GESTIÓN DE USUARIOS (CRUD)
// ===============================
use App\Controllers\UserController;
use App\Controllers\ClienteController;

$router->get('/admin/usuarios', [UserController::class, 'index']);
$router->get('/admin/usuarios/nuevo', [UserController::class, 'create']);
$router->post('/admin/usuarios/guardar', [UserController::class, 'store']);
$router->get('/admin/usuarios/editar', [UserController::class, 'edit']);
$router->post('/admin/usuarios/actualizar', [UserController::class, 'update']);
$router->post('/admin/usuarios/baja', [UserController::class, 'delete']);
$router->post('/admin/usuarios/cambiar-bloqueo', [UserController::class, 'toggleBlock']);

// ===============================
// RUTAS DE CLIENTES
// ===============================
$router->get('/admin/clientes', [ClienteController::class, 'index']);
$router->get('/admin/clientes/nuevo', [ClienteController::class, 'create']);
$router->post('/admin/clientes/guardar', [ClienteController::class, 'store']);
$router->get('/admin/clientes/editar', [ClienteController::class, 'edit']);
$router->post('/admin/clientes/actualizar', [ClienteController::class, 'update']);
$router->post('/admin/clientes/borrar', [ClienteController::class, 'delete']);

// Rutas de Admin (Logs)
use App\Controllers\LogController;
$router->get('/admin/logs', [LogController::class, 'index']);

// ===============================
// 6. Despachar la petición
// ===============================
$router->dispatch();
