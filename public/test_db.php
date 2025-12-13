<?php
// Test de conexión a la base de datos.
// Cargar bootstrap (usa config/db.php del servidor).
require __DIR__ . '/includes/bootstrap.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo = db();
    $stmt = $pdo->query('SELECT NOW() AS ahora, DATABASE() AS db');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "OK DB conectada" . PHP_EOL;
    echo "NOW() = " . ($row['ahora'] ?? 'sin valor') . PHP_EOL;
    echo "DATABASE() = " . ($row['db'] ?? 'sin valor') . PHP_EOL;
} catch (Throwable $e) {
    echo "ERROR DB: " . $e->getMessage() . PHP_EOL;
}
