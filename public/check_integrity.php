<?php
// public/check_integrity.php
header('Content-Type: text/plain');
echo "=== CHEQUEO DE ARCHIVOS ===\n\n";

$files = [
    'includes/auth.php',
    'includes/bootstrap.php',
    'api/login.php',
    'index_v2_6.php'
];

foreach ($files as $f) {
    $fullPath = __DIR__ . '/../public/' . $f; // Ajuste asumiendo que el script está en public/
    
    // Tratamos de buscar relativo a este script
    // public/check_integrity.php -> busca public/includes/auth.php
    $path = __DIR__ . '/' . $f;
    
    echo "Archivo: $f\n";
    if (file_exists($path)) {
        echo "   Estado: ✅ EXISTE\n";
        echo "   Tamaño: " . filesize($path) . " bytes\n";
    } else {
        echo "   Estado: ❌ NO EXISTE\n";
    }
    echo "------------------\n";
}
