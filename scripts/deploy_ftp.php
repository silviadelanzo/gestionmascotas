<?php
// scripts/deploy_ftp.php
// Subida por FTP/FTPS de archivos puntuales (sin tocar el resto)
// Uso (CLI):
//   php scripts/deploy_ftp.php --ini=scripts/deploy.local.ini --test-file=public/test_ftp.txt
//   php scripts/deploy_ftp.php --ini=scripts/deploy.local.ini --upload-list

declare(strict_types=1);

function parseArgs(array $argv): array {
    $out = [
        'ini' => 'scripts/deploy.local.ini',
        'test-file' => null,
        'upload-list' => false,
        'dry-run' => false,
        'backup' => true,
    ];
    foreach ($argv as $a) {
        if (str_starts_with($a, '--ini=')) $out['ini'] = substr($a, 6);
        elseif (str_starts_with($a, '--test-file=')) $out['test-file'] = substr($a, 12);
        elseif ($a === '--upload-list') $out['upload-list'] = true;
        elseif ($a === '--no-backup') $out['backup'] = false;
        elseif ($a === '--dry-run') $out['dry-run'] = true;
    }
    return $out;
}

function fail(string $msg): void { fwrite(STDERR, "ERROR: $msg`n"); exit(1); }

function ensureRemoteDir($ftp, string $remotePath): void {
    $parts = array_filter(explode('/', trim($remotePath, '/')));
    $path = '';
    foreach ($parts as $p) {
        $path .= '/' . $p;
        if (!@ftp_chdir($ftp, $path)) {
            @ftp_chdir($ftp, '/');
            @ftp_mkdir($ftp, $path);
        }
    }
}

function remoteExists($ftp, string $remoteFile): bool {
    $dir = dirname($remoteFile);
    $base = basename($remoteFile);
    $list = @ftp_nlist($ftp, $dir);
    if ($list === false) return false;
    foreach ($list as $item) if (basename($item) === $base) return true;
    return false;
}

function uploadFile($ftp, string $localFile, string $remoteFile, bool $backup, bool $dry): bool {
    if (!is_file($localFile)) { echo "[SKIP] No existe local: $localFile`n"; return false; }
    $remoteDir = str_replace('\\', '/', dirname($remoteFile));
    ensureRemoteDir($ftp, $remoteDir);

    if ($backup && remoteExists($ftp, $remoteFile)) {
        $bak = $remoteFile . '.bak.' . date('Ymd_His');
        if ($dry) {
            echo "[DRY] backup $remoteFile -> $bak`n";
        } else {
            @ftp_rename($ftp, $remoteFile, $bak);
            echo "[OK ] backup: $bak`n";
        }
    }

    if ($dry) {
        echo "[DRY] put $localFile -> $remoteFile`n";
        return true;
    }
    $ok = @ftp_put($ftp, $remoteFile, $localFile, FTP_BINARY);
    echo ($ok ? '[OK ]' : '[ERR]') . " put $localFile -> $remoteFile`n";
    return $ok;
}

// --- MAIN ---
if (php_sapi_name() !== 'cli') fail('Ejecutar por CLI. Ej: php scripts/deploy_ftp.php --ini=scripts/deploy.local.ini --test-file=public/test_ftp.txt');

$args = parseArgs($argv);
if (!is_file($args['ini'])) fail('No se encuentra el archivo INI: ' . $args['ini']);

$ini = parse_ini_file($args['ini'], true, INI_SCANNER_TYPED);
if (!$ini || empty($ini['ftp'])) fail('INI invalido o seccion [ftp] ausente');

$ftpCfg = $ini['ftp'];
$host = (string)($ftpCfg['host'] ?? '');
$user = (string)($ftpCfg['user'] ?? '');
$pass = (string)($ftpCfg['pass'] ?? '');
$port = (int)($ftpCfg['port'] ?? 21);
$ssl  = (int)($ftpCfg['ssl']  ?? 0) === 1;
$pasv = (int)($ftpCfg['passive'] ?? 1) === 1;
$to   = (int)($ftpCfg['timeout'] ?? 30);
$root = rtrim((string)($ftpCfg['remote_root'] ?? '/'), '/');

if ($host === '' || $user === '' || $pass === '') fail('Completar host/user/pass en el INI');

echo "Conectando a $host:$port (" . ($ssl ? 'FTPS' : 'FTP') . ")...`n";
$ftp = $ssl ? @ftp_ssl_connect($host, $port, $to) : @ftp_connect($host, $port, $to);
if (!$ftp) fail('No se pudo conectar');
if (!@ftp_login($ftp, $user, $pass)) fail('Login invalido');
@ftp_pasv($ftp, $pasv);

$uploads = [];
if ($args['test-file']) {
    $uploads[] = $args['test-file'];
}
if ($args['upload-list']) {
    $list = $ini['files']['list'] ?? [];
    if (is_string($list)) $list = [$list];
    foreach ($list as $f) $uploads[] = $f;
}
if (!$uploads) fail('No hay archivos para subir. Usa --test-file o --upload-list');

$okAll = true;
foreach ($uploads as $local) {
    $localPath = $local;
    if (!is_file($localPath)) {
        // permitir rutas relativas desde raiz del repo
        $localPath = __DIR__ . '/../' . ltrim($local, '/\\');
    }
    $remote = $root . '/' . ltrim(str_replace('\\\\', '/', $local), '/');
    $ok = uploadFile($ftp, $localPath, $remote, $args['backup'], $args['dry-run']);
    $okAll = $okAll && $ok;
}

ftp_close($ftp);
exit($okAll ? 0 : 2);