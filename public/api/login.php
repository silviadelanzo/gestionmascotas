<?php
require __DIR__ . '/../includes/bootstrap.php';

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

  $_SESSION['uid'] = (int)$user['id'];
  $_SESSION['nombre'] = $user['nombre'] ?? '';
  $_SESSION['rol'] = $user['rol'] ?? 'dueno';
  $_SESSION['is_admin'] = ($_SESSION['rol'] === 'admin');

  // Redirigir directo al launchpad segun rol
  $role = $_SESSION['rol'];
  $launchUrl = $role === 'prestador'
    ? $baseUrl . '/launchpad_prestador.php'
    : $baseUrl . '/launchpad_dueno_v2.php';

  header('Location: ' . $launchUrl);
  exit;
} catch (Throwable $e) {
  header('Location: ' . $baseUrl . '/login.php?err=server');
  exit;
}
