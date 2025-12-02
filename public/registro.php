<?php
require __DIR__ . '/includes/bootstrap.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/Exception.php';

$envCfg = require __DIR__ . '/config/env.php';
$baseUrl = rtrim((string)($envCfg['base_url'] ?? ''), '/');
if ($baseUrl === '') {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/public/registro.php', PHP_URL_PATH);
  $dir = trim(dirname($uriPath), '/');
  $dir = $dir !== '' ? '/' . $dir : '';
  $baseUrl = $scheme . '://' . $host . $dir;
}
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

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';
$tipoUsuario = $_POST['tipo_usuario'] ?? 'dueno';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($nombre === '') {
    $errors[] = 'El nombre es obligatorio.';
  }

  if ($email === '') {
    $errors[] = 'El email es obligatorio.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Ingresa un email válido.';
  }

  if ($password === '') {
    $errors[] = 'La contraseña es obligatoria.';
  } elseif (strlen($password) < 6) {
    $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
  }

  if ($passwordConfirm === '') {
    $errors[] = 'Confirma la contraseña.';
  } elseif ($password !== '' && $password !== $passwordConfirm) {
    $errors[] = 'Las contraseñas no coinciden.';
  }

  $allowedTypes = ['dueno', 'prestador'];
  if ($tipoUsuario === '' || !in_array($tipoUsuario, $allowedTypes, true)) {
    $errors[] = 'Selecciona un tipo de usuario válido.';
  }

  if (empty($errors)) {
    try {
      $pdo = db();

      // Verificar que no exista el usuario.
      $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
      $stmt->execute(['email' => $email]);
      if ($stmt->fetch()) {
        $errors[] = 'Ya existe una cuenta con ese email.';
      } else {
        // Si no está en suscripciones, lo agregamos.
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
      $errors[] = 'Ocurrió un error al validar el email. Intenta más tarde.';
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

      // Generar token de verificación y guardar
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

      // Enviar correo de verificación
      try {
        $mailCfg = require __DIR__ . '/../config/mail.php';
        $mailer = createMailerFromConfig($mailCfg);
        $mailer->addAddress($email, $nombre);
        $mailer->isHTML(true);
        $mailer->Subject = 'Verificación de cuenta - Mascotas y Mimos';
        $verifyUrl = $baseUrl . '/verificar.php?token=' . urlencode($token);
        $mailer->Body = '<p>Hola ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . ',</p>'
          . '<p>Gracias por registrarte. Haz clic en el siguiente enlace para activar tu cuenta:</p>'
          . '<p><a href="' . $verifyUrl . '">' . $verifyUrl . '</a></p>'
          . '<p>Si no te registraste, ignora este correo.</p>';
        $mailer->AltBody = "Hola {$nombre},\n\n"
          . "Gracias por registrarte. Activa tu cuenta aquí: {$verifyUrl}\n\n"
          . "Si no te registraste, ignora este correo.";
        $mailer->send();
        $successMessage = 'Te enviamos un correo para verificar tu cuenta. Revisa tu bandeja.';
      } catch (MailException $mailError) {
        $successMessage = 'Tu cuenta fue creada. No pudimos enviar el correo de verificación, pero puedes volver a solicitarlo más tarde.';
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

require __DIR__ . '/includes/header.php';
?>
<style>
  body {
    background: #f5f6fa;
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }
  .auth-layout {
    min-height: calc(100vh - 120px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem 3rem;
  }
  .auth-card {
    width: 100%;
    max-width: 420px;
    background: #ffffff;
    border-radius: 28px;
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
    padding: 2.5rem 2rem;
  }
  .auth-card h1 {
    margin-bottom: 0.5rem;
    font-size: 1.9rem;
    text-align: center;
    color: #1f2937;
  }
  .auth-card p {
    margin: 0 0 1.8rem;
    text-align: center;
    color: #6b7280;
  }
  .alert {
    border-radius: 18px;
    padding: 0.9rem 1.1rem;
    font-size: 0.95rem;
    margin-bottom: 1.2rem;
  }
  .alert ul {
    margin: 0.35rem 0 0;
    padding-left: 1.2rem;
  }
  .alert-error {
    background: #fef2f2;
    color: #b91c1c;
  }
  .alert-success {
    background: #ecfdf5;
    color: #047857;
  }
  .auth-form .field {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    margin-bottom: 1rem;
    font-size: 0.95rem;
    color: #475467;
  }
  .auth-form input,
  .auth-form select {
    width: 100%;
    padding: 0.85rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    font-size: 1rem;
    background: #f8fafc;
    transition: border 0.2s ease, box-shadow 0.2s ease;
  }
  .password-group {
    position: relative;
  }
  .password-group .password-toggle {
    position: absolute;
    top: 50%;
    right: 0.5rem;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    font-size: 1.1rem;
    cursor: pointer;
    color: #6b7280;
    padding: 0;
  }
  .auth-form input:focus,
  .auth-form select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    background: #ffffff;
  }
  .cta-button {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 20px;
    font-size: 1.05rem;
    font-weight: 600;
    background: linear-gradient(135deg, #7c3aed, #6366f1);
    color: #ffffff;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.2s ease;
    margin-top: 0.5rem;
  }
  .cta-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 15px 20px rgba(99, 102, 241, 0.25);
  }
  .login-link {
    margin-top: 1.4rem;
    text-align: center;
    font-size: 0.95rem;
    color: #6b7280;
  }
  .login-link a {
    color: #4f46e5;
    font-weight: 600;
    text-decoration: none;
  }
  .login-link a:hover {
    text-decoration: underline;
  }
</style>
<main class="auth-layout">
  <section class="auth-card">
    <h1>Mascotas y Mimos</h1>
    <p>Crea tu cuenta para comenzar.</p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <strong>Revisa la información:</strong>
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php elseif ($successMessage): ?>
      <div class="alert alert-success">
        <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
      </div>
      <script>
        setTimeout(function () {
          window.location.href = '/public/login.php';
        }, 1800);
      </script>
    <?php endif; ?>

    <form method="post" class="auth-form" novalidate>
      <label class="field" for="nombre">
        <span>Nombre</span>
        <input
          type="text"
          id="nombre"
          name="nombre"
          value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
          required
        >
      </label>

      <label class="field" for="email">
        <span>Email</span>
        <input
          type="email"
          id="email"
          name="email"
          value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
          required
        >
      </label>

      <label class="field" for="password">
        <span>Contraseña</span>
        <div class="password-group">
          <input
            type="password"
            id="password"
            name="password"
            required
          >
          <button type="button" class="password-toggle" data-target="password" aria-label="Mostrar contraseña">
            <span aria-hidden="true">👁</span>
          </button>
        </div>
      </label>

      <label class="field" for="password_confirm">
        <span>Confirmar contraseña</span>
        <div class="password-group">
          <input
            type="password"
            id="password_confirm"
            name="password_confirm"
            required
          >
          <button type="button" class="password-toggle" data-target="password_confirm" aria-label="Mostrar contraseña confirmada">
            <span aria-hidden="true">👁</span>
          </button>
        </div>
      </label>

      <label class="field" for="tipo_usuario">
        <span>Tipo de usuario</span>
        <select id="tipo_usuario" name="tipo_usuario" required>
          <option value="dueno" <?= $tipoUsuario === 'dueno' ? 'selected' : '' ?>>Dueño</option>
          <option value="prestador" <?= $tipoUsuario === 'prestador' ? 'selected' : '' ?>>Prestador</option>
        </select>
      </label>

      <button type="submit" class="cta-button">Registrar</button>
    </form>

    <div class="login-link">
      ¿Ya tienes cuenta? <a href="/public/login.php">Inicia sesión</a>
    </div>
  </section>
  </main>
  <script>
    (function () {
      const buttons = document.querySelectorAll('.password-toggle');
      buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
          const targetId = btn.getAttribute('data-target');
          const field = document.getElementById(targetId);
          if (!field) return;
          const isPassword = field.type === 'password';
          field.type = isPassword ? 'text' : 'password';
          btn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
      });
    })();
  </script>
<?php require __DIR__ . '/includes/footer.php'; ?>
$envCfg = require __DIR__ . '/config/env.php';
$baseUrl = rtrim((string)($envCfg['base_url'] ?? ''), '/');
$baseUrl = $baseUrl !== '' ? $baseUrl : 'http://localhost/public';
