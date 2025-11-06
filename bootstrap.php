<?php

// Ruta base del proyecto
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

// Cargar variables desde .env si existe
(function () {
    $envPath = BASE_PATH . DIRECTORY_SEPARATOR . '.env';
    if (!file_exists($envPath)) {
        return;
    }
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        [$key, $value] = array_map('trim', explode('=', $line, 2) + [null, null]);
        if ($key !== null) {
            // Quitar comillas si las hay
            $value = trim($value, "\"' ");
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
})();

// Helper env
if (!function_exists('env')) {
    function env(string $key, $default = null) {
        return $_ENV[$key] ?? getenv($key) ?? $default;
    }
}

// Autoloader simple para App\\ namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\\\\\';
    if (str_starts_with($class, $prefix)) {
        $relative = substr($class, strlen($prefix));
        $path = BASE_PATH . '/app/' . str_replace('\\\
', '/', $relative) . '.php';
        if (file_exists($path)) require $path;
    }
});

// Función de render básico con layout
if (!function_exists('render')) {
    function render(string $template, array $params = []) {
        extract($params, EXTR_SKIP);
        ob_start();
        $viewFile = BASE_PATH . '/app/Views/' . $template . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo "Vista no encontrada: {$template}";
            return;
        }
        require $viewFile;
        $content = ob_get_clean();
        require BASE_PATH . '/app/Views/layout.php';
    }
}

// Cargar config app (después de env)
$config = require BASE_PATH . '/config/app.php';
