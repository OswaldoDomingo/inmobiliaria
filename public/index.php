<?php
// public/index.php
require_once dirname(__DIR__) . '/config/env.php';
// 1. Cargar Configuración
require_once '../config/config.php';

// 2. Cargar Autoloader
require_once APP . 'Autoloader.php';
Autoloader::registrar();

// 3. Prueba rápida de que las constantes funcionan
echo "<h1>🔧 Sistema Iniciado</h1>";
echo "<p>Conectando a base de datos: " . DB_NAME . "</p>";
echo "<p>Ruta APP física: " . APP . "</p>";

// Aquí pronto instaciaremos el Router:
// $router = new Router();
// $router->run();
// ... código anterior ...

echo "<hr>";
echo "<h3>Prueba de Base de Datos:</h3>";

try {
    // Intentamos obtener el usuario administrador que creamos con el SQL
    $db = Database::conectar();
    $sql = "SELECT * FROM usuarios WHERE rol = 'admin' LIMIT 1";
    $query = $db->query($sql);
    $admin = $query->fetch();

    if ($admin) {
        echo "<div style='color: green;'>✅ Conexión Exitosa. Admin encontrado: " . $admin->nombre . " (" . $admin->email . ")</div>";
    } else {
        echo "<div style='color: orange;'>⚠️ Conexión buena, pero no encontré al usuario Admin.</div>";
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Error: " . $e->getMessage() . "</div>";
}