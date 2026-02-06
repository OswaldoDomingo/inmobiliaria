<?php
/**
 * Diagnóstico de Login
 * ⚠️ ELIMINAR DESPUÉS DE USAR - Contiene información sensible
 */

declare(strict_types=1);

define('ROOT', dirname(__DIR__, 2));
define('CONFIG', ROOT . '/config');
define('APP', ROOT . '/app');

require_once APP . '/Autoloader.php';
\App\Autoloader::register(ROOT);

use App\Core\Env;
use App\Core\Database;
use App\Models\User;

// Cargar .env
Env::load(CONFIG . '/.env');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Diagnóstico Login</title>
    <style>
        body { 
            font-family: 'Courier New', monospace; 
            max-width: 900px; 
            margin: 30px auto; 
            padding: 20px; 
            background: #1a1a1a;
            color: #00ff00;
        }
        h1, h2 { color: #00ff00; border-bottom: 2px solid #00ff00; padding-bottom: 10px; }
        .ok { color: #00ff00; font-weight: bold; }
        .error { color: #ff0000; font-weight: bold; }
        .warning { color: #ffaa00; font-weight: bold; }
        .info { color: #00aaff; }
        .section { background: #2a2a2a; padding: 15px; margin: 15px 0; border-left: 4px solid #00ff00; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        td, th { padding: 8px; border: 1px solid #333; text-align: left; }
        th { background: #333; color: #ffff00; }
        pre { background: #2a2a2a; padding: 15px; overflow-x: auto; }
        input, button { padding: 10px; margin: 5px; font-size: 14px; }
        button { background: #00aa00; color: white; border: none; cursor: pointer; }
        button:hover { background: #00cc00; }
    </style>
</head>
<body>
    <h1>🔐 DIAGNÓSTICO DE LOGIN</h1>
    <p class="warning">⚠️ ELIMINA ESTE ARCHIVO DESPUÉS DE USAR</p>
    <p class="info">Generado: <?= date('d/m/Y H:i:s') ?></p>

    <?php
    // 1. VERIFICAR CONEXIÓN A BASE DE DATOS
    echo "<h2>1. Conexión a Base de Datos</h2>";
    echo "<div class='section'>";
    
    try {
        $pdo = Database::conectar();
        echo "<p class='ok'>✓ Conexión exitosa a la base de datos</p>";
        
        // Mostrar info de conexión (sin contraseña)
        echo "<table>";
        echo "<tr><th>Variable</th><th>Valor</th></tr>";
        echo "<tr><td>DB_HOST</td><td>" . ($_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'NO DEFINIDO') . "</td></tr>";
        echo "<tr><td>DB_NAME</td><td>" . ($_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'NO DEFINIDO') . "</td></tr>";
        echo "<tr><td>DB_USER</td><td>" . ($_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'NO DEFINIDO') . "</td></tr>";
        echo "<tr><td>DB_PASS</td><td>****** (configurado)</td></tr>";
        echo "</table>";
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error de conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p class='info'>Verifica las credenciales en config/.env</p>";
        echo "</div></body></html>";
        exit;
    }
    echo "</div>";

    // 2. LISTAR USUARIOS
    echo "<h2>2. Usuarios en Base de Datos</h2>";
    echo "<div class='section'>";
    
    try {
        $stmt = $pdo->query("SELECT id_usuario, nombre, email, rol, activo, cuenta_bloqueada, intentos_fallidos, password_hash FROM usuarios ORDER BY id_usuario");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($usuarios)) {
            echo "<p class='error'>✗ No hay usuarios en la base de datos</p>";
        } else {
            echo "<p class='ok'>✓ Encontrados " . count($usuarios) . " usuarios</p>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Activo</th><th>Bloqueado</th><th>Intentos</th><th>Hash válido</th></tr>";
            
            foreach ($usuarios as $u) {
                $hashValido = !empty($u['password_hash']) && strlen($u['password_hash']) >= 60;
                $hashClass = $hashValido ? 'ok' : 'error';
                
                echo "<tr>";
                echo "<td>{$u['id_usuario']}</td>";
                echo "<td>" . htmlspecialchars($u['nombre']) . "</td>";
                echo "<td>" . htmlspecialchars($u['email']) . "</td>";
                echo "<td>{$u['rol']}</td>";
                echo "<td>" . ($u['activo'] ? '✓' : '✗') . "</td>";
                echo "<td>" . ($u['cuenta_bloqueada'] ? '<span class="error">SÍ</span>' : 'No') . "</td>";
                echo "<td>{$u['intentos_fallidos']}</td>";
                echo "<td class='$hashClass'>" . ($hashValido ? '✓ ' . strlen($u['password_hash']) . ' chars' : '✗ INVÁLIDO') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    echo "</div>";

    // 3. PROBAR LOGIN
    echo "<h2>3. Probar Login</h2>";
    echo "<div class='section'>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
        $email = $_POST['test_email'];
        $password = $_POST['test_password'];
        
        echo "<h3>Resultado del test:</h3>";
        
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if (!$user) {
            echo "<p class='error'>✗ Usuario no encontrado con email: " . htmlspecialchars($email) . "</p>";
        } else {
            echo "<p class='ok'>✓ Usuario encontrado: " . htmlspecialchars($user->nombre) . "</p>";
            echo "<pre>";
            echo "ID: {$user->id_usuario}\n";
            echo "Nombre: {$user->nombre}\n";
            echo "Email: {$user->email}\n";
            echo "Rol: {$user->rol}\n";
            echo "Activo: " . ($user->activo ? 'Sí' : 'No') . "\n";
            echo "Bloqueado: " . ($user->cuenta_bloqueada ? 'SÍ' : 'No') . "\n";
            echo "Intentos fallidos: {$user->intentos_fallidos}\n";
            echo "Hash almacenado: " . substr($user->password_hash, 0, 20) . "...\n";
            echo "Longitud hash: " . strlen($user->password_hash) . " caracteres\n";
            echo "</pre>";
            
            // Verificar contraseña
            echo "<h4>Verificación de contraseña:</h4>";
            
            if (password_verify($password, $user->password_hash)) {
                echo "<p class='ok'>✓ ¡CONTRASEÑA CORRECTA! El login debería funcionar.</p>";
                
                if ((int)$user->activo === 0) {
                    echo "<p class='warning'>⚠ PERO el usuario está INACTIVO</p>";
                }
                if ((int)$user->cuenta_bloqueada === 1) {
                    echo "<p class='warning'>⚠ PERO la cuenta está BLOQUEADA</p>";
                }
            } else {
                echo "<p class='error'>✗ CONTRASEÑA INCORRECTA</p>";
                echo "<p class='info'>El hash almacenado no coincide con la contraseña ingresada.</p>";
                
                // Mostrar cómo debería verse el hash correcto
                $hashCorrecto = password_hash($password, PASSWORD_DEFAULT);
                echo "<p class='info'>Hash que generaría esta contraseña: <code>" . substr($hashCorrecto, 0, 30) . "...</code></p>";
            }
        }
    }
    ?>
    
    <form method="POST">
        <p><strong>Probar credenciales:</strong></p>
        <input type="email" name="test_email" placeholder="Email" required style="width: 250px;">
        <input type="password" name="test_password" placeholder="Contraseña" required style="width: 200px;">
        <button type="submit">Probar Login</button>
    </form>
    </div>

    <!-- 4. GENERAR NUEVO HASH -->
    <h2>4. Generar Hash para Nueva Contraseña</h2>
    <div class="section">
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
        $newPass = $_POST['new_password'];
        $newHash = password_hash($newPass, PASSWORD_DEFAULT);
        
        echo "<p class='ok'>Hash generado para usar en la BD:</p>";
        echo "<pre style='word-break: break-all;'>" . htmlspecialchars($newHash) . "</pre>";
        echo "<p class='info'>Copia este hash y úsalo para actualizar la contraseña del usuario en la BD:</p>";
        echo "<pre>UPDATE usuarios SET password_hash = '" . htmlspecialchars($newHash) . "' WHERE email = 'email@ejemplo.com';</pre>";
    }
    ?>
    <form method="POST">
        <input type="text" name="new_password" placeholder="Nueva contraseña" required style="width: 250px;">
        <button type="submit">Generar Hash</button>
    </form>
    </div>

    <!-- 5. RESETEAR USUARIO -->
    <h2>5. Resetear Usuario (Desbloquear)</h2>
    <div class="section">
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_user_id'])) {
        $userId = (int)$_POST['reset_user_id'];
        
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET cuenta_bloqueada = 0, intentos_fallidos = 0 WHERE id_usuario = ?");
            $stmt->execute([$userId]);
            
            if ($stmt->rowCount() > 0) {
                echo "<p class='ok'>✓ Usuario ID $userId desbloqueado y reiniciados los intentos fallidos</p>";
            } else {
                echo "<p class='warning'>⚠ No se encontró usuario con ID $userId</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    ?>
    <form method="POST">
        <input type="number" name="reset_user_id" placeholder="ID Usuario" required style="width: 100px;">
        <button type="submit">Desbloquear</button>
    </form>
    </div>

    <hr style="margin: 40px 0; border-color: #333;">
    <p class="error" style="text-align: center;">
        ⚠️ ELIMINA ESTE ARCHIVO CUANDO TERMINES DE DIAGNOSTICAR
    </p>

</body>
</html>
