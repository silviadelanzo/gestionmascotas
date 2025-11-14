<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/helpers.php';

header('Content-Type: text/html; charset=utf-8');

$token = isset($_GET['token']) ? trim((string)$_GET['token']) : '';
if ($token === '') {
    http_response_code(400);
    echo '<!doctype html><meta charset="utf-8"><title>Token inválido</title><p style="font-family:Arial">Token inválido.</p>';
    exit;
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, email, activo FROM suscripciones WHERE unsubscribe_token = ? LIMIT 1');
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) {
        echo '<!doctype html><meta charset="utf-8"><title>No encontrado</title><p style="font-family:Arial">Suscripción no encontrada.</p>';
        exit;
    }

    if ((int)$row['activo'] === 0) {
        echo '<!doctype html><meta charset="utf-8"><title>Ya dado de baja</title>'
           . '<div style="font-family:Arial;max-width:560px;margin:40px auto;background:#fff;padding:24px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.08);text-align:center">'
           . '<h1 style="margin:0 0 8px;color:#A97155;font-size:20px">Listo</h1>'
           . '<p style="margin:0;color:#5A3E36">El email ya estaba dado de baja.</p>'
           . '<p style="margin-top:16px"><a href="/gestionmascotas/public/" style="color:#A97155">Volver al sitio</a></p>'
           . '</div>';
        exit;
    }

    $upd = $pdo->prepare('UPDATE suscripciones SET activo = 0 WHERE id = ?');
    $upd->execute([$row['id']]);

    echo '<!doctype html><meta charset="utf-8"><title>Baja realizada</title>'
       . '<div style="font-family:Arial;max-width:560px;margin:40px auto;background:#fff;padding:24px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.08);text-align:center">'
       . '<h1 style="margin:0 0 8px;color:#A97155;font-size:20px">Listo</h1>'
       . '<p style="margin:0;color:#5A3E36">Dimos de baja el email '
       . htmlspecialchars((string)$row['email'], ENT_QUOTES, 'UTF-8')
       . ' de nuestras notificaciones.</p>'
       . '<p style="margin-top:16px"><a href="/gestionmascotas/public/" style="color:#A97155">Volver al sitio</a></p>'
       . '</div>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<!doctype html><meta charset="utf-8"><title>Error</title><p style="font-family:Arial">Error al procesar la baja.</p>';
}

