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
function helpers_require_admin(): void {
  if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403); echo "Prohibido"; exit;
  }
}
