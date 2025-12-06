<?php
function db(): PDO {
  static $pdo = null;
  if (!$pdo) {
    $cfg = require __DIR__ . '/../config/db.php';
    $host = $cfg['host'] ?? 'localhost';
    $name = $cfg['name'] ?? '';
    $port = $cfg['port'] ?? null;
    $charset = 'utf8mb4';
    $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";
    if (!empty($port)) {
      $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
    }
    $pdo = new PDO($dsn, (string)($cfg['user'] ?? ''), (string)($cfg['pass'] ?? ''), [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  }
  return $pdo;
}

/**
 * Devuelve la base URL de la app (carpeta public) respetando el host actual.
 * Si en config/env.php hay un base_url que apunta a localhost pero el host real es otro,
 * se recalcula a partir de la request para evitar enlaces rotos en producción.
 */
function app_base_url(): string {
  static $baseUrl = null;
  if ($baseUrl !== null) {
    return $baseUrl;
  }

  $envCfg = require __DIR__ . '/../config/env.php';
  $configured = rtrim((string)($envCfg['base_url'] ?? ''), '/');
  $requestHost = $_SERVER['HTTP_HOST'] ?? '';

  if ($configured !== '') {
    $cfgHost = parse_url($configured, PHP_URL_HOST);
    if ($cfgHost && $requestHost && strcasecmp($cfgHost, $requestHost) !== 0 && str_contains($cfgHost, 'localhost')) {
      // Evita usar base_url de localhost cuando el host real es otro (producción).
      $configured = '';
    }
  }

  if ($configured === '') {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $requestHost !== '' ? $requestHost : 'localhost';
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $dir = trim(dirname($uriPath), '/');
    $dir = $dir !== '' ? '/' . $dir : '';
    $configured = $scheme . '://' . $host . $dir;
  }

  // Si viene de /api, removemos el sufijo para apuntar a /public.
  $configured = preg_replace('~/api$~', '', $configured);

  return $baseUrl = rtrim($configured, '/');
}

function helpers_require_admin(): void {
  if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403); echo "Prohibido"; exit;
  }
}

/**
 * Devuelve la URL completa a la landing page actual.
 * Para cambiar de versión, solo modificar esta función.
 */
function home_url(): string {
  return app_base_url() . '/index_v2_5.php';  // CAMBIAR AQUÍ al actualizar versión
}
