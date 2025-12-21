<?php
declare(strict_types=1);

use App\Core\Config;
use App\Core\Env;
use App\Core\Router;

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

Env::load(CONFIG . '/.env');

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
error_reporting($debug ? E_ALL : E_ALL & ~E_NOTICE);

set_exception_handler(function (\Throwable $e) use ($debug) {
    if ($e instanceof \PDOException) {
        http_response_code(500);
        error_log('DB ERROR: ' . $e->getMessage());
        echo '<h1>Error de sistema</h1><p>No se ha podido completar la operacion.</p>';
        return;
    }
    if ($debug) {
        throw $e;
    }
    http_response_code(500);
    error_log($e->getMessage());
    echo '<h1>Error de sistema</h1><p>Intentalo de nuevo mas tarde.</p>';
});

// ===============================
// 4. Seguridad de sesión
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

// Mapear HEAD a GET para soportar health checks y curl -I
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'HEAD') {
    $_SERVER['REQUEST_METHOD'] = 'GET';
}

$router = new Router();

// --------------------------------------------------------------------------
// Rutas Públicas
// --------------------------------------------------------------------------
use App\Controllers\HomeController;
use App\Controllers\LegalController;
use App\Controllers\QuienesSomosController;
use App\Controllers\TasacionController;
use App\Controllers\AuthController;
use App\Controllers\InmueblePublicController;

// Landing
$router->get('/', [HomeController::class, 'index']);

// Legal
$router->get('/legal/aviso-legal', [LegalController::class, 'avisoLegal']);
$router->get('/legal/privacidad', [LegalController::class, 'privacidad']);
$router->get('/legal/cookies', [LegalController::class, 'cookies']);

// Quiénes somos
$router->get('/quienes-somos', [QuienesSomosController::class, 'index']);

// Tasación
$router->get('/tasacion', [TasacionController::class, 'index']);
$router->post('/tasacion/enviar', [TasacionController::class, 'enviar']);

//Vende 
$router->get('/vende', [HomeController::class, 'vende']);

// Auth
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'authenticate']);
$router->get('/logout', [AuthController::class, 'logout']);

// Propiedades Público
$router->get('/propiedades', [InmueblePublicController::class, 'index']);
$router->get('/propiedades/ver', [InmueblePublicController::class, 'show']);

// Contacto Público
use App\Controllers\ContactController;
$router->get('/contacto', [ContactController::class, 'index']);
$router->post('/contacto/enviar', [ContactController::class, 'enviar']);

// --------------------------------------------------------------------------
// Rutas de Administración
// --------------------------------------------------------------------------
use App\Controllers\UserController;
use App\Controllers\ClienteController;
use App\Controllers\InmuebleController;
use App\Controllers\DemandaController;
use App\Controllers\LogController;

$router->get('/dashboard', function() {
    require VIEW . '/admin/dashboard.php';
});

// Logs
$router->get('/admin/logs', [LogController::class, 'index']);

// Usuarios
$router->get('/admin/usuarios', [UserController::class, 'index']);
$router->get('/admin/usuarios/nuevo', [UserController::class, 'create']);
$router->post('/admin/usuarios/guardar', [UserController::class, 'store']);
$router->get('/admin/usuarios/editar', [UserController::class, 'edit']);
$router->post('/admin/usuarios/actualizar', [UserController::class, 'update']);
$router->post('/admin/usuarios/baja', [UserController::class, 'delete']);
$router->post('/admin/usuarios/cambiar-bloqueo', [UserController::class, 'toggleBlock']);

// Clientes
$router->get('/admin/clientes', [ClienteController::class, 'index']);
$router->get('/admin/clientes/nuevo', [ClienteController::class, 'create']);
$router->post('/admin/clientes/guardar', [ClienteController::class, 'store']);
$router->get('/admin/clientes/editar', [ClienteController::class, 'edit']);
$router->post('/admin/clientes/actualizar', [ClienteController::class, 'update']);
$router->post('/admin/clientes/borrar', [ClienteController::class, 'delete']);

// Inmuebles (Admin)
$router->get('/admin/inmuebles', [InmuebleController::class, 'index']);
$router->get('/admin/inmuebles/nuevo', [InmuebleController::class, 'create']);
$router->post('/admin/inmuebles/guardar', [InmuebleController::class, 'store']);
$router->get('/admin/inmuebles/editar', [InmuebleController::class, 'edit']);
$router->post('/admin/inmuebles/actualizar', [InmuebleController::class, 'update']);
$router->post('/admin/inmuebles/borrar', [InmuebleController::class, 'delete']);

// Demandas
$router->get('/admin/demandas', [DemandaController::class, 'index']);
$router->get('/admin/demandas/nueva', [DemandaController::class, 'create']);
$router->post('/admin/demandas/guardar', [DemandaController::class, 'store']);
$router->get('/admin/demandas/editar', [DemandaController::class, 'edit']);
$router->post('/admin/demandas/actualizar', [DemandaController::class, 'update']);
$router->post('/admin/demandas/borrar', [DemandaController::class, 'delete']);

// ===============================
// 6. Despachar
// ===============================
$router->dispatch();
