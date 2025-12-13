<?php
if (!function_exists('str_contains')) {
  function str_contains(string $haystack, string $needle): bool {
    if ($needle === '') {
      return true;
    }
    return strpos($haystack, $needle) !== false;
  }
}

function db(): PDO {
  static $pdo = null;
  if (!$pdo) {
    $cfgPath = __DIR__ . '/../config/db.php';
    if (!is_file($cfgPath)) {
      throw new PDOException('Falta configurar public/config/db.php en el servidor.');
    }
    $cfg = require $cfgPath;
    if (!is_array($cfg)) {
      throw new PDOException('Config inválida en public/config/db.php (debe devolver un array).');
    }
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

  $envCfgPath = __DIR__ . '/../config/env.php';
  $envCfg = is_file($envCfgPath) ? (require $envCfgPath) : [];
  if (!is_array($envCfg)) {
    $envCfg = [];
  }
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
 * Detecta automáticamente la versión más reciente de index_v2_*.php
 */
function home_url(): string {
  static $cachedUrl = null;
  
  if ($cachedUrl !== null) {
    return $cachedUrl;
  }
  
  $publicDir = __DIR__ . '/..';
  $versions = [];
  
  // Buscar todos los archivos index_v2_*.php
  foreach (glob($publicDir . '/index_v2_*.php') as $file) {
    if (preg_match('/index_v2_(\d+)\.php$/', basename($file), $matches)) {
      $versions[] = (int)$matches[1];
    }
  }
  
  if (empty($versions)) {
    // Fallback si no hay versiones
    return $cachedUrl = app_base_url() . '/index.php';
  }
  
  // Obtener la versión más alta
  $latestVersion = max($versions);
  
  return $cachedUrl = app_base_url() . '/index_v2_' . $latestVersion . '.php';
}
