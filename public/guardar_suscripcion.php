<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/helpers.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;
require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';
require_once __DIR__ . '/../lib/PHPMailer/Exception.php';

header('Content-Type: text/html; charset=utf-8');

function respond(string $title, string $message, bool $ok = true): void {
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1">'
       . '<script src="https://cdn.tailwindcss.com"></script>'
       . '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title></head><body class="min-h-screen flex items-center justify-center" style="background: linear-gradient(135deg,#FFD6A5 0%,#FAE0C3 100%);">'
       . '<div class="bg-white/90 backdrop-blur rounded-xl p-8 shadow w-full max-w-md text-center">'
       . '<img src="assets/logo/logo.png" alt="Mascotas y Mimos" class="w-20 h-20 object-contain mx-auto mb-4">'
       . '<h1 class="text-xl font-semibold mb-2">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>'
       . '<p class="text-gray-700">' . $message . '</p>'
       . '<a href="index.php" class="inline-block mt-6 text-orange-600 hover:text-orange-700">Volver</a>'
       . '</div></body></html>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond('Suscripción', 'Método no permitido', false);
}

$nombre = isset($_POST['nombre']) ? trim((string)$_POST['nombre']) : null;
$email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
$autorizacion = isset($_POST['autorizacion']) ? 1 : 0;

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond('Suscripción', 'Email inválido. Por favor intenta nuevamente.', false);
}

try {
    $pdo = db();
    // Asegurar tabla si no existe (no destructivo)
    $pdo->exec("CREATE TABLE IF NOT EXISTS suscripciones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NULL,
        email VARCHAR(190) NOT NULL UNIQUE,
        tipo VARCHAR(30) NOT NULL DEFAULT 'usuario',
        autorizacion TINYINT(1) NOT NULL DEFAULT 1,
        fecha_alta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $stmt = $pdo->prepare('INSERT INTO suscripciones (nombre, email, tipo, autorizacion, fecha_alta) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([
        $nombre !== '' ? $nombre : null,
        $email,
        'usuario',
        $autorizacion,
    ]);

    // Enviar correo de agradecimiento (no bloquea la confirmación)
    $mailCfg = require __DIR__ . '/../config/mail.php';
    $m = new PHPMailer(true);
    try {
        $m->isSMTP();
        $m->Host = $mailCfg['host'];
        $m->SMTPAuth = true;
        $m->Username = $mailCfg['username'];
        $m->Password = $mailCfg['password'];

        // Selección robusta de cifrado/puerto
        $enc = strtolower((string)($mailCfg['encryption'] ?? 'tls'));
        if ($enc === 'ssl' || ((int)($mailCfg['port'] ?? 0)) === 465) {
            $m->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $m->Port = (int)($mailCfg['port'] ?? 465);
        } else {
            $m->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $m->Port = (int)($mailCfg['port'] ?? 587);
        }

        $m->CharSet = 'UTF-8';
        $m->setFrom($mailCfg['from_email'], $mailCfg['from_name']);
        $m->addAddress($email, $nombre ?: '');
        $m->isHTML(true);
        $m->Subject = 'Gracias por suscribirte a Mascotas y Mimos';
        $embedLogo = __DIR__ . '/assets/logo/logo.png';
        if (file_exists($embedLogo)) {
            $m->AddEmbeddedImage($embedLogo, 'logoimg', 'logo.png', 'base64', 'image/png');
        }
        $html = '<div style="font-family:Poppins, sans-serif;text-align:center;color:#A97155;">'
              . '<img src="cid:logoimg" width="150" style="margin-bottom:20px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.15);">'
              . '<h2>¡Gracias por suscribirte a <strong>Mascotas y Mimos</strong>!</h2>'
              . '<p>Sitio dedicado a cuidar y mimar a nuestros mejores compañeros.</p>'
              . '<p><a href="https://mascotasymimos.com" style="color:#A97155;text-decoration:none;font-weight:bold;">Visitanos en mascotasymimos.com</a></p>'
              . '</div>';
        $m->Body = $html;
        $m->AltBody = 'Gracias por suscribirte a Mascotas y Mimos. Visitá mascotasymimos.com';
        $m->send();
    } catch (MailException $e) {
        if (isset($_GET['debug'])) {
            respond('Email no enviado', 'Detalle: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'), false);
        }
    }

    respond('¡Gracias por suscribirte!', 'Te avisaremos cuando el sitio esté disponible.');
} catch (PDOException $e) {
    // Manejo de duplicado
    $msg = strtolower($e->getMessage());
    if ((int)$e->getCode() === 23000 || str_contains($msg, 'duplicate') || str_contains($msg, 'uniq')) {
        respond('Ya estás suscrito', 'Tu email ya estaba registrado. ¡Gracias!');
    }
    if (isset($_GET['debug'])) {
        respond('Error DB', 'Detalle: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'), false);
    }
    respond('Error', 'No pudimos guardar tu suscripción. Intenta más tarde.');
}
