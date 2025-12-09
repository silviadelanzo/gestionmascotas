<?php
/**
 * Script para probar login completo y verificar cookies
 * Simula el proceso de login y muestra qué cookies se setean
 */

// Cargar configuración
$config = require __DIR__ . '/config/db.php';

function db() {
  global $config;
  static $pdo = null;
  if ($pdo === null) {
    $pdo = new PDO(
      "mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4",
      $config['user'],
      $config['pass'],
      [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
  }
  return $pdo;
}

// Funciones de auth
define('AUTH_SECRET_KEY', 'mascotas_y_mimos_secret_2025_change_in_production');

function auth_create_token(array $userData): string {
  $payload = [
    'uid' => $userData['id'],
    'nombre' => $userData['nombre'],
    'email' => $userData['email'],
    'rol' => $userData['rol'],
    'exp' => time() + 3600,
    'iat' => time()
  ];
  
  $encoded = base64_encode(json_encode($payload));
  $signature = hash_hmac('sha256', $encoded, AUTH_SECRET_KEY);
  
  return $encoded . '.' . $signature;
}

// Script principal
$email = $_GET['email'] ?? '';
$pass = $_GET['pass'] ?? '';

if (empty($email) || empty($pass)) {
  echo "USO: test_login_cookie.php?email=tu@email.com&pass=tupassword\n";
  exit;
}

try {
  $pdo = db();
  
  $stmt = $pdo->prepare('SELECT id, nombre, email, password, rol FROM usuarios WHERE email = :email LIMIT 1');
  $stmt->execute(['email' => strtolower(trim($email))]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user || !password_verify($pass, $user['password'])) {
    echo "ERROR: Credenciales incorrectas\n";
    exit;
  }

  // Crear token
  $token = auth_create_token($user);
  
  // Detectar entorno
  $isProduction = ($_SERVER['HTTP_HOST'] ?? '') === 'mascotasymimos.com';
  
  // Configurar cookie
  $cookieParams = [
    'expires' => time() + 3600,
    'path' => $isProduction ? '/gestionmascotas/public' : '/',
    'domain' => $isProduction ? 'mascotasymimos.com' : '',
    'secure' => $isProduction,
    'httponly' => true,
    'samesite' => 'Lax'
  ];
  
  // Intentar setear cookie
  $cookieSet = setcookie('auth_token', $token, $cookieParams);
  
  // Mostrar resultado
  header('Content-Type: text/html; charset=utf-8');
  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="utf-8">
    <title>Test Login Cookie</title>
    <style>
      body { font-family: monospace; padding: 20px; background: #f0f0f0; }
      .success { color: green; }
      .error { color: red; }
      .info { color: blue; }
      pre { background: white; padding: 15px; border-radius: 5px; }
    </style>
  </head>
  <body>
    <h1>🔍 Test de Login y Cookies</h1>
    
    <h2>1. Usuario autenticado:</h2>
    <pre class="success">
✅ ID: <?= $user['id'] ?>
✅ Nombre: <?= $user['nombre'] ?>
✅ Email: <?= $user['email'] ?>
✅ Rol: <?= $user['rol'] ?>
    </pre>
    
    <h2>2. Token creado:</h2>
    <pre class="info">
Token: <?= substr($token, 0, 100) ?>...
Longitud: <?= strlen($token) ?> caracteres
    </pre>
    
    <h2>3. Configuración de cookie:</h2>
    <pre class="info">
Nombre: auth_token
Expires: <?= date('Y-m-d H:i:s', $cookieParams['expires']) ?>
Path: <?= $cookieParams['path'] ?>
Domain: <?= $cookieParams['domain'] ?: '(vacío - dominio actual)' ?>
Secure: <?= $cookieParams['secure'] ? 'Sí (solo HTTPS)' : 'No' ?>
HttpOnly: <?= $cookieParams['httponly'] ? 'Sí' : 'No' ?>
SameSite: <?= $cookieParams['samesite'] ?>
    </pre>
    
    <h2>4. Cookie seteada:</h2>
    <pre class="<?= $cookieSet ? 'success' : 'error' ?>">
<?= $cookieSet ? '✅ Cookie seteada correctamente' : '❌ ERROR al setear cookie' ?>
    </pre>
    
    <h2>5. Cookies recibidas en esta página:</h2>
    <pre>
<?php
if (empty($_COOKIE)) {
  echo "⚠️ NO HAY COOKIES\n";
} else {
  foreach ($_COOKIE as $name => $value) {
    echo "$name = " . substr($value, 0, 50) . "...\n";
  }
}
?>
    </pre>
    
    <h2>6. Headers enviados:</h2>
    <pre>
<?php
$headers = headers_list();
foreach ($headers as $header) {
  if (stripos($header, 'Set-Cookie') !== false) {
    echo "✅ $header\n";
  }
}
?>
    </pre>
    
    <hr>
    <p><strong>IMPORTANTE:</strong> Refrescá esta página (F5) y verificá si la cookie "auth_token" aparece en la sección 5.</p>
    <p>Si NO aparece después de refrescar, significa que el navegador está bloqueando la cookie.</p>
    
    <p><a href="?email=<?= urlencode($email) ?>&pass=<?= urlencode($pass) ?>">🔄 Refrescar test</a></p>
  </body>
  </html>
  <?php
  
} catch (Exception $e) {
  echo "ERROR: " . $e->getMessage();
}
