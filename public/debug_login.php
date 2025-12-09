<?php
/**
 * Script de debug para verificar login - STANDALONE
 * Usar: debug_login.php?email=tu@email.com&pass=tupassword
 */

// Cargar configuración de BD manualmente
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

// Funciones de auth copiadas
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

function auth_verify_token(string $token): ?array {
  $parts = explode('.', $token);
  if (count($parts) !== 2) {
    return null;
  }
  
  [$encoded, $signature] = $parts;
  
  $expectedSignature = hash_hmac('sha256', $encoded, AUTH_SECRET_KEY);
  if (!hash_equals($expectedSignature, $signature)) {
    return null;
  }
  
  $payload = json_decode(base64_decode($encoded), true);
  if (!$payload) {
    return null;
  }
  
  if (isset($payload['exp']) && $payload['exp'] < time()) {
    return null;
  }
  
  return $payload;
}

// Script principal
header('Content-Type: text/plain; charset=utf-8');

$email = $_GET['email'] ?? '';
$pass = $_GET['pass'] ?? '';

if (empty($email) || empty($pass)) {
  echo "USO: debug_login.php?email=tu@email.com&pass=tupassword\n";
  exit;
}

echo "=== DEBUG DE LOGIN ===\n\n";
echo "Email a buscar: $email\n";
echo "Password a verificar: $pass\n\n";

try {
  $pdo = db();
  
  echo "1. BUSCANDO USUARIO EN BD:\n";
  $stmt = $pdo->prepare('SELECT id, nombre, email, password, rol FROM usuarios WHERE email = :email LIMIT 1');
  $stmt->execute(['email' => strtolower(trim($email))]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if (!$user) {
    echo "   ❌ USUARIO NO ENCONTRADO\n";
    echo "   El email '$email' no existe en la base de datos\n\n";
    
    echo "2. USUARIOS EXISTENTES:\n";
    $stmt = $pdo->query('SELECT id, nombre, email, rol FROM usuarios ORDER BY id LIMIT 10');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
      echo "   - ID: {$u['id']}, Email: {$u['email']}, Nombre: {$u['nombre']}, Rol: {$u['rol']}\n";
    }
    exit;
  }
  
  echo "   ✅ USUARIO ENCONTRADO\n";
  echo "   ID: {$user['id']}\n";
  echo "   Nombre: {$user['nombre']}\n";
  echo "   Email: {$user['email']}\n";
  echo "   Rol: {$user['rol']}\n";
  echo "   Password Hash: " . substr($user['password'], 0, 20) . "...\n\n";
  
  echo "2. VERIFICANDO PASSWORD:\n";
  $passwordMatch = password_verify($pass, $user['password']);
  
  if ($passwordMatch) {
    echo "   ✅ PASSWORD CORRECTO\n";
    echo "   El login debería funcionar\n\n";
    
    echo "3. CREANDO TOKEN:\n";
    $token = auth_create_token($user);
    echo "   Token creado: " . substr($token, 0, 50) . "...\n\n";
    
    echo "4. VERIFICANDO TOKEN:\n";
    $decoded = auth_verify_token($token);
    if ($decoded) {
      echo "   ✅ TOKEN VÁLIDO\n";
      echo "   UID: {$decoded['uid']}\n";
      echo "   Nombre: {$decoded['nombre']}\n";
      echo "   Rol: {$decoded['rol']}\n";
    } else {
      echo "   ❌ TOKEN INVÁLIDO\n";
    }
    
  } else {
    echo "   ❌ PASSWORD INCORRECTO\n";
    echo "   La contraseña '$pass' no coincide con el hash guardado\n\n";
    
    echo "SOLUCIÓN:\n";
    echo "Necesitás resetear la contraseña del usuario.\n";
    echo "Ejecutá este SQL en phpMyAdmin:\n\n";
    $newHash = password_hash($pass, PASSWORD_DEFAULT);
    echo "UPDATE usuarios SET password = '$newHash' WHERE email = '$email';\n";
  }
  
} catch (Exception $e) {
  echo "ERROR: " . $e->getMessage() . "\n";
}
