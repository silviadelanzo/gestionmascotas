<?php
/**
 * Script de diagnóstico de sesiones - STANDALONE
 * No depende de bootstrap ni otros archivos
 * Usar: test_session.php?debug=1
 */

// Iniciar sesión manualmente
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$debug = isset($_GET['debug']) && $_GET['debug'] === '1';

if ($debug) {
  header('Content-Type: text/plain; charset=utf-8');
  echo "=== DIAGNÓSTICO DE SESIÓN ===\n\n";
  
  echo "1. INFORMACIÓN DEL SERVIDOR:\n";
  echo "   PHP Version: " . phpversion() . "\n";
  echo "   HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "\n";
  echo "   HTTPS: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'YES' : 'NO') . "\n\n";
  
  echo "2. CONFIGURACIÓN DE SESIÓN:\n";
  echo "   session.save_path: " . ini_get('session.save_path') . "\n";
  echo "   session.cookie_path: " . ini_get('session.cookie_path') . "\n";
  echo "   session.cookie_domain: " . ini_get('session.cookie_domain') . "\n";
  echo "   session.cookie_secure: " . ini_get('session.cookie_secure') . "\n";
  echo "   session.cookie_httponly: " . ini_get('session.cookie_httponly') . "\n";
  echo "   session.cookie_samesite: " . ini_get('session.cookie_samesite') . "\n\n";
  
  echo "3. ESTADO DE SESIÓN:\n";
  echo "   Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'NONE') . "\n";
  echo "   Session ID: " . session_id() . "\n";
  echo "   Session Name: " . session_name() . "\n\n";
  
  echo "4. VARIABLES DE SESIÓN:\n";
  if (empty($_SESSION)) {
    echo "   SESIÓN VACÍA - No hay variables\n\n";
  } else {
    foreach ($_SESSION as $key => $value) {
      echo "   SESSION[" . $key . "] = " . var_export($value, true) . "\n";
    }
    echo "\n";
  }
  
  echo "5. COOKIES RECIBIDAS:\n";
  if (empty($_COOKIE)) {
    echo "   NO HAY COOKIES\n\n";
  } else {
    $found = false;
    foreach ($_COOKIE as $name => $value) {
      if (strpos($name, 'PHPSESS') !== false || strpos($name, 'session') !== false) {
        echo "   " . $name . " = " . $value . "\n";
        $found = true;
      }
    }
    if (!$found) {
      echo "   No hay cookies de sesión\n";
    }
    echo "\n";
  }
  
  echo "6. PERMISOS DE DIRECTORIO:\n";
  $savePath = ini_get('session.save_path');
  if ($savePath && is_dir($savePath)) {
    echo "   Directorio: " . $savePath . "\n";
    echo "   Existe: SI\n";
    echo "   Es escribible: " . (is_writable($savePath) ? 'SI' : 'NO') . "\n";
    echo "   Permisos: " . substr(sprintf('%o', fileperms($savePath)), -4) . "\n\n";
  } else {
    echo "   Directorio no accesible: " . $savePath . "\n\n";
  }
  
  echo "7. PRUEBA DE PERSISTENCIA:\n";
  $_SESSION['test_time'] = time();
  $_SESSION['test_num'] = rand(1000, 9999);
  echo "   test_time = " . $_SESSION['test_time'] . "\n";
  echo "   test_num = " . $_SESSION['test_num'] . "\n\n";
  
  echo "=== FIN DEL DIAGNÓSTICO ===\n\n";
  echo "INSTRUCCIONES:\n";
  echo "1. REFRESCA esta página (F5) varias veces\n";
  echo "2. Fijate si test_time y test_num CAMBIAN o SE MANTIENEN\n\n";
  echo "Si CAMBIAN = Sesión NO funciona (PROBLEMA)\n";
  echo "Si SE MANTIENEN = Sesión SI funciona (OK)\n";
  
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Test de Sesión</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 0;
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .container {
      text-align: center;
      background: rgba(255, 255, 255, 0.1);
      padding: 50px;
      border-radius: 20px;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    h1 {
      margin: 0 0 20px 0;
      font-size: 2.5em;
    }
    p {
      font-size: 1.2em;
      margin: 20px 0;
    }
    a {
      display: inline-block;
      background: #e74c3c;
      color: white;
      padding: 15px 40px;
      text-decoration: none;
      border-radius: 50px;
      font-size: 1.2em;
      font-weight: bold;
      transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
    }
    a:hover {
      background: #c0392b;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(231, 76, 60, 0.6);
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>🔍 Diagnóstico de Sesiones</h1>
    <p>Script para analizar el problema de login</p>
    <p>Haz click abajo para ver el diagnóstico completo</p>
    <a href="?debug=1">Ver Diagnóstico</a>
  </div>
</body>
</html>
