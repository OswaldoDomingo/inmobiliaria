<?php
declare(strict_types=1);

use App\Core\Database;

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
// 2. Cargar Autoloader (si lo tienes ya creado)
// ===============================
$autoloadPath = APP . '/Autoloader.php';

if (file_exists($autoloadPath)) {
    require_once $autoloadPath;

    if (class_exists(\App\Autoloader::class)) {
        \App\Autoloader::register(ROOT);
    }
}

// ===============================
// 3. Cargar Database (tal y como la tienes ahora)
// ===============================
$dbPathCore = APP . '/core/Database.php';
$dbPathRoot = APP . '/Database.php';

if (file_exists($dbPathCore)) {
    require_once $dbPathCore;
} elseif (file_exists($dbPathRoot)) {
    require_once $dbPathRoot;
} else {
    die("<h1>Error de arranque</h1>
         <p>No se ha encontrado <code>Database.php</code> ni en <code>app/core</code> ni en <code>app/</code>.</p>");
}

// ===============================
// 4. Lógica mínima: probar BD
// ===============================

$admin   = null;
$dbError = null;

try {
    // OJO: aquí usamos la clase Database que ya te funcionaba
    $db = Database::conectar();

    $sql   = "SELECT * FROM usuarios WHERE rol = 'admin' LIMIT 1";
    $query = $db->query($sql);
    $admin = $query->fetch();
} catch (Throwable $e) {
    $dbError = $e->getMessage();
}

// ===============================
// 5. Cargar la vista de la landing
// ===============================
require VIEW . '/landing.php';
