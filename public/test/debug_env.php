<?php
/**
 * Debug de Variables de Entorno
 */

declare(strict_types=1);

define('ROOT', dirname(__DIR__, 2));
define('CONFIG', ROOT . '/config');
define('APP', ROOT . '/app');

require_once APP . '/Core/Env.php';
use App\Core\Env;

// Cargar .env
Env::load(CONFIG . '/.env');

echo "<pre>";
echo "<h3>1. Variables Cargadas (.env)</h3>";
$content = file_get_contents(CONFIG . '/.env');
echo htmlspecialchars($content);

echo "<h3>2. Estado de Variables en PHP</h3>";
$vars = [
    'SMTP_HOST',
    'SMTP_PORT',
    'SMTP_USER',
    'SMTP_PASS',
    'LEAD_AGENCY_EMAIL',
    'NOREPLY_EMAIL'
];

foreach ($vars as $var) {
    echo "<strong>$var:</strong>\n";
    echo "  getenv(): " . var_export(getenv($var), true) . "\n";
    echo "  \$_ENV[]: " . var_export($_ENV[$var] ?? 'NOT SET', true) . "\n";
    echo "\n";
}

echo "<h3>3. Prueba de Env::load</h3>";
// Forzar recarga para ver si cambia algo
echo "Recargando...\n";
Env::load(CONFIG . '/.env');
foreach ($vars as $var) {
    echo "<strong>$var:</strong> " . var_export(getenv($var), true) . "\n";
}
