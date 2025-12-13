<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/../lib/PHPMailer/SMTP.php';
require __DIR__ . '/../lib/PHPMailer/Exception.php';

header('Content-Type: text/plain; charset=utf-8');

$cfgPath = __DIR__ . '/../config/mail.php';
if (!is_file($cfgPath)) {
  exit("Falta configurar config/mail.php en el servidor.\n");
}

$cfg = require $cfgPath;
if (!is_array($cfg)) {
  exit("Config mail inválida (debe devolver un array).\n");
}

$to = isset($_GET['to']) && filter_var($_GET['to'], FILTER_VALIDATE_EMAIL)
  ? $_GET['to']
  : (string)($cfg['notify_email'] ?? '');

if ($to === '') {
  exit("Definí ?to=tu@email o 'notify_email' en config/mail.php.\n");
}

$mail = new PHPMailer(true);

try {
  $mail->isSMTP();
  $mail->Host = (string)($cfg['host'] ?? '');
  $mail->SMTPAuth = true;
  $mail->Username = (string)($cfg['username'] ?? '');
  $mail->Password = (string)($cfg['password'] ?? '');

  $enc = strtolower((string)($cfg['encryption'] ?? 'tls'));
  if ($enc === 'ssl' || ((int)($cfg['port'] ?? 0)) === 465) {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = (int)($cfg['port'] ?? 465);
  } else {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = (int)($cfg['port'] ?? 587);
  }

  $mail->setFrom((string)$cfg['from_email'], (string)$cfg['from_name']);
  $mail->addAddress($to);
  $mail->Subject = 'Prueba PHPMailer (servidor)';
  $mail->Body = 'OK - Email de prueba enviado desde public/test_mail_server.php';

  $mail->send();
  echo "OK - Email enviado a {$to}\n";
} catch (Exception $e) {
  echo "ERROR: {$mail->ErrorInfo}\n";
}

