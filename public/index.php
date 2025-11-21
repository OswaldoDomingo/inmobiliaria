<?php

declare(strict_types=1);

// ===============================
// 0. Constantes de rutas
// ===============================
define('ROOT', dirname(__DIR__));        // C:\servidor\apache24\htdocs\inmobiliaria
define('APP', ROOT . '/app');            // ...\inmobiliaria\app
define('CONFIG', ROOT . '/config');      // ...\inmobiliaria\config
define('VIEW', APP . '/views');          // ajusta si usas Views/ en mayúscula

// ===============================
// 1. Cargar configuración
// ===============================
$config = require CONFIG . '/config.php';

// ===============================
// 2. Cargar Autoloader y Database
// ===============================

// --- AUTLOADER ---
$autoloadCore = APP . '/core/Autoloader.php';
$autoloadRoot = APP . '/Autoloader.php';

if (file_exists($autoloadCore)) {
    require_once $autoloadCore;
} elseif (file_exists($autoloadRoot)) {
    require_once $autoloadRoot;
} else {
    die("<h1>Error de arranque</h1>
         <p>No se ha encontrado <code>Autoloader.php</code> ni en <code>app/core</code> ni en <code>app/</code>.</p>
         <p>Rutas probadas:</p>
         <ul>
            <li>$autoloadCore</li>
            <li>$autoloadRoot</li>
         </ul>");
}

// Registrar autoloader si existe esa clase
if (class_exists('Autoloader')) {
    Autoloader::registrar();
}

// --- DATABASE ---
$dbPathCore = APP . '/core/Database.php';
$dbPathRoot = APP . '/Database.php';

if (file_exists($dbPathCore)) {
    require_once $dbPathCore;
} elseif (file_exists($dbPathRoot)) {
    require_once $dbPathRoot;
} else {
    die("<h1>Error de arranque</h1>
         <p>No se ha encontrado <code>Database.php</code> ni en <code>app/core</code> ni en <code>app/</code>.</p>
         <p>Rutas probadas:</p>
         <ul>
            <li>$dbPathCore</li>
            <li>$dbPathRoot</li>
         </ul>");
}

// ===============================
// 3. Prueba rápida
// ===============================

echo "<h1>🔧 Sistema Iniciado</h1>";
echo "<p>Conectando a base de datos: " . htmlspecialchars($config['db']['dbname']) . "</p>";
echo "<p>Ruta APP física: " . APP . "</p>";

echo "<hr>";
echo "<h3>Prueba de Base de Datos:</h3>";

try {
    $db = Database::conectar();

    $sql   = "SELECT * FROM usuarios WHERE rol = 'admin' LIMIT 1";
    $query = $db->query($sql);
    $admin = $query->fetch();

    if ($admin) {
        echo "<div style='color: green;'>✅ Conexión Exitosa. Admin encontrado: "
            . htmlspecialchars($admin->nombre) . " ("
            . htmlspecialchars($admin->email) . ")</div>";
    } else {
        echo "<div style='color: orange;'>⚠️ Conexión correcta, pero no se encontró ningún usuario con rol = 'admin'.</div>";
    }
} catch (Throwable $e) {
    echo "<div style='color: red;'>❌ Error al conectar o consultar la base de datos: "
        . htmlspecialchars($e->getMessage()) . "</div>";
}
