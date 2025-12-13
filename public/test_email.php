<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carga PHPMailer desde la copia local (sin Composer)
require __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/../lib/PHPMailer/SMTP.php';
require __DIR__ . '/../lib/PHPMailer/Exception.php';

$cfg = require __DIR__ . '/../config/mail.php';

$mail = new PHPMailer(true);

// Depuración opcional: ?debug=2
$debug = isset($_GET['debug']) ? (int)$_GET['debug'] : 0; // 0,1,2
$mail->SMTPDebug = $debug; // 0=off, 2=verbose
$mail->Debugoutput = 'html';

// Destinatario opcional: ?to=correo@dominio
$to = isset($_GET['to']) && filter_var($_GET['to'], FILTER_VALIDATE_EMAIL)
  ? $_GET['to']
  : $cfg['username']; // por defecto, se envía a la cuenta remitente

try {
    $mail->isSMTP();
    $mail->Host = $cfg['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $cfg['username'];
    $mail->Password = $cfg['password'];

    if (($cfg['encryption'] ?? 'tls') === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }
    $mail->Port = (int)$cfg['port'];

    $mail->setFrom($cfg['from_email'], $cfg['from_name']);
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->Subject = 'Prueba PHPMailer';
    $mail->Body = '<p>Email de prueba enviado desde <code>public/test_email.php</code></p>';

    // Diagnóstico SSL opcional: ?insecure=1 (no usar en producción)
    if (isset($_GET['insecure'])) {
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
    }

    if (!$mail->send()) {
        echo 'Error al enviar: ' . htmlspecialchars($mail->ErrorInfo, ENT_QUOTES, 'UTF-8');
    } else {
        echo 'OK - Email enviado a ' . htmlspecialchars($to, ENT_QUOTES, 'UTF-8');
    }
} catch (Exception $e) {
    echo 'Error al enviar: ' . htmlspecialchars($mail->ErrorInfo ?: $e->getMessage(), ENT_QUOTES, 'UTF-8');
}
