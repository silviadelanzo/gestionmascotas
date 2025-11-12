<?php
// Image optimization script for Mascotas y Mimos
// - Resizes PNGs to max width 1280px (keeps aspect ratio)
// - Overwrites original files with optimized PNG (compression 9)
// - Optionally writes .webp if supported

declare(strict_types=1);

function optimize_png(string $path, int $maxWidth = 1280): bool {
    if (!file_exists($path)) {
        echo "Skip (not found): {$path}\n";
        return false;
    }
    $info = @getimagesize($path);
    if (!$info) {
        echo "Skip (invalid image): {$path}\n";
        return false;
    }
    [$w, $h, $type] = $info;
    // Load image depending on type; default to PNG loader
    switch ($type) {
        case IMAGETYPE_PNG:
            $src = @imagecreatefrompng($path);
            break;
        case IMAGETYPE_JPEG:
            $src = @imagecreatefromjpeg($path);
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp')) {
                $src = @imagecreatefromwebp($path);
                break;
            }
            // fallthrough to unsupported
        default:
            echo "Skip (unsupported type): {$path}\n";
            return false;
    }
    if (!$src) {
        echo "Skip (cannot load): {$path}\n";
        return false;
    }
    $newW = $w;
    $newH = $h;
    if ($w > $maxWidth) {
        $ratio = $h / $w;
        $newW = $maxWidth;
        $newH = (int) round($maxWidth * $ratio);
    }
    if ($newW !== $w || $newH !== $h) {
        $dst = imagecreatetruecolor($newW, $newH);
        // Preserve alpha for PNG
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
        imagedestroy($src);
        $src = $dst;
    }
    // Save optimized PNG
    $ok = imagepng($src, $path, 9);
    if ($ok) {
        echo "Optimized PNG: {$path} ({$newW}x{$newH})\n";
    }
    // Optionally write WebP alongside
    if (function_exists('imagewebp')) {
        $webpPath = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $path);
        if ($webpPath && $webpPath !== $path) {
            if (@imagewebp($src, $webpPath, 80)) {
                echo "Generated WebP: {$webpPath}\n";
            }
        }
    }
    imagedestroy($src);
    return $ok;
}

$base = realpath(__DIR__ . '/../public/assets');
if (!$base) { die("Assets path not found\n"); }
$imgDir = $base . DIRECTORY_SEPARATOR . 'img';
$logoDir = $base . DIRECTORY_SEPARATOR . 'logo';
if (!is_dir($logoDir)) {
    @mkdir($logoDir, 0775, true);
}

$images = [
    'hero.png',
    'beneficio1.png',
    'beneficio2.png',
    'footer.png',
    'adopcion.png',
    'belleza.png',
    'relajacion.png',
    // comunidad may be misnamed; handle both
    'comunidad.png',
    'veterinario.png',
    'paseador.png',
    'logo.png',
];

foreach ($images as $file) {
    $path = $imgDir . DIRECTORY_SEPARATOR . $file;
    if (!file_exists($path) && $file === 'comunidad.png') {
        // Try comunidad.png.png fallback
        $alt = $imgDir . DIRECTORY_SEPARATOR . 'comunidad.png.png';
        if (file_exists($alt)) { $path = $alt; }
    }
    optimize_png($path);
}

// Ensure site logo at assets/logo/logo.png (copy from img if needed) and optimize
$siteLogo = $logoDir . DIRECTORY_SEPARATOR . 'logo.png';
$imgLogo = $imgDir . DIRECTORY_SEPARATOR . 'logo.png';
if (!file_exists($siteLogo) && file_exists($imgLogo)) {
    @copy($imgLogo, $siteLogo);
}
optimize_png($siteLogo);

echo "Done.\n";

