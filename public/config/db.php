<?php
// Auto-detectar entorno: LOCAL vs PRODUCCIÓN
$isProduction = ($_SERVER['HTTP_HOST'] ?? '') === 'mascotasymimos.com';

if ($isProduction) {
  // CONFIGURACIÓN SERVIDOR PRODUCCIÓN
  return [
    'host' => '45.143.162.54',
    'port' => 3306,
    'name' => 'sistemasia_inventpro',
    'user' => 'sistemasia_inventpro',
    'pass' => 'Santiago2980%%',
  ];
} else {
  // CONFIGURACIÓN LOCAL
  return [
    'host' => 'localhost',
    'port' => 3306,
    'name' => 'petcare_saas',
    'user' => 'root',
    'pass' => '',
  ];
}