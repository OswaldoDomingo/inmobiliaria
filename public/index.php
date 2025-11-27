<?php
declare(strict_types=1);

use App\Core\Database;
use App\Core\Router;

// ===============================
// DEBUG SOLO EN DESARROLLO
// ===============================
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// ===============================
// 0. Constantes de rutas
// ===============================
define('ROOT', dirname(__DIR__));        // C:\servidor\apache24\htdocs\inmobiliaria
define('APP', ROOT . '/app');            // ...\inmobiliaria\app
define('CONFIG', ROOT . '/config');      // ...\inmobiliaria\config
define('VIEW', APP . '/views');          // ...\inmobiliaria\app\views

// ===============================
// 1. Cargar configuración
// ===============================
$config = require CONFIG . '/config.php';

// ===============================
// 2. Cargar Autoloader
// ===============================
$autoloadPath = APP . '/Autoloader.php';

if (file_exists($autoloadPath)) {
    require_once $autoloadPath;

    if (class_exists(\App\Autoloader::class)) {
        \App\Autoloader::register(ROOT);
    }
}

// ===============================
// 3. Cargar Database
// ===============================
// El Autoloader se encargará de cargar App\Core\Database cuando sea necesario.
// Eliminamos la carga manual para evitar conflictos de rutas/casing.

// ===============================
// 4. Inicializar Router y Definir Rutas
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

$router->get('/admin/usuarios', [UserController::class, 'index']);
$router->get('/admin/usuarios/nuevo', [UserController::class, 'create']);
$router->post('/admin/usuarios/guardar', [UserController::class, 'store']);
$router->get('/admin/usuarios/editar', [UserController::class, 'edit']);
$router->post('/admin/usuarios/actualizar', [UserController::class, 'update']);
$router->post('/admin/usuarios/baja', [UserController::class, 'delete']);

// ===============================
// 5. Despachar la petición
// ===============================
$router->dispatch();
