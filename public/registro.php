<?php
require __DIR__ . '/includes/bootstrap.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/Exception.php';

$baseUrl = app_base_url();
$loginUrl = (parse_url($baseUrl, PHP_URL_SCHEME) !== null) ? ($baseUrl . '/login.php') : 'login.php';

$errors = [];
$successMessage = '';
$shouldRedirect = false;

function createMailerFromConfig(array $mailCfg): PHPMailer {
  $mailer = new PHPMailer(true);
  $mailer->isSMTP();
  $mailer->Host = $mailCfg['host'];
  $mailer->SMTPAuth = true;
  $mailer->Username = $mailCfg['username'];
  $mailer->Password = $mailCfg['password'];

  $enc = strtolower((string)($mailCfg['encryption'] ?? 'tls'));
  if ($enc === 'ssl' || ((int)($mailCfg['port'] ?? 0)) === 465) {
    $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mailer->Port = (int)($mailCfg['port'] ?? 465);
  } else {
    $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mailer->Port = (int)($mailCfg['port'] ?? 587);
  }

  $mailer->CharSet = 'UTF-8';
  $mailer->setFrom($mailCfg['from_email'], $mailCfg['from_name']);
  return $mailer;
}

// Preseleccionar rol desde GET (?role=dueno|prestador) o POST
$defaultRole = $_GET['role'] ?? ($_POST['tipo_usuario'] ?? 'dueno');
$tipoUsuario = in_array($defaultRole, ['dueno', 'prestador'], true) ? $defaultRole : 'dueno';
$useVideoBg = ($tipoUsuario === 'dueno');

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($nombre === '') {
    $errors[] = 'El nombre es obligatorio.';
  }

  if ($email === '') {
    $errors[] = 'El email es obligatorio.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Ingresa un email valido.';
  }

  if ($password === '') {
    $errors[] = 'La contrasena es obligatoria.';
  } elseif (strlen($password) < 6) {
    $errors[] = 'La contrasena debe tener al menos 6 caracteres.';
  }

  if ($passwordConfirm === '') {
    $errors[] = 'Confirma la contrasena.';
  } elseif ($password !== '' && $password !== $passwordConfirm) {
    $errors[] = 'Las contrasenas no coinciden.';
  }

  $allowedTypes = ['dueno', 'prestador'];
  if ($tipoUsuario === '' || !in_array($tipoUsuario, $allowedTypes, true)) {
    $errors[] = 'Selecciona un tipo de usuario valido.';
  }

  if (empty($errors)) {
    try {
      $pdo = db();

      $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
      $stmt->execute(['email' => $email]);
      if ($stmt->fetch()) {
        $errors[] = 'Ya existe una cuenta con ese email.';
      } else {
        try {
          $subscriptionEmail = strtolower($email);
          $subscriptionTipo = $tipoUsuario === 'prestador' ? 'prestador' : 'usuario';
          $checkSubscription = $pdo->prepare('SELECT id FROM suscripciones WHERE email = :email LIMIT 1');
          $checkSubscription->execute(['email' => $subscriptionEmail]);
          if (!$checkSubscription->fetch()) {
            $insertSubscription = $pdo->prepare(
              'INSERT INTO suscripciones (nombre, email, tipo, autorizacion, fecha_alta)
               VALUES (:nombre, :email, :tipo, 1, NOW())'
            );
            $insertSubscription->execute([
              'nombre' => $nombre !== '' ? $nombre : null,
              'email' => $subscriptionEmail,
              'tipo' => $subscriptionTipo,
            ]);
          }
        } catch (PDOException $subscriptionError) {
          // No interrumpir el registro si hay un fallo en la tabla de suscripciones.
        }
      }
    } catch (PDOException $e) {
      $errors[] = 'Ocurrio un error al validar el email. Intenta mas tarde.';
    }
  }

  if (empty($errors)) {
    try {
      if (!isset($pdo)) {
        $pdo = db();
      }
      $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
      $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nombre, email, password, rol, email_verified_at, estado, created_at, updated_at)
         VALUES (:nombre, :email, :password, :rol, NULL, :estado, NOW(), NOW())'
      );

      $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'password' => $hashedPassword,
        'rol' => $tipoUsuario,
        'estado' => 'pendiente',
      ]);

      $newUserId = (int)$pdo->lastInsertId();

      $token = bin2hex(random_bytes(32));
      $exp = (new DateTimeImmutable('+2 days'))->format('Y-m-d H:i:s');
      $stmtToken = $pdo->prepare(
        'INSERT INTO email_verifications_app (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)'
      );
      $stmtToken->execute([
        'user_id' => $newUserId,
        'token' => $token,
        'expires_at' => $exp,
      ]);

      try {
        $mailCfg = require __DIR__ . '/../config/mail.php';
        $mailer = createMailerFromConfig($mailCfg);
        $mailer->addAddress($email, $nombre);
        $mailer->isHTML(true);
        $mailer->Subject = 'Verificacion de cuenta - Mascotas y Mimos';
        $verifyUrl = $baseUrl . '/verificar.php?token=' . urlencode($token);
        $mailer->Body = '<p>Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',</p>'
          . '<p>Gracias por registrarte. Haz clic en el siguiente enlace para activar tu cuenta:</p>'
          . '<p><a href="' . $verifyUrl . '">' . $verifyUrl . '</a></p>'
          . '<p>Si no te registraste, ignora este correo.</p>';
        $mailer->AltBody = "Hola {$nombre},\n\n"
          . "Gracias por registrarte. Activa tu cuenta aqui: {$verifyUrl}\n\n"
          . "Si no te registraste, ignora este correo.";
        $mailer->send();
        $successMessage = 'Te enviamos un correo para verificar tu cuenta. Revisa tu bandeja.';
      } catch (MailException $mailError) {
        $successMessage = 'Tu cuenta fue creada. No pudimos enviar el correo de verificacion, pero puedes volver a solicitarlo mas tarde.';
      }

      $shouldRedirect = true;
      $nombre = '';
      $email = '';
      $password = '';
      $tipoUsuario = 'dueno';
    } catch (PDOException $e) {
      $errors[] = 'No pudimos crear tu cuenta. Intenta nuevamente.';
    }
  }
}

if ($successMessage && $shouldRedirect) {
  header('Refresh: 3; URL=' . $loginUrl);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Crear cuenta - Mascotas y Mimos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    <?php if ($useVideoBg): ?>
    .video-bg {
      position: fixed;
      inset: 0;
      z-index: -2;
      overflow: hidden;
    }
    .video-bg video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      filter: brightness(0.75);
    }
    .overlay {
      position: fixed;
      inset: 0;
      background: linear-gradient(135deg, rgba(15, 12, 12, 0.45), rgba(15, 12, 12, 0.35));
      z-index: -1;
    }
    <?php endif; ?>
    :root {
      --brand: #A97155;
      --brand-dark: #8d5f47;
      --text: #3b2c26;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      min-height: 100vh;
      font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', sans-serif;
      color: var(--text);
      background: radial-gradient(circle at 18% 22%, rgba(255,214,165,0.6), transparent 35%),
                  radial-gradient(circle at 78% 12%, rgba(250,224,195,0.6), transparent 30%),
                  linear-gradient(135deg, #fff4ec, #f9e4d5);
      position: relative;
      overflow: hidden;
    }
    <?php if (!$useVideoBg): ?>
    body::before {
      content: '';
      position: absolute;
      inset: 0;
      background: url('/assets/img/hero.webp') center/cover no-repeat;
      filter: blur(20px) brightness(0.9);
      opacity: 0.3;
      z-index: 0;
    }
    <?php endif; ?>
    main {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 2rem 1rem;
    }
    .auth-card {
      width: 100%;
      max-width: 460px;
      background: rgba(255,255,255,0.94);
      backdrop-filter: blur(8px);
      border-radius: 30px;
      padding: 2.5rem 2.2rem;
      box-shadow: 0 25px 70px rgba(80, 50, 35, 0.18);
    }
    .auth-card h1 {
      margin: 0 0 0.4rem;
      font-size: 1.9rem;
      text-align: center;
    }
    .auth-card p {
      margin: 0 0 1.6rem;
      text-align: center;
      color: #6b4e43;
    }
    .alert {
      border-radius: 18px;
      padding: 0.9rem 1.1rem;
      font-size: 0.95rem;
      margin-bottom: 1.2rem;
    }
    .alert ul { margin: 0.35rem 0 0; padding-left: 1.2rem; }
    .alert-error { background: #fef2f2; color: #b91c1c; }
    .alert-success { background: #ecfdf5; color: #047857; }
    .form-field {
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
      margin-bottom: 1rem;
      font-size: 0.95rem;
    }
    .form-field label { font-weight: 600; }
    .form-control {
      width: 100%;
      padding: 0.85rem 1rem;
      border: 1px solid #e6d8cf;
      border-radius: 16px;
      background: #fffaf7;
      font-size: 1rem;
      transition: border 0.2s ease, box-shadow 0.2s ease;
    }
    .form-control:focus {
      outline: none;
      border-color: var(--brand);
      box-shadow: 0 0 0 3px rgba(169, 113, 85, 0.18);
      background: #ffffff;
    }
    .password-group { position: relative; }
    .password-toggle {
      position: absolute;
      top: 50%;
      right: 0.65rem;
      transform: translateY(-50%);
      border: none;
      background: transparent;
      color: #6b4e43;
      font-size: 1rem;
      cursor: pointer;
    }
    .cta-button {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: 20px;
      font-size: 1.05rem;
      font-weight: 700;
      background: linear-gradient(135deg, var(--brand), var(--brand-dark));
      color: #ffffff;
      cursor: pointer;
      transition: transform 0.15s ease, box-shadow 0.2s ease;
      margin-top: 0.3rem;
    }
    .cta-button:hover {
      transform: translateY(-1px);
      box-shadow: 0 15px 28px rgba(169, 113, 85, 0.25);
    }
    .login-link {
      margin-top: 1.3rem;
      text-align: center;
      font-size: 0.95rem;
      color: #6b4e43;
    }
    .login-link a {
      color: var(--brand);
      font-weight: 700;
      text-decoration: none;
    }
    .login-link a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <?php if ($useVideoBg): ?>
    <div class="video-bg">
      <video src="assets/videos/dueno_ingresando.mp4" autoplay loop muted playsinline poster="assets/img/hero.webp"></video>
    </div>
    <div class="overlay"></div>
  <?php endif; ?>
  <main>
    <section class="auth-card" aria-labelledby="register-title">
      <h1 id="register-title">Crear cuenta</h1>
      <p>Guardalo todo en una sola cuenta. Gratis para duenos y prestadores.</p>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error" role="alert">
          <strong>Revisa la informacion:</strong>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php elseif ($successMessage): ?>
        <div class="alert alert-success" role="status">
          <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <script>
          setTimeout(function () {
            window.location.href = <?= json_encode($loginUrl, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
          }, 1800);
        </script>
      <?php endif; ?>

      <form method="post" novalidate>
        <div class="form-field">
          <label for="nombre">Nombre completo</label>
          <input
            type="text"
            id="nombre"
            name="nombre"
            class="form-control"
            value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
            required
            autocomplete="name"
          >
        </div>

        <div class="form-field">
          <label for="email">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-control"
            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
            required
            autocomplete="email"
          >
        </div>

        <div class="form-field">
          <label for="password">Contrasena</label>
          <div class="password-group">
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              required
              autocomplete="new-password"
              minlength="6"
            >
            <button type="button" class="password-toggle" data-target="password" aria-label="Mostrar contrasena">👁</button>
          </div>
        </div>

        <div class="form-field">
          <label for="password_confirm">Confirmar contrasena</label>
          <div class="password-group">
            <input
              type="password"
              id="password_confirm"
              name="password_confirm"
              class="form-control"
              required
              autocomplete="new-password"
            >
            <button type="button" class="password-toggle" data-target="password_confirm" aria-label="Mostrar contrasena confirmada">👁</button>
          </div>
        </div>

        <div class="form-field">
          <label for="tipo_usuario">Tipo de usuario</label>
          <select id="tipo_usuario" name="tipo_usuario" class="form-control" required>
            <option value="dueno" <?= $tipoUsuario === 'dueno' ? 'selected' : '' ?>>Dueno</option>
            <option value="prestador" <?= $tipoUsuario === 'prestador' ? 'selected' : '' ?>>Prestador</option>
          </select>
        </div>

        <button type="submit" class="cta-button">Crear cuenta</button>
      </form>

      <div class="login-link">
        ¿Ya tienes cuenta? <a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Inicia sesion</a>
      </div>
    </section>
  </main>
  <script>
    (function () {
      document.querySelectorAll('.password-toggle').forEach((btn) => {
        btn.addEventListener('click', () => {
          const targetId = btn.getAttribute('data-target');
          const field = document.getElementById(targetId);
          if (!field) return;
          const isPassword = field.type === 'password';
          field.type = isPassword ? 'text' : 'password';
          btn.setAttribute('aria-label', isPassword ? 'Ocultar contrasena' : 'Mostrar contrasena');
        });
      });
    })();
  </script>
</body>
</html>
