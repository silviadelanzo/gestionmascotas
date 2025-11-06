<?php
// DB connectivity quick test (plain text). Remove this file after testing.
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__.'/includes/config.php';

echo "DB connectivity test\n";
echo "Host: ".(defined('DB_HOST')?DB_HOST:'(undefined)')."\n";
echo "Port: ".(defined('DB_PORT')?DB_PORT:'(undefined)')."\n";
echo "DB  : ".(defined('DB_NAME')?DB_NAME:'(undefined)')."\n";
echo "User: ".(defined('DB_USER')?DB_USER:'(undefined)')."\n";

try {
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $val = $pdo->query('SELECT 1')->fetchColumn();
    $db  = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "Connected: YES\n";
    echo "SELECT 1: ".$val."\n";
    echo "Database(): ".$db."\n";
} catch (Throwable $e) {
    echo "Connected: NO\n";
    echo "Error: ".$e->getMessage()."\n";
    if ($e->getCode()) {
        echo "Code: ".$e->getCode()."\n";
    }
}

echo "\nNote: delete db_test.php after verifying.\n";
