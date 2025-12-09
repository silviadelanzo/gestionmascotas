<?php
require __DIR__ . '/../config/env.php';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/helpers.php';

// Configurar sesiones de manera robusta para producción
if (session_status() === PHP_SESSION_NONE) {
  // Configuración de cookies de sesión
  $isProduction = ($_SERVER['HTTP_HOST'] ?? '') === 'mascotasymimos.com';
  
  session_set_cookie_params([
    'lifetime' => 0,  // Hasta que se cierre el navegador
    'path' => $isProduction ? '/gestionmascotas/public' : '/gestionmascotas/public',
    'domain' => $isProduction ? 'mascotasymimos.com' : '',
    'secure' => $isProduction,  // Solo HTTPS en producción
    'httponly' => true,  // No accesible desde JavaScript
    'samesite' => 'Lax'  // Protección CSRF
  ]);
  
  session_start();
}