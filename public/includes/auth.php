<?php
/**
 * Sistema de autenticación con tokens
 * Reemplaza sesiones PHP por cookies firmadas
 */

// Clave secreta para firmar tokens (cambiar en producción)
define('AUTH_SECRET_KEY', 'mascotas_y_mimos_secret_2025_change_in_production');

/**
 * Crear token de autenticación
 */
function auth_create_token(array $userData): string {
  $payload = [
    'uid' => $userData['id'],
    'nombre' => $userData['nombre'],
    'email' => $userData['email'],
    'rol' => $userData['rol'],
    'exp' => time() + 3600, // Expira en 1 hora
    'iat' => time()
  ];
  
  $encoded = base64_encode(json_encode($payload));
  $signature = hash_hmac('sha256', $encoded, AUTH_SECRET_KEY);
  
  return $encoded . '.' . $signature;
}

/**
 * Verificar y decodificar token
 */
function auth_verify_token(string $token): ?array {
  $parts = explode('.', $token);
  if (count($parts) !== 2) {
    return null;
  }
  
  [$encoded, $signature] = $parts;
  
  // Verificar firma
  $expectedSignature = hash_hmac('sha256', $encoded, AUTH_SECRET_KEY);
  if (!hash_equals($expectedSignature, $signature)) {
    return null;
  }
  
  // Decodificar payload
  $payload = json_decode(base64_decode($encoded), true);
  if (!$payload) {
    return null;
  }
  
  // Verificar expiración
  if (isset($payload['exp']) && $payload['exp'] < time()) {
    return null;
  }
  
  return $payload;
}

/**
 * Guardar token en cookie
 */
function auth_set_cookie(string $token): void {
  $isProduction = ($_SERVER['HTTP_HOST'] ?? '') === 'mascotasymimos.com';
  
  setcookie('auth_token', $token, [
    'expires' => time() + 3600, // 1 hora
    'path' => $isProduction ? '/gestionmascotas/public' : '/',
    'domain' => $isProduction ? 'mascotasymimos.com' : '',
    'secure' => $isProduction,
    'httponly' => true,
    'samesite' => 'Lax'
  ]);
}

/**
 * Obtener usuario actual desde cookie
 */
function auth_get_user(): ?array {
  if (!isset($_COOKIE['auth_token'])) {
    return null;
  }
  
  return auth_verify_token($_COOKIE['auth_token']);
}

/**
 * Verificar si usuario está logueado
 */
function auth_is_logged(): bool {
  return auth_get_user() !== null;
}

/**
 * Cerrar sesión (borrar cookie)
 */
function auth_logout(): void {
  $isProduction = ($_SERVER['HTTP_HOST'] ?? '') === 'mascotasymimos.com';
  
  setcookie('auth_token', '', [
    'expires' => time() - 3600,
    'path' => $isProduction ? '/gestionmascotas/public' : '/',
    'domain' => $isProduction ? 'mascotasymimos.com' : '',
    'secure' => $isProduction,
    'httponly' => true,
    'samesite' => 'Lax'
  ]);
}

/**
 * Requerir autenticación (redirige si no está logueado)
 */
function auth_require(): array {
  $user = auth_get_user();
  if (!$user) {
    header('Location: ' . app_base_url() . '/login.php');
    exit;
  }
  return $user;
}