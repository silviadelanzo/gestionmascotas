<?php
/**
 * Script de diagnóstico completo para el problema de login
 * Usar: login.php?debug=1 para ver información detallada
 */
require __DIR__ . '/../includes/bootstrap.php';

// Activar debug si viene el parámetro
$debug = isset($_GET['debug']) && $_GET['debug'] === '1';

if ($debug) {
  header('Content-Type: text/plain; charset=utf-8');
  echo "=== DIAGNÓSTICO DE SESIÓN Y LOGIN ===\n\n";
  
  echo "1. INFORMACIÓN DEL SERVIDOR:\n";
  echo "   - PHP Version: " . phpversion() . "\n";
  echo "   - Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "\n";
  echo "   - HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "\n";
  echo "   - HTTPS: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'YES' : 'NO') . "\n\n";
  
  echo "2. CONFIGURACIÓN DE SESIÓN:\n";
  echo "   - session.save_path: " . ini_get('session.save_path') . "\n";
  echo "   - session.save_handler: " . ini_get('session.save_handler') . "\n";
  echo "   - session.cookie_lifetime: " . ini_get('session.cookie_lifetime') . "\n";
  echo "   - session.cookie_path: " . ini_get('session.cookie_path') . "\n";
  echo "   - session.cookie_domain: " . ini_get('session.cookie_domain') . "\n";
  echo "   - session.cookie_secure: " . ini_get('session.cookie_secure') . "\n";
  echo "   - session.cookie_httponly: " . ini_get('session.cookie_httponly') . "\n";
  echo "   - session.cookie_samesite: " . ini_get('session.cookie_samesite') . "\n";
  echo "   - session.use_cookies: " . ini_get('session.use_cookies') . "\n";
  echo "   - session.use_only_cookies: " . ini_get('session.use_only_cookies') . "\n\n";
  
  echo "3. ESTADO DE SESIÓN ACTUAL:\n";
  echo "   - Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'NONE') . "\n";
  echo "   - Session ID: " . session_id() . "\n";
  echo "   - Session Name: " . session_name() . "\n\n";
  
  echo "4. VARIABLES DE SESIÓN:\n";
  if (empty($_SESSION)) {
    echo "   ⚠️ SESIÓN VACÍA - No hay variables de sesión\n\n";
  } else {
    foreach ($_SESSION as $key => $value) {
      echo "   - $_SESSION['$key'] = " . var_export($value, true) . "\n";
    }
    echo "\n";
  }
  
  echo "5. COOKIES RECIBIDAS:\n";
  if (empty($_COOKIE)) {
    echo "   ⚠️ NO HAY COOKIES\n\n";
  } else {
    foreach ($_COOKIE as $name => $value) {
      if (strpos($name, 'PHPSESSID') !== false || strpos($name, 'session') !== false) {
        echo "   - $name = $value\n";
      }
    }
    echo "\n";
  }
  
  echo "6. PERMISOS DE DIRECTORIO DE SESIÓN:\n";
  $savePath = ini_get('session.save_path');
  if ($savePath && is_dir($savePath)) {
    echo "   - Directorio existe: SÍ\n";
    echo "   - Es escribible: " . (is_writable($savePath) ? 'SÍ' : '❌ NO') . "\n";
    echo "   - Permisos: " . substr(sprintf('%o', fileperms($savePath)), -4) . "\n";
  } else {
    echo "   ⚠️ Directorio no existe o no es accesible: $savePath\n";
  }
  echo "\n";
  
  echo "7. PRUEBA DE ESCRITURA DE SESIÓN:\n";
  $_SESSION['test_timestamp'] = time();
  $_SESSION['test_random'] = rand(1000, 9999);
  echo "   - Escribiendo variables de prueba...\n";
  echo "   - test_timestamp = " . $_SESSION['test_timestamp'] . "\n";
  echo "   - test_random = " . $_SESSION['test_random'] . "\n";
  echo "\n";
  
  echo "8. HEADERS QUE SE ENVIARÍAN:\n";
  if (!headers_sent()) {
    echo "   - Headers NO enviados todavía (OK)\n";
  } else {
    echo "   ⚠️ Headers YA enviados\n";
  }
  echo "\n";
  
  echo "=== FIN DEL DIAGNÓSTICO ===\n";
  echo "\nREFRESCA ESTA PÁGINA (F5) y verifica si las variables de prueba persisten.\n";
  echo "Si test_timestamp y test_random cambian en cada refresh, la sesión NO está funcionando.\n";
  
  exit;
}

// Código normal del login continúa aquí...
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ingresar · Mascotas y Mimos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    /* ... resto del CSS ... */
    body {
      margin: 0;
      font-family: 'Poppins', system-ui, -apple-system, sans-serif;
      background: #0f0c0c;
      color: #2B1D18;
      overflow-x: hidden;
    }
    .debug-link {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: rgba(255, 0, 0, 0.8);
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      text-decoration: none;
      font-size: 14px;
      z-index: 9999;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .debug-link:hover {
      background: rgba(255, 0, 0, 1);
    }
  </style>
</head>
<body>
  <!-- Botón de debug flotante -->
  <a href="?debug=1" class="debug-link">🔍 Ver Diagnóstico</a>
  
  <!-- Resto del contenido del login... -->
  <p style="color: white; text-align: center; padding: 50px;">
    Formulario de login normal aquí...<br>
    <strong>Haz click en "Ver Diagnóstico" abajo a la derecha para analizar el problema</strong>
  </p>
</body>
</html>
