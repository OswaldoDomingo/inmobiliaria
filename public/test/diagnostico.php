<?php
/**
 * Diagnóstico de Configuración SMTP
 * Muestra la configuración actual SIN exponer contraseñas
 */

declare(strict_types=1);

define('ROOT', dirname(__DIR__, 2));
define('VIEW', ROOT . '/app/Views');

// Cargar configuración
$config = require ROOT . '/config/config.php';
require ROOT . '/app/Core/Config.php';
App\Core\Config::init($config);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico SMTP - CRM</title>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            max-width: 900px; 
            margin: 30px auto; 
            padding: 20px; 
            background: #1a1a1a;
            color: #00ff00;
        }
        h1 { color: #00ff00; border-bottom: 2px solid #00ff00; padding-bottom: 10px; }
        h2 { color: #ffff00; margin-top: 30px; }
        .ok { color: #00ff00; font-weight: bold; }
        .error { color: #ff0000; font-weight: bold; }
        .warning { color: #ffaa00; font-weight: bold; }
        .info { color: #00aaff; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        td { padding: 8px; border: 1px solid #333; }
        td:first-child { width: 30%; font-weight: bold; color: #ffff00; }
        .section { background: #2a2a2a; padding: 15px; margin: 15px 0; border-left: 4px solid #00ff00; }
        .masked { background: #333; color: #666; }
        pre { background: #2a2a2a; padding: 15px; overflow-x: auto; border-left: 4px solid #00aaff; }
    </style>
</head>
<body>
    <h1>🔍 DIAGNÓSTICO SMTP - CRM Inmobiliaria</h1>
    <p class="info">Generado: <?= date('d/m/Y H:i:s') ?></p>

    <?php
    // 1. CONFIGURACIÓN SMTP
    echo "<h2>1. Configuración SMTP</h2>";
    echo "<div class='section'>";
    
    $smtpConfig = App\Core\Config::get('smtp', []);
    $host = $smtpConfig['host'] ?? '';
    $port = $smtpConfig['port'] ?? 0;
    $user = $smtpConfig['user'] ?? '';
    $pass = $smtpConfig['pass'] ?? '';
    $secure = $smtpConfig['secure'] ?? '';
    
    echo "<table>";
    echo "<tr><td>SMTP_HOST</td><td>" . ($host ? "<span class='ok'>✓ Configurado: $host</span>" : "<span class='error'>✗ NO configurado</span>") . "</td></tr>";
    echo "<tr><td>SMTP_PORT</td><td>" . ($port ? "<span class='ok'>✓ $port</span>" : "<span class='error'>✗ NO configurado</span>") . "</td></tr>";
    echo "<tr><td>SMTP_USER</td><td>" . ($user ? "<span class='ok'>✓ " . substr($user, 0, 3) . "***@" . substr(strstr($user, '@'), 1) . "</span>" : "<span class='error'>✗ NO configurado</span>") . "</td></tr>";
    echo "<tr><td>SMTP_PASS</td><td>" . ($pass ? "<span class='ok'>✓ Configurado (" . strlen($pass) . " caracteres)</span>" : "<span class='error'>✗ NO configurado</span>") . "</td></tr>";
    echo "<tr><td>SMTP_SECURE</td><td>" . ($secure ? "<span class='ok'>✓ $secure</span>" : "<span class='warning'>⚠ none (sin cifrado)</span>") . "</td></tr>";
    echo "</table>";
    echo "</div>";

    // 2. EMAILS DE DESTINO
    echo "<h2>2. Emails de Destino</h2>";
    echo "<div class='section'>";
    $agencyEmail = App\Core\Config::get('emails.agency', '');
    $noreplyEmail = App\Core\Config::get('emails.noreply', '');
    
    echo "<table>";
    echo "<tr><td>LEAD_AGENCY_EMAIL</td><td>" . ($agencyEmail ? "<span class='ok'>✓ $agencyEmail</span>" : "<span class='error'>✗ NO configurado</span>") . "</td></tr>";
    echo "<tr><td>NOREPLY_EMAIL</td><td>" . ($noreplyEmail ? "<span class='ok'>✓ $noreplyEmail</span>" : "<span class='warning'>⚠ NO configurado (se usará SMTP_USER)</span>") . "</td></tr>";
    echo "</table>";
    echo "</div>";

    // 3. ARCHIVOS REQUERIDOS
    echo "<h2>3. Archivos del Sistema</h2>";
    echo "<div class='section'>";
    $files = [
        'PHPMailer/PHPMailer.php' => ROOT . '/app/Lib/PHPMailer/PHPMailer.php',
        'PHPMailer/SMTP.php' => ROOT . '/app/Lib/PHPMailer/SMTP.php',
        'PHPMailer/Exception.php' => ROOT . '/app/Lib/PHPMailer/Exception.php',
        'MailService.php' => ROOT . '/app/Services/MailService.php',
        'Template layout.php' => ROOT . '/app/Views/emails/layout.php',
        'Template tasacion_cliente.php' => ROOT . '/app/Views/emails/tasacion_cliente.php',
        'Template tasacion_agencia.php' => ROOT . '/app/Views/emails/tasacion_agencia.php',
    ];
    
    echo "<table>";
    foreach ($files as $name => $path) {
        $exists = file_exists($path);
        echo "<tr><td>$name</td><td>" . ($exists ? "<span class='ok'>✓ Existe</span>" : "<span class='error'>✗ NO existe</span>") . "</td></tr>";
    }
    echo "</table>";
    echo "</div>";

    // 4. TEST DE CONEXIÓN SMTP
    echo "<h2>4. Test de Conexión SMTP</h2>";
    echo "<div class='section'>";
    
    if ($host && $port) {
        echo "<p>Intentando conectar a <code>$host:$port</code>...</p>";
        
        $errno = 0;
        $errstr = '';
        $timeout = 5;
        
        $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
        
        if ($socket) {
            echo "<p class='ok'>✓ Conexión TCP exitosa al servidor SMTP</p>";
            fclose($socket);
        } else {
            echo "<p class='error'>✗ NO se puede conectar al servidor SMTP</p>";
            echo "<p class='error'>Error: [$errno] $errstr</p>";
            echo "<p class='warning'>Posibles causas:</p>";
            echo "<ul>";
            echo "<li>El host o puerto son incorrectos</li>";
            echo "<li>Firewall bloqueando la conexión</li>";
            echo "<li>El servidor SMTP está caído</li>";
            echo "<li>Necesitas estar conectado a internet</li>";
            echo "</ul>";
        }
    } else {
        echo "<p class='error'>✗ No se puede probar la conexión: faltan SMTP_HOST o SMTP_PORT</p>";
    }
    echo "</div>";

    // 5. LOGS
    echo "<h2>5. Logs de Email</h2>";
    echo "<div class='section'>";
    $logFile = ROOT . '/logs/mail.log';
    
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        $lastLines = array_slice(array_filter($lines), -20); // Últimas 20 líneas
        
        echo "<p class='ok'>✓ Archivo de log existe</p>";
        echo "<p><strong>Últimas 20 líneas:</strong></p>";
        echo "<pre>" . htmlspecialchars(implode("\n", $lastLines)) . "</pre>";
    } else {
        echo "<p class='warning'>⚠ No hay archivo de log todavía (se creará en el primer envío)</p>";
    }
    echo "</div>";

    // 6. DIAGNÓSTICO FINAL
    echo "<h2>6. Diagnóstico y Recomendaciones</h2>";
    echo "<div class='section'>";
    
    $issues = [];
    
    if (!$host) $issues[] = "Falta SMTP_HOST en .env";
    if (!$port) $issues[] = "Falta SMTP_PORT en .env";
    if (!$user) $issues[] = "Falta SMTP_USER en .env";
    if (!$pass) $issues[] = "Falta SMTP_PASS en .env";
    if (!$agencyEmail) $issues[] = "Falta LEAD_AGENCY_EMAIL en .env";
    
    if (empty($issues)) {
        echo "<p class='ok'>✓ Configuración básica completa</p>";
        echo "<p class='info'>Si aún no funciona, revisa:</p>";
        echo "<ul>";
        echo "<li>Que las credenciales sean correctas</li>";
        echo "<li>Que tu proveedor SMTP permita el acceso desde esta IP</li>";
        echo "<li>Si usas Gmail, que tengas una 'App Password' (no la contraseña normal)</li>";
        echo "<li>Los logs arriba para ver el error específico</li>";
        echo "</ul>";
    } else {
        echo "<p class='error'>✗ Problemas detectados:</p>";
        echo "<ul>";
        foreach ($issues as $issue) {
            echo "<li class='error'>$issue</li>";
        }
        echo "</ul>";
        echo "<p class='info'>Edita tu archivo <code>.env</code> y añade las variables faltantes.</p>";
        echo "<p class='info'>Puedes copiar desde <code>.env.example</code> como referencia.</p>";
    }
    echo "</div>";

    // 7. EJEMPLO DE .ENV
    if (!empty($issues)) {
        echo "<h2>7. Ejemplo de Configuración .env</h2>";
        echo "<div class='section'>";
        echo "<pre>";
        echo "# Ejemplo para Gmail:\n";
        echo "SMTP_HOST=smtp.gmail.com\n";
        echo "SMTP_PORT=587\n";
        echo "SMTP_SECURE=tls\n";
        echo "SMTP_USER=tu-cuenta@gmail.com\n";
        echo "SMTP_PASS=tu-app-password  # NO la contraseña normal!\n\n";
        echo "LEAD_AGENCY_EMAIL=agencia@tudominio.com\n";
        echo "NOREPLY_EMAIL=noreply@tudominio.com\n";
        echo "</pre>";
        echo "</div>";
    }
    ?>

    <hr style="margin: 40px 0; border-color: #333;">
    <p class="info" style="text-align: center;">
        <strong>Siguiente paso:</strong> Intenta enviar un email de prueba desde 
        <a href="/test/email.php" style="color: #00aaff;">/test/email.php</a>
    </p>

</body>
</html>
