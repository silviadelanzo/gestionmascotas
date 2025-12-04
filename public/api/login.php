<?php
require __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Metodo no permitido');
}

$envCfg = require __DIR__ . '/../config/env.php';
$baseUrl = rtrim((string)($envCfg['base_url'] ?? ''), '/');
if ($baseUrl === '') {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/public/api/login.php', PHP_URL_PATH);
  $dir = trim(dirname($uriPath), '/');
  $dir = $dir !== '' ? '/' . $dir : '';
  $baseUrl = $scheme . '://' . $host . $dir;
  $baseUrl = preg_replace('~/api$~', '', $baseUrl);
}

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

  $role = $_SESSION['rol'];
  if ($role === 'prestador') {
    header('Location: ' . $baseUrl . '/launchpad_prestador.php');
  } else {
    header('Location: ' . $baseUrl . '/launchpad_dueno.php');
  }
  exit;
} catch (Throwable $e) {
  header('Location: ' . $baseUrl . '/login.php?err=server');
  exit;
}
