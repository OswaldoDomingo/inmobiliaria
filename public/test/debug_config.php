<?php
/**
 * Debug de Config::get
 */

declare(strict_types=1);

define('ROOT', dirname(__DIR__, 2));
define('CONFIG', ROOT . '/config');
define('APP', ROOT . '/app');
define('VIEW', APP . '/Views');

require_once APP . '/Core/Env.php';
require_once APP . '/Core/Config.php';

use App\Core\Env;
use App\Core\Config;

// 1. Cargar Env
Env::load(CONFIG . '/.env');

// 2. Cargar Config
$configData = require CONFIG . '/config.php';
Config::init($configData);

echo "<pre>";
echo "<h1>Diagnóstico de Config::get()</h1>";

echo "<h3>1. Configuración SMTP (Raw Data)</h3>";
print_r(Config::get('smtp'));

echo "<h3>2. Configuración Emails (Raw Data)</h3>";
print_r(Config::get('emails'));

echo "<h3>3. Valores Específicos</h3>";
echo "Config::get('smtp.user'): [" . Config::get('smtp.user') . "]\n";
echo "Config::get('emails.noreply'): [" . Config::get('emails.noreply') . "]\n";

echo "<h3>4. Prueba de Lógica MailService</h3>";
$fromEmail = Config::get('emails.noreply') ?? Config::get('smtp.user') ?? '';
echo "Valor calculado de \$fromEmail: [" . $fromEmail . "]\n";
echo "Condición empty(\$fromEmail): " . (empty($fromEmail) ? "TRUE (Falla)" : "FALSE (Pasa)") . "\n";
