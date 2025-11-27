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
// 3. Cargar Database (Legacy fallback)
// ===============================
// Mantenemos esto por si algún script antiguo depende de ello, 
// pero idealmente el Autoloader ya se encarga de App\Core\Database.
$dbPathCore = APP . '/core/Database.php';
$dbPathRoot = APP . '/Database.php';

if (file_exists($dbPathCore)) {
    require_once $dbPathCore;
} elseif (file_exists($dbPathRoot)) {
    require_once $dbPathRoot;
} else {
    // Si el autoloader funciona, esto podría sobrar, pero lo dejamos por seguridad.
}

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
// 5. Despachar la petición
// ===============================
$router->dispatch();
