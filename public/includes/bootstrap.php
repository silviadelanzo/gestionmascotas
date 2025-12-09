<?php
require __DIR__ . '/../config/env.php';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/auth.php';  // Sistema de autenticación con tokens

// Mantener sesiones PHP solo para compatibilidad con código existente
// pero el login usará el nuevo sistema de tokens
if (session_status() === PHP_SESSION_NONE) {
  $isProduction = ($_SERVER['HTTP_HOST'] ?? '') === 'mascotasymimos.com';
  
  session_set_cookie_params([
    'lifetime' => 0,
    'path' => $isProduction ? '/gestionmascotas/public' : '/',
    'domain' => $isProduction ? 'mascotasymimos.com' : '',
    'secure' => $isProduction,
    'httponly' => true,
    'samesite' => 'Lax'
  ]);
  
  session_start();
}

// Sincronizar token con sesión PHP para compatibilidad
$user = auth_get_user();
if ($user) {
  $_SESSION['uid'] = $user['uid'];
  $_SESSION['nombre'] = $user['nombre'];
  $_SESSION['rol'] = $user['rol'];
  $_SESSION['is_admin'] = ($user['rol'] === 'admin');
}