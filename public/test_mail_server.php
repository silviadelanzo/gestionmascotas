<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require __DIR__ . '/../lib/PHPMailer/SMTP.php';
require __DIR__ . '/../lib/PHPMailer/Exception.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'mail.mascotasymimos.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'no-responder@mascotasymimos.com';
    $mail->Password = 'O!,xi7lOTcfla[%K';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 587;

    $mail->setFrom('no-responder@mascotasymimos.com', 'Sistema Mascotas');
    $mail->addAddress('contacto@mascotasymimos.com'); // destinatario de prueba

    $mail->Subject = 'Prueba PHPMailer';
    $mail->Body = 'Email enviado correctamente desde test_email.php';

    $mail->send();
    echo "OK - Email enviado";
} catch (Exception $e) {
    echo "Error al enviar: {$mail->ErrorInfo}";
}
