<?php
require __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Metodo no permitido');
}

$isProduction = ($_SERVER['HTTP_HOST'] ?? '') === 'mascotasymimos.com';
$baseUrl = $isProduction 
  ? 'https://mascotasymimos.com/gestionmascotas/public'
  : 'http://localhost/gestionmascotas/public';

$email = strtolower(trim($_POST['email'] ?? ''));
$pass  = $_POST['password'] ?? '';

if ($email === '' || $pass === '') {
  header('Location: ' . $baseUrl . '/login.php?err=datos');
  exit;
}

try {
  $pdo = db();
  $stmt = $pdo->prepare('SELECT id, nombre, email, password, rol FROM usuarios WHERE email = :email LIMIT 1');
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user || !password_verify($pass, (string)$user['password'])) {
    header('Location: ' . $baseUrl . '/login.php?err=credenciales');
    exit;
  }

  // Crear token de autenticación
  $token = auth_create_token($user);
  auth_set_cookie($token);
  
  // También setear en sesión PHP para compatibilidad
  $_SESSION['uid'] = (int)$user['id'];
  $_SESSION['nombre'] = $user['nombre'] ?? '';
  $_SESSION['rol'] = $user['rol'] ?? 'dueno';
  $_SESSION['is_admin'] = ($_SESSION['rol'] === 'admin');

  // ⭐ FIX DEFINITIVO: Pantalla intermedia explícita con JS Redirect
  $redirectUrl = $baseUrl . '/index_v2_6.php';
  ?>
  <!DOCTYPE html>
  <html lang="es">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciando sesión...</title>
    <style>
      body {
        font-family: sans-serif;
        background-color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
      }
      .box {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
      }
      .btn {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background: #e74c3c;
        color: white;
        text-decoration: none;
        border-radius: 5px;
      }
    </style>
    <script>
      // Redirigir después de 500ms para asegurar que la cookie se guarde
      setTimeout(function() {
        window.location.href = "<?= htmlspecialchars($redirectUrl, ENT_QUOTES) ?>";
      }, 500);
    </script>
  </head>
  <body>
    <div class="box">
      <h2>¡Login Correcto!</h2>
      <p>Redirigiendo a la plataforma...</p>
      <p>Si no te redirige automáticamente, click aquí:</p>
      <a href="<?= htmlspecialchars($redirectUrl, ENT_QUOTES) ?>" class="btn">Continuar</a>
    </div>
  </body>
  </html>
  <?php
  exit;
  
} catch (Throwable $e) {
  header('Location: ' . $baseUrl . '/login.php?err=server');
  exit;
}
