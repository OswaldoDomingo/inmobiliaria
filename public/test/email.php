<?php
/**
 * Script de Prueba de Envío de Emails
 * 
 * Acceder vía navegador: /test/email.php
 */

declare(strict_types=1);

define('ROOT', dirname(__DIR__, 2));
define('VIEW', ROOT . '/app/Views');
define('CONFIG', ROOT . '/config');
define('APP', ROOT . '/app');

// Cargar Autoloader si es necesario, o require manual de Env
require_once APP . '/Core/Env.php';
use App\Core\Env;

// Cargar .env
Env::load(CONFIG . '/.env');

$config = require ROOT . '/config/config.php';
require ROOT . '/app/Core/Config.php';
App\Core\Config::init($config);
require ROOT . '/app/Services/MailService.php';

use App\Services\MailService;

$sent = false;
$error = '';
$testEmail = '';

    // Forzar el debug del HTML renderizado
    if (isset($_GET['debug_html'])) {
        $templateData = [
            'titulo' => 'Tu Valoración Inmobiliaria',
            'precio_min' => '150.000 €',
            'precio_max' => '180.000 €',
            'barrio' => 'Salamanca',
            'zona' => 'Recoletos',
            'cp' => '28001',
            'superficie' => '90',
            'caracteristicas' => 'Exterior, Con Ascensor, Test'
        ];
        
        // Simular renderizado manual
        extract($templateData);
        ob_start();
        include VIEW . '/emails/tasacion_cliente.php';
        $content = ob_get_clean();
        
        ob_start();
        include VIEW . '/emails/layout.php';
        $fullHtml = ob_get_clean();
        
        echo "<h1>Vista Previa del HTML (Debug)</h1>";
        echo "<div style='border: 1px solid #ccc; padding: 20px;'>$fullHtml</div>";
        echo "<h2>Código Fuente:</h2>";
        echo "<textarea style='width: 100%; height: 400px;'>" . htmlspecialchars($fullHtml) . "</textarea>";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testEmail = $_POST['email'] ?? '';
    
    if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email inválido';
    } else {
        try {
            MailService::send(
                $testEmail,
                'Test Email - CRM Inmobiliaria',
                [
                    'template' => 'tasacion_cliente',
                    'data' => [
                        'precio_min' => '150,000€',
                        'precio_max' => '180,000€',
                        'barrio' => 'Salamanca',
                        'zona' => 'Recoletos',
                        'cp' => '28001',
                        'superficie' => '90',
                        'caracteristicas' => 'Exterior, Con Ascensor, Test'
                    ]
                ]
            );
            $sent = true;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test de Emails - CRM</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; color: #721c24; }
        input[type="email"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🧪 Test de Envío de Emails</h1>
    
    <?php if ($sent): ?>
        <div class="success">✅ Email enviado correctamente a <?= htmlspecialchars($testEmail) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error">❌ Error: <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <label>Tu email para recibir la prueba:</label>
        <input type="email" name="email" required placeholder="tu@email.com" value="<?= htmlspecialchars($testEmail) ?>">
        <button type="submit">Enviar Email de Prueba</button>
    </form>
    
    <p style="margin-top: 30px; color: #666; font-size: 14px;">
        Este script prueba el envío de emails usando MailService y la plantilla tasacion_cliente.
    </p>
</body>
</html>
