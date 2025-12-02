<?php
require __DIR__ . '/includes/bootstrap.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/Exception.php';

$envCfg = require __DIR__ . '/config/env.php';
$baseUrl = rtrim((string)($envCfg['base_url'] ?? ''), '/');
$baseUrl = $baseUrl !== '' ? $baseUrl : 'http://localhost/public';

$email = trim($_POST['email'] ?? '');
$errors = [];
$successMessage = '';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($email === '') {
    $errors[] = 'Ingresa tu email.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email inválido.';
  } else {
    try {
      $pdo = db();
      $stmt = $pdo->prepare('SELECT id, nombre FROM usuarios WHERE email = :email LIMIT 1');
      $stmt->execute(['email' => $email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      // Siempre respondemos con éxito para no filtrar existencia de emails
      $successMessage = 'Si el email está registrado, te enviamos un enlace para restablecer tu contraseña.';

      if ($user) {
        $tokenPlain = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $tokenPlain);

        // Upsert sobre password_reset_tokens (email PK)
        $stmtUpsert = $pdo->prepare(
          'REPLACE INTO password_reset_tokens (email, token, created_at) VALUES (:email, :token, NOW())'
        );
        $stmtUpsert->execute([
          'email' => $email,
          'token' => $tokenHash,
        ]);

        try {
          $mailCfg = require __DIR__ . '/../config/mail.php';
          $mailer = createMailerFromConfig($mailCfg);
          $mailer->addAddress($email, $user['nombre'] ?? '');
          $mailer->isHTML(true);
          $mailer->Subject = 'Restablecer contraseña - Mascotas y Mimos';
          $resetUrl = $baseUrl . '/reset_password.php?token=' . urlencode($tokenPlain) . '&email=' . urlencode($email);
          $mailer->Body = '<p>Hola ' . htmlspecialchars($user['nombre'] ?? '', ENT_QUOTES, 'UTF-8') . ',</p>'
            . '<p>Recibimos una solicitud para restablecer tu contraseña. Usa el siguiente enlace:</p>'
            . '<p><a href="' . $resetUrl . '">' . $resetUrl . '</a></p>'
            . '<p>El enlace vence en 1 hora. Si no fuiste tú, ignora este correo.</p>';
          $mailer->AltBody = "Hola {$user['nombre']},\n\n"
            . "Restablece tu contraseña aquí (vence en 1 hora): {$resetUrl}\n\n"
            . "Si no fuiste tú, ignora este correo.";
          $mailer->send();
        } catch (MailException $mailErr) {
          // Silencioso para el usuario; se mantiene mensaje genérico
        }
      }
    } catch (PDOException $e) {
      $errors[] = 'No pudimos procesar la solicitud. Intenta más tarde.';
    }
  }
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<style>
  .auth-wrapper {
    max-width: 640px;
    margin: 0 auto;
    padding: 2rem 1rem 3rem;
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }
  .auth-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 1.6rem;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    border: 1px solid #e5e7eb;
  }
  .auth-card h1 { margin: 0 0 0.75rem; font-size: 1.5rem; color: #111827; }
  .auth-card p { margin: 0 0 1.25rem; color: #4b5563; }
  .alert { border-radius: 12px; padding: 0.9rem 1rem; margin-bottom: 1rem; }
  .alert-error { background: #fef2f2; color: #b91c1c; }
  .alert-success { background: #ecfdf5; color: #047857; }
  form { display: flex; flex-direction: column; gap: 0.75rem; }
  label { display: flex; flex-direction: column; gap: 0.35rem; color: #374151; font-weight: 500; }
  input[type="email"] { padding: 0.8rem; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 1rem; }
  button { padding: 0.9rem 1rem; border: none; border-radius: 12px; background: linear-gradient(135deg,#7c3aed,#6366f1); color: #fff; font-weight: 600; cursor: pointer; }
  button:hover { opacity: 0.95; }
</style>
<main class="auth-wrapper">
  <section class="auth-card">
    <h1>Restablecer contraseña</h1>
    <p>Ingresa el email con el que te registraste. Te enviaremos un enlace para elegir una nueva contraseña.</p>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
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
  <?php endif; ?>

  <form method="post" novalidate>
    <label>
      Email
      <input type="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required>
    </label>
    <button type="submit">Enviar enlace</button>
  </form>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
