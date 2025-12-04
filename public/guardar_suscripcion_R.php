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

function createMailer(array $mailCfg): PHPMailer {
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond('Suscripción', 'Método no permitido', false);
}

$nombre = isset($_POST['nombre']) ? trim((string)$_POST['nombre']) : null;

$honeypot = isset($_POST['hp_telefono']) ? trim((string)$_POST['hp_telefono']) : '';
if ($honeypot !== '') {
    // Posible bot: responder exito generico sin procesar nada
    respond('Gracias por suscribirte', 'Te avisaremos cuando el sitio este disponible.');
}

$email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
$autorizacion = isset($_POST['autorizacion']) ? 1 : 0;
$perfil = isset($_POST['perfil']) ? strtolower(trim((string)$_POST['perfil'])) : 'dueno';
if (!in_array($perfil, ['dueno', 'prestador'], true)) {
    $perfil = 'dueno';
}
$perfilLabel = $perfil === 'prestador' ? 'prestador/a' : 'dueño/a de mascotas';

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond('Suscripción', 'Email inválido. Por favor, intentá nuevamente.', false);
}

try {
    $pdo = db();
    // Asegurar tabla y columnas si no existen (no destructivo)
    $pdo->exec("CREATE TABLE IF NOT EXISTS suscripciones (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NULL,
        email VARCHAR(190) NOT NULL UNIQUE,
        tipo VARCHAR(30) NOT NULL DEFAULT 'usuario',
        autorizacion TINYINT(1) NOT NULL DEFAULT 1,
        activo TINYINT(1) NOT NULL DEFAULT 1,
        fecha_alta DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        unsubscribe_token VARCHAR(64) NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Si la tabla existía sin columnas nuevas, agregarlas de forma segura
    $cols = $pdo->query("SHOW COLUMNS FROM suscripciones")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('activo', $cols, true)) {
        $pdo->exec("ALTER TABLE suscripciones ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1 AFTER autorizacion");
    }
    if (!in_array('unsubscribe_token', $cols, true)) {
        $pdo->exec("ALTER TABLE suscripciones ADD COLUMN unsubscribe_token VARCHAR(64) NULL UNIQUE AFTER fecha_alta");
    }

    // Normalizar email (case-insensitive) y verificar existencia
    $email = strtolower(trim($email));
    $check = $pdo->prepare('SELECT nombre FROM suscripciones WHERE email = ? LIMIT 1');
    $check->execute([$email]);
    if ($row = $check->fetch(PDO::FETCH_ASSOC)) {
        if (empty($nombre)) {
            $nombre = isset($row['nombre']) && $row['nombre'] !== '' ? (string)$row['nombre'] : ($nombre ?? '');
        }
        // Email ya existente: no insertar ni enviar correo
        respond('Ya estás suscrito', 'Tu email ya estaba registrado. ¡Gracias!');
    }

    // Insertar nuevo registro (sin token de desuscripción)
    $inserted = false;
    try {
        $stmt = $pdo->prepare('INSERT INTO suscripciones (nombre, email, tipo, autorizacion, fecha_alta) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([
            ($nombre !== '' && $nombre !== null) ? trim($nombre) : null,
            $email,
            $perfil,
            $autorizacion,
        ]);
        $inserted = true;
    } catch (PDOException $e) {
        // Si falla por otra razón, propagar; la columna unsubscribe_token no se usa en el INSERT
        throw $e;
    }

    // Enviar correo de agradecimiento (solo para nuevas suscripciones)
    $mailCfg = require __DIR__ . '/../config/mail.php';
    $m = createMailer($mailCfg);
    try {
        $m->addAddress($email, $nombre ?: '');
        $m->isHTML(true);
        $m->Subject = 'Gracias por suscribirte a Mascotas y Mimos';
        $embedLogo = __DIR__ . '/assets/logo/logo.png';
        if (file_exists($embedLogo)) {
            $m->AddEmbeddedImage($embedLogo, 'logoimg', 'logo.png', 'base64', 'image/png');
        }
        $perfilMensaje = $perfil === 'prestador'
            ? 'Te registraste como prestador/a para ofrecer tus servicios.'
            : 'Te registraste como dueño/a para cuidar mejor a tus mascotas.';
        $html = '<div style="font-family:Poppins, sans-serif;text-align:center;color:#A97155;">'
              . '<a href="https://mascotasymimos.com" target="_blank" style="text-decoration:none">'
              . '<img src="cid:logoimg" width="150" style="margin-bottom:20px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.15);" alt="Mascotas y Mimos">'
              . '</a>'
              . '<h2>¡Gracias por suscribirte a <strong>Mascotas y Mimos</strong>!</h2>'
              . '<p>' . htmlspecialchars($perfilMensaje, ENT_QUOTES, 'UTF-8') . '</p>'
              . '<p>Sitio dedicado a cuidar y mimar a nuestros mejores compañeros.</p>'
              . '<p><a href="https://mascotasymimos.com" style="color:#A97155;text-decoration:none;font-weight:bold;">Visitanos en mascotasymimos.com</a></p>'
              . '</div>';
        $m->Body = $html;
        $m->AltBody = "Gracias por suscribirte a Mascotas y Mimos.\n"
                    . "Sitio: https://mascotasymimos.com\n"
                    . "Perfil: {$perfilLabel}\n";
        $m->send();
    } catch (MailException $e) {
        if (isset($_GET['debug'])) {
            respond('Email no enviado', 'Detalle: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'), false);
        }
    }

    $notifyEmail = (string)($mailCfg['notify_email'] ?? 'mascotasymimos@gmail.com');
    if ($notifyEmail !== '') {
        try {
            $adminMailer = createMailer($mailCfg);
            $adminMailer->addAddress($notifyEmail, 'Mascotas y Mimos');
            $adminMailer->isHTML(true);
            $adminMailer->Subject = 'Nuevo registro de suscripción';
            $nombreSafe = $nombre !== null ? htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') : 'Sin nombre';
            $emailSafe = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
            $fechaAlta = date('Y-m-d H:i:s');
            $autorizacionLabel = $autorizacion ? 'Sí' : 'No';
            $adminMailer->Body = '<div style="font-family:Poppins, sans-serif;color:#5A3E36;">'
                               . '<h3>Nuevo registro en la landing</h3>'
                               . '<ul style="list-style:none;padding-left:0;">'
                               . '<li><strong>Nombre:</strong> ' . $nombreSafe . '</li>'
                               . '<li><strong>Email:</strong> ' . $emailSafe . '</li>'
                               . '<li><strong>Perfil:</strong> ' . htmlspecialchars($perfilLabel, ENT_QUOTES, 'UTF-8') . '</li>'
                               . '<li><strong>Autorización:</strong> ' . $autorizacionLabel . '</li>'
                               . '<li><strong>Fecha:</strong> ' . $fechaAlta . '</li>'
                               . '</ul>'
                               . '</div>';
            $adminMailer->AltBody = "Nuevo registro en la landing\n"
                                  . "Nombre: {$nombreSafe}\n"
                                  . "Email: {$emailSafe}\n"
                                  . "Perfil: {$perfilLabel}\n"
                                  . "Autorización: {$autorizacionLabel}\n"
                                  . "Fecha: {$fechaAlta}\n";
            $adminMailer->send();
        } catch (MailException $e) {
            if (isset($_GET['debug'])) {
                respond('Notificación no enviada', 'Detalle: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'), false);
            }
        }
    }

    respond('¡Gracias por suscribirte!', 'Te avisaremos cuando el sitio esté disponible.');
} catch (PDOException $e) {
    // Manejo de duplicado (condición de carrera): no enviar correo
    $msg = strtolower($e->getMessage());
    if ((int)$e->getCode() === 23000 || str_contains($msg, 'duplicate') || str_contains($msg, 'uniq')) {
        respond('Ya estás suscrito', 'Tu email ya estaba registrado. ¡Gracias!');
    }
    if (isset($_GET['debug'])) {
        respond('Error DB', 'Detalle: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'), false);
    }
    respond('Error', 'No pudimos guardar tu suscripción. Intentá más tarde.');
}