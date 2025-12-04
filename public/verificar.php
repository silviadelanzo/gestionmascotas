<?php
require __DIR__ . '/includes/bootstrap.php';

$baseUrl = app_base_url();
$loginUrl = (parse_url($baseUrl, PHP_URL_SCHEME) !== null) ? ($baseUrl . '/login.php') : 'login.php';

$token = $_GET['token'] ?? '';
$message = '';
$isSuccess = false;
$pdo = null;

if ($token === '') {
  $message = 'Token invalido.';
} else {
  try {
    $pdo = db();

    $stmt = $pdo->prepare('SELECT user_id, expires_at, used_at FROM email_verifications_app WHERE token = :token LIMIT 1');
    $stmt->execute(['token' => $token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
      $message = 'Token invalido o expirado.';
    } else {
      $userId = (int)$row['user_id'];
      $isExpired = !empty($row['expires_at']) && strtotime($row['expires_at']) < time();

      if (!empty($row['used_at'])) {
        $message = 'Tu cuenta ya estaba verificada. Ahora puedes iniciar sesion.';
        $isSuccess = true;
      } elseif ($isExpired) {
        $message = 'Token invalido o expirado.';
      } else {
        $pdo->beginTransaction();

        $update = $pdo->prepare('UPDATE usuarios SET estado = :estado, email_verified_at = NOW() WHERE id = :id');
        $update->execute([
          'estado' => 'activo',
          'id' => $userId,
        ]);

        $markUsed = $pdo->prepare('UPDATE email_verifications_app SET used_at = NOW(), expires_at = NOW() WHERE token = :token');
        $markUsed->execute(['token' => $token]);

        $pdo->commit();

        $isSuccess = true;
        $message = 'Listo. Email verificado. Ya puedes iniciar sesion.';
      }
    }
  } catch (Throwable $e) {
    if ($pdo && $pdo->inTransaction()) {
      $pdo->rollBack();
    }
    $message = 'Ocurrio un error al verificar tu email.';
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Verificacion de email - Mascotas y Mimos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
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
      background: radial-gradient(circle at 15% 20%, rgba(255,214,165,0.55), transparent 38%),
                  radial-gradient(circle at 75% 10%, rgba(250,224,195,0.55), transparent 32%),
                  linear-gradient(135deg, #fff4ec, #f9e4d5);
      position: relative;
      overflow: hidden;
    }
    body::before {
      content: '';
      position: absolute;
      inset: 0;
      background: url('/assets/img/hero.webp') center/cover no-repeat;
      filter: blur(20px) brightness(0.9);
      opacity: 0.3;
      z-index: 0;
    }
    main {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 2rem 1rem;
    }
    .verify-card {
      width: 100%;
      max-width: 480px;
      background: rgba(255,255,255,0.94);
      backdrop-filter: blur(8px);
      border-radius: 30px;
      padding: 2.5rem 2.2rem;
      box-shadow: 0 25px 70px rgba(80, 50, 35, 0.18);
      text-align: center;
    }
    .status {
      width: 64px;
      height: 64px;
      margin: 0 auto 1rem;
      border-radius: 50%;
      display: grid;
      place-items: center;
      font-size: 1.8rem;
      color: #fff;
      background: linear-gradient(135deg, var(--brand), var(--brand-dark));
    }
    .verify-card h1 {
      margin: 0 0 0.5rem;
      font-size: 1.8rem;
    }
    .verify-card p {
      margin: 0 0 1.4rem;
      color: #6b4e43;
      line-height: 1.5;
    }
    .btn {
      display: inline-block;
      padding: 0.9rem 1.6rem;
      border-radius: 999px;
      background: linear-gradient(135deg, var(--brand), var(--brand-dark));
      color: #fff;
      font-weight: 700;
      text-decoration: none;
      box-shadow: 0 12px 24px rgba(169, 113, 85, 0.25);
    }
    .btn:hover { opacity: 0.93; }
  </style>
</head>
<body>
  <main>
    <section class="verify-card" aria-labelledby="verify-title">
      <div class="status"><?= $isSuccess ? '✓' : '!' ?></div>
      <h1 id="verify-title"><?= $isSuccess ? 'Listo' : 'Ups...' ?></h1>
      <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
      <?php if ($isSuccess): ?>
        <a class="btn" href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Ir a iniciar sesion</a>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
