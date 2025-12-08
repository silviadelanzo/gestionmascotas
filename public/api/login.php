<?php
require __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Metodo no permitido');
}

// Detectar si estamos en producción o local
$isProduction = ($_SERVER['HTTP_HOST'] ?? '') === 'mascotasymimos.com';
$baseUrl = $isProduction 
  ? 'https://mascotasymimos.com/gestionmascotas/public'
  : 'http://localhost/gestionmascotas/public';

$email = strtolower(trim($_POST['email'] ?? ''));
$pass  = $_POST['password'] ?? '';

if ($email === '' || $pass === '') {
  redirectTo($baseUrl . '/login.php?err=datos');
}

try {
  $pdo = db();
  $stmt = $pdo->prepare('SELECT id, nombre, email, password, rol FROM usuarios WHERE email = :email LIMIT 1');
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user || !password_verify($pass, (string)$user['password'])) {
    redirectTo($baseUrl . '/login.php?err=credenciales');
  }

  $_SESSION['uid'] = (int)$user['id'];
  $_SESSION['nombre'] = $user['nombre'] ?? '';
  $_SESSION['rol'] = $user['rol'] ?? 'dueno';
  $_SESSION['is_admin'] = ($_SESSION['rol'] === 'admin');

  // Redirigir al index
  redirectTo($baseUrl . '/index_v2_6.php');
} catch (Throwable $e) {
  redirectTo($baseUrl . '/login.php?err=server');
}

/**
 * Función helper para redirigir usando JavaScript en lugar de header()
 * Esto evita problemas con configuraciones de servidor que bloquean header redirects
 */
function redirectTo(string $url): void {
  echo '<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="0;url=' . htmlspecialchars($url, ENT_QUOTES) . '">
  <script>window.location.href="' . htmlspecialchars($url, ENT_QUOTES) . '";</script>
</head>
<body>
  <p>Redirigiendo...</p>
</body>
</html>';
  exit;
}
