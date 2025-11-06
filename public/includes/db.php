<?php
require_once __DIR__.'/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (Throwable $e) {
        // Degradar sin romper la página; mensaje opcional según flag
        // Evitar cambiar headers si ya hubo salida (p. ej., header.php ya imprimió HTML)
        if (!headers_sent()) {
            http_response_code(200);
        }
        if (defined('SHOW_DB_WARNING') && SHOW_DB_WARNING) {
            echo '<div style="background:#fff3cd;color:#664d03;padding:8px;border:1px solid #ffecb5;">Aviso: No se pudo conectar a la base de datos. Algunas funciones pueden no estar disponibles.</div>';
        }
        throw $e;
    }
    return $pdo;
}
