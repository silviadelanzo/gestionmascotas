<?php
declare(strict_types=1);

$baseDir = realpath(__DIR__);
if ($baseDir === false) {
    fwrite(STDERR, "ERROR: No se pudo acceder a la ruta base." . PHP_EOL);
    exit(1);
}

$protectedFiles = [
    'config/mail.php',
    'config/env.php',
    'config/db.php',
    'public/test_mail_server.php',
    'public/index.php',
    'public/guardar_suscripcion.php',
    'public/assets/css/style.css',
    'public/assets/css/style_tailwind_overrides.css',
    'public/assets/js/app.js',
];

$protectedDirs = [
    'lib/PHPMailer',
    'public/assets/img',
    'public/assets/logo',
    'public/api',
    'public/dashboard',
    'public/includes',
    'public/mascotas',
    'public/uploads',
    'public/assets/css',
    'public/assets/js',
    'public/assets',
    'public',
    'app',
    'config',
    'sql',
];

$directoriesToDelete = ['.vscode', '.github', 'vendor', 'cgi-bin', 'node_modules', '.idea'];
$filesToDelete = [
    'ver_ruta.php',
    'gestionmascotas.code-workspace',
    'README_DEPLOY.md.bak',
    'README.old',
    'composer.phar',
    '.env',
    '.env.backup',
];

$filePatterns = ['*.bak', '*.tmp', '*.old', '*.log'];

$directoriesToEnsure = [
    'app',
    'app/Controllers',
    'app/Models',
    'app/Views',
    'app/Views/layouts',
    'config',
    'lib',
    'lib/PHPMailer',
    'public',
    'public/api',
    'public/dashboard',
    'public/assets',
    'public/assets/css',
    'public/assets/js',
    'public/assets/img',
    'public/assets/logo',
    'public/mascotas',
    'public/includes',
    'public/uploads',
    'sql',
];

$protectedFilesNorm = array_values(array_unique(array_map('normalizeRelativePath', $protectedFiles)));
$protectedDirsNorm = array_values(array_unique(array_map('normalizeRelativePath', $protectedDirs)));
$directoriesToEnsureNorm = array_values(array_unique(array_map('normalizeRelativePath', $directoriesToEnsure)));

logProtectedFiles($protectedFilesNorm, $baseDir);
logProtectedDirectories($protectedDirsNorm, $baseDir);

traverseAndClean(
    $baseDir,
    $directoriesToDelete,
    $filesToDelete,
    $filePatterns,
    $protectedDirsNorm,
    $protectedFilesNorm
);

ensureDirectoriesExist($directoriesToEnsureNorm, $baseDir);

function traverseAndClean(
    string $baseDir,
    array $dirsToDelete,
    array $filesToDelete,
    array $patterns,
    array $protectedDirs,
    array $protectedFiles
): void {
    $stack = [$baseDir];
    while ($stack) {
        $currentDir = array_pop($stack);
        $relativeDir = relativeFromBase($currentDir, $baseDir);
        if (!is_readable($currentDir)) {
            logAction('ERROR: No se pudo acceder a la ruta ' . formatRelative($relativeDir));
            continue;
        }

        $entries = @scandir($currentDir);
        if ($entries === false) {
            logAction('ERROR: No se pudo acceder a la ruta ' . formatRelative($relativeDir));
            continue;
        }

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = $currentDir . DIRECTORY_SEPARATOR . $entry;
            $relative = relativeFromBase($fullPath, $baseDir);
            $normalizedRelative = normalizeRelativePath($relative);

            if (is_dir($fullPath) && !is_link($fullPath)) {
                if (isProtectedScope($normalizedRelative, $protectedDirs)) {
                    continue;
                }

                if (in_array($entry, $dirsToDelete, true)) {
                    deleteDirectory($fullPath, $normalizedRelative, $protectedDirs, $protectedFiles);
                    continue;
                }

                $stack[] = $fullPath;
                continue;
            }

            if (
                isProtectedFileRelative($normalizedRelative, $protectedFiles) ||
                isProtectedScope($normalizedRelative, $protectedDirs)
            ) {
                continue;
            }

            if (in_array($entry, $filesToDelete, true) || matchesPatterns($entry, $patterns)) {
                deleteFile($fullPath, $normalizedRelative);
            }
        }
    }
}

function deleteDirectory(
    string $directory,
    string $relative,
    array $protectedDirs,
    array $protectedFiles
): void {
    if (isProtectedScope($relative, $protectedDirs)) {
        return;
    }

    if (!is_readable($directory)) {
        logAction('ERROR: No se pudo acceder a la ruta ' . formatRelative($relative));
        return;
    }

    $items = @scandir($directory);
    if ($items === false) {
        logAction('ERROR: No se pudo acceder a la ruta ' . formatRelative($relative));
        return;
    }

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $path = $directory . DIRECTORY_SEPARATOR . $item;
        $childRelative = normalizeRelativePath($relative === '' ? $item : $relative . '/' . $item);

        if (is_dir($path) && !is_link($path)) {
            deleteDirectory($path, $childRelative, $protectedDirs, $protectedFiles);
            continue;
        }

        if (
            isProtectedFileRelative($childRelative, $protectedFiles) ||
            isProtectedScope($childRelative, $protectedDirs)
        ) {
            continue;
        }

        deleteFile($path, $childRelative);
    }

    if (@rmdir($directory)) {
        logAction('Eliminada carpeta: ' . formatRelative($relative));
    } else {
        logAction('ERROR: No se pudo eliminar carpeta: ' . formatRelative($relative));
    }
}

function deleteFile(string $filePath, string $relative): void
{
    if (!@unlink($filePath)) {
        logAction('ERROR: No se pudo eliminar archivo: ' . formatRelative($relative));
        return;
    }

    logAction('Eliminado archivo: ' . formatRelative($relative));
}

function ensureDirectoriesExist(array $directories, string $baseDir): void
{
    foreach ($directories as $dir) {
        if ($dir === '') {
            continue;
        }

        $fullPath = buildFullPath($baseDir, $dir);
        if (is_dir($fullPath)) {
            continue;
        }

        if (@mkdir($fullPath, 0775, true)) {
            logAction('Creada carpeta: ' . $dir);
        } else {
            logAction('ERROR: No se pudo crear la carpeta: ' . $dir);
        }
    }
}

function logProtectedFiles(array $protectedFiles, string $baseDir): void
{
    foreach ($protectedFiles as $file) {
        if ($file === '') {
            continue;
        }

        $fullPath = buildFullPath($baseDir, $file);
        if (file_exists($fullPath)) {
            logAction('Conservado archivo protegido: ' . $file);
        } else {
            logAction('FALTA ARCHIVO: ' . $file);
        }
    }
}

function logProtectedDirectories(array $protectedDirs, string $baseDir): void
{
    foreach ($protectedDirs as $dir) {
        if ($dir === '') {
            continue;
        }

        $fullPath = buildFullPath($baseDir, $dir);
        if (is_dir($fullPath)) {
            logAction('Conservada carpeta protegida: ' . $dir);
        }
    }
}

function matchesPatterns(string $filename, array $patterns): bool
{
    foreach ($patterns as $pattern) {
        $regex = patternToRegex($pattern);
        if (preg_match($regex, $filename)) {
            return true;
        }
    }

    return false;
}

function patternToRegex(string $pattern): string
{
    $escaped = preg_quote($pattern, '/');
    $escaped = str_replace(['\*', '\?'], ['.*', '.'], $escaped);

    return '/^' . $escaped . '$/i';
}

function normalizeRelativePath(string $path): string
{
    $normalized = str_replace('\\', '/', $path);
    return trim($normalized, '/');
}

function buildFullPath(string $baseDir, string $relative): string
{
    if ($relative === '') {
        return $baseDir;
    }

    return $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
}

function relativeFromBase(string $target, string $baseDir): string
{
    $normalizedBase = rtrim(str_replace('\\', '/', $baseDir), '/');
    $normalizedTarget = str_replace('\\', '/', $target);

    if ($normalizedTarget === $normalizedBase) {
        return '';
    }

    $prefix = $normalizedBase . '/';
    if (strpos($normalizedTarget, $prefix) === 0) {
        $relative = substr($normalizedTarget, strlen($prefix));
        return normalizeRelativePath($relative);
    }

    return normalizeRelativePath($normalizedTarget);
}

function isProtectedFileRelative(string $relative, array $protectedFiles): bool
{
    return in_array($relative, $protectedFiles, true);
}

function isProtectedScope(string $relative, array $protectedDirs): bool
{
    if ($relative === '') {
        return false;
    }

    foreach ($protectedDirs as $dir) {
        if ($dir === '') {
            continue;
        }

        if ($relative === $dir || strpos($relative, $dir . '/') === 0) {
            return true;
        }
    }

    return false;
}

function formatRelative(string $relative): string
{
    return $relative === '' ? '.' : $relative;
}

function logAction(string $message): void
{
    echo $message . PHP_EOL;
}
