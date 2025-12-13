<?php
if (!headers_sent() && ob_get_level() === 0) {
  ob_start();
}

// (env/db se cargan on-demand desde helpers.php)
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';  // Sistema de autenticación con tokens

// Mantener sesiones PHP solo para compatibilidad con código existente,
// pero el login usa el sistema de tokens (ver auth.php).
if (session_status() === PHP_SESSION_NONE) {
  $isProduction = ($_SERVER['HTTP_HOST'] ?? '') === 'mascotasymimos.com'
    || ($_SERVER['HTTP_HOST'] ?? '') === 'www.mascotasymimos.com';

  session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $isProduction,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);

  session_start();
}

// Sincronizar token con sesión PHP para compatibilidad.
$user = auth_get_user();
if ($user) {
  $_SESSION['uid'] = $user['uid'];
  $_SESSION['nombre'] = $user['nombre'];
  $_SESSION['rol'] = $user['rol'];
  $_SESSION['is_admin'] = ($user['rol'] === 'admin');
}
