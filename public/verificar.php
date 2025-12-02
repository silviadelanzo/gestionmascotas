<?php
require __DIR__ . '/includes/bootstrap.php';

$envCfg = require __DIR__ . '/config/env.php';
$baseUrl = rtrim((string)($envCfg['base_url'] ?? ''), '/');
if ($baseUrl === '') {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/public/verificar.php', PHP_URL_PATH);
  $dir = trim(dirname($uriPath), '/');
  $dir = $dir !== '' ? '/' . $dir : '';
  $baseUrl = $scheme . '://' . $host . $dir;
}
$loginUrl = (parse_url($baseUrl, PHP_URL_SCHEME) !== null) ? ($baseUrl . '/login.php') : 'login.php';

$token = $_GET['token'] ?? '';
$message = '';
$isSuccess = false;
$pdo = null;

if ($token === '') {
  $message = 'Token inválido.';
} else {
  try {
    $pdo = db();

    $stmt = $pdo->prepare('SELECT user_id, expires_at, used_at FROM email_verifications_app WHERE token = :token LIMIT 1');
    $stmt->execute(['token' => $token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
      $message = 'Token inválido o expirado.';
    } else {
      $userId = (int)$row['user_id'];
      $isExpired = !empty($row['expires_at']) && strtotime($row['expires_at']) < time();

      if (!empty($row['used_at'])) {
        $message = 'Tu cuenta ya estaba verificada. Ahora puedes iniciar sesión.';
        $isSuccess = true;
      } elseif ($isExpired) {
        $message = 'Token inválido o expirado.';
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
        $message = 'Listo. Email verificado. Ya podés iniciar sesión.';
      }
    }
  } catch (Throwable $e) {
    if ($pdo && $pdo->inTransaction()) {
      $pdo->rollBack();
    }
    $message = 'Ocurrió un error al verificar tu email.';
  }
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<style>
  .verify-wrapper {
    min-height: calc(100vh - 140px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem 3rem;
    background: #f5f6fa;
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }
  .verify-card {
    width: 100%;
    max-width: 420px;
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 18px 50px rgba(15, 23, 42, 0.14);
    padding: 2.5rem 2rem;
    text-align: center;
  }
  .verify-card h1 {
    font-size: 1.8rem;
    margin-bottom: 1rem;
    color: #111827;
  }
  .verify-card p {
    color: #4b5563;
    line-height: 1.5;
    margin-bottom: 1.5rem;
  }
  .verify-card .status {
    font-size: 2.2rem;
    margin-bottom: 1rem;
  }
  .verify-card .btn {
    display: inline-block;
    margin-top: 0.5rem;
    padding: 0.85rem 1.6rem;
    border-radius: 999px;
    background: linear-gradient(135deg, #7c3aed, #6366f1);
    color: #ffffff;
    font-weight: 600;
    text-decoration: none;
  }
  .verify-card .btn:hover {
    opacity: 0.93;
  }
</style>
<main class="verify-wrapper">
  <section class="verify-card">
    <div class="status"><?= $isSuccess ? '✅' : '⚠️' ?></div>
    <h1><?= $isSuccess ? '¡Listo!' : 'Ups...' ?></h1>
    <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    <?php if ($isSuccess): ?>
      <a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn">Ir a iniciar sesión</a>
    <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
