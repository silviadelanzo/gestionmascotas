<?php
require __DIR__ . '/../includes/bootstrap.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

require_once __DIR__ . '/../../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../../lib/PHPMailer/SMTP.php';
require_once __DIR__ . '/../../lib/PHPMailer/Exception.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Metodo no permitido');
}

$baseUrl = app_base_url();

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

$email = strtolower(trim($_POST['email'] ?? ''));
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: ' . $baseUrl . '/olvide_password.php?err=invalid');
  exit;
}

try {
  $pdo = db();

  // Crear tabla si no existe.
  $pdo->exec(
    'CREATE TABLE IF NOT EXISTS password_resets_app (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      user_id BIGINT NOT NULL,
      token VARCHAR(128) NOT NULL,
      expires_at DATETIME NOT NULL,
      used_at DATETIME NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      UNIQUE KEY uq_password_resets_token (token),
      INDEX idx_password_resets_user (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
  );

  $stmt = $pdo->prepare('SELECT id, email FROM usuarios WHERE email = :email LIMIT 1');
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $userId = (int)$user['id'];

    // Opcional: invalidar tokens previos del usuario.
    $pdo->prepare('DELETE FROM password_resets_app WHERE user_id = :uid')->execute(['uid' => $userId]);

    $token = bin2hex(random_bytes(32));
    $expires = (new DateTimeImmutable('+2 hours'))->format('Y-m-d H:i:s');

    $insert = $pdo->prepare(
      'INSERT INTO password_resets_app (user_id, token, expires_at) VALUES (:uid, :token, :expires)'
    );
    $insert->execute([
      'uid' => $userId,
      'token' => $token,
      'expires' => $expires,
    ]);

    try {
      $mailCfg = require __DIR__ . '/../../config/mail.php';
      $mailer = createMailerFromConfig($mailCfg);
      $mailer->addAddress($email);
      $mailer->isHTML(true);
      $mailer->Subject = 'Restablecer contrasena - Mascotas y Mimos';
      $resetUrl = rtrim($baseUrl, '/') . '/reset_password.php?token=' . urlencode($token);
      $mailer->Body = '<p>Recibimos una solicitud para restablecer tu contrasena.</p>'
        . '<p>Si fuiste tu, usa este enlace (vence en 2 horas):</p>'
        . '<p><a href="' . $resetUrl . '">' . $resetUrl . '</a></p>'
        . '<p>Si no solicitaste este cambio, ignora este mensaje.</p>';
      $mailer->AltBody = "Recibimos una solicitud para restablecer tu contrasena.\n\n"
        . "Enlace (vence en 2 horas): {$resetUrl}\n\n"
        . "Si no solicitaste este cambio, ignora este mensaje.";
      $mailer->send();
    } catch (MailException $mailError) {
      // No romper el flujo aunque falle el mail; se sigue mostrando mensaje generico.
    }
  }
} catch (Throwable $e) {
  // Silenciar detalles y devolver mensaje generico.
}

header('Location: ' . $baseUrl . '/olvide_password.php?sent=1');
exit;
