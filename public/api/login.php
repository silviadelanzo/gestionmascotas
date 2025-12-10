<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Metodo no permitido');
}

$baseUrl = app_base_url();

$email = strtolower(trim($_POST['email'] ?? ''));
$pass  = $_POST['password'] ?? '';

if ($email === '' || $pass === '') {
  header('Location: ' . $baseUrl . '/login.php?err=datos');
  exit;
}

try {
  $pdo = db();
  $stmt = $pdo->prepare('SELECT id, nombre, email, password, rol FROM usuarios WHERE email = :email LIMIT 1');
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user || !password_verify($pass, (string)$user['password'])) {
    header('Location: ' . $baseUrl . '/login.php?err=credenciales');
    exit;
  }

  // Token y sesion
  $token = auth_create_token($user);
  auth_set_cookie($token);

  $_SESSION['uid'] = (int)$user['id'];
  $_SESSION['nombre'] = $user['nombre'] ?? '';
  $_SESSION['rol'] = $user['rol'] ?? 'dueno';
  $_SESSION['is_admin'] = ($_SESSION['rol'] === 'admin');

  $redirect = $baseUrl . '/index_v2_6.php';
  header('Location: ' . $redirect);
  echo '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') . '"></head><body><script>window.location.replace("' . addslashes($redirect) . '");</script></body></html>';
  exit;
} catch (Throwable $e) {
  header('Location: ' . $baseUrl . '/login.php?err=server');
  exit;
}
