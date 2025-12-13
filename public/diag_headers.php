<?php
header('Content-Type: text/plain; charset=utf-8');

function file_bom_status(string $path): string {
  if (!is_file($path)) {
    return 'missing';
  }
  $fh = fopen($path, 'rb');
  if (!$fh) {
    return 'unreadable';
  }
  $bytes = fread($fh, 3);
  fclose($fh);
  $hex = strtoupper(bin2hex($bytes ?: ''));
  if ($bytes === "\xEF\xBB\xBF") {
    return "BOM(UTF-8) first3=EFBBBF";
  }
  return "ok first3={$hex}";
}

echo "diag_headers.php\n";
echo "PHP: " . PHP_VERSION . "\n";
echo "URL: " . ($_SERVER['REQUEST_URI'] ?? '') . "\n\n";

$hsFile = null;
$hsLine = null;
echo "headers_sent(before bootstrap): " . (headers_sent($hsFile, $hsLine) ? "YES {$hsFile}:{$hsLine}" : "NO") . "\n";

echo "\nBOM check:\n";
$base = __DIR__;
$checks = [
  $base . '/includes/bootstrap.php',
  $base . '/includes/helpers.php',
  $base . '/includes/auth.php',
  $base . '/api/login.php',
  $base . '/login.php',
  $base . '/registro.php',
  $base . '/index_v2_6.php',
];
foreach ($checks as $p) {
  echo basename($p) . ': ' . file_bom_status($p) . "\n";
}

echo "\nIncluding bootstrap...\n";
require __DIR__ . '/includes/bootstrap.php';

$hsFile2 = null;
$hsLine2 = null;
echo "headers_sent(after bootstrap): " . (headers_sent($hsFile2, $hsLine2) ? "YES {$hsFile2}:{$hsLine2}" : "NO") . "\n";
echo "ob_get_level: " . ob_get_level() . "\n";
echo "session_status: " . session_status() . " (0=disabled,1=none,2=active)\n";
echo "session_id: " . session_id() . "\n";
echo "cookies: " . (empty($_COOKIE) ? '(none)' : 'present') . "\n";
echo "app_base_url: " . (function_exists('app_base_url') ? app_base_url() : '(missing)') . "\n";

