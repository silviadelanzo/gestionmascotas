<?php
// Script temporal para verificar usuarios en la base de datos
require __DIR__ . '/includes/bootstrap.php';

header('Content-Type: text/plain; charset=utf-8');

try {
  $pdo = db();
  
  echo "=== USUARIOS EN LA BASE DE DATOS ===\n\n";
  
  $stmt = $pdo->query('SELECT id, nombre, email, rol, created_at FROM usuarios ORDER BY id');
  $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  if (empty($users)) {
    echo "No hay usuarios en la base de datos.\n";
  } else {
    foreach ($users as $user) {
      echo "ID: {$user['id']}\n";
      echo "Nombre: {$user['nombre']}\n";
      echo "Email: {$user['email']}\n";
      echo "Rol: {$user['rol']}\n";
      echo "Creado: {$user['created_at']}\n";
      echo str_repeat('-', 50) . "\n";
    }
    echo "\nTotal: " . count($users) . " usuarios\n";
  }
  
} catch (Exception $e) {
  echo "ERROR: " . $e->getMessage() . "\n";
}
