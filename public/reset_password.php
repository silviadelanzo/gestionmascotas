<?php
require __DIR__ . '/includes/bootstrap.php';

$envCfg = require __DIR__ . '/config/env.php';
$baseUrl = rtrim((string)($envCfg['base_url'] ?? ''), '/');
if ($baseUrl === '') {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/public/reset_password.php', PHP_URL_PATH);
  $dir = trim(dirname($uriPath), '/');
  $dir = $dir !== '' ? '/' . $dir : '';
  $baseUrl = $scheme . '://' . $host . $dir;
}
$loginUrl = (parse_url($baseUrl, PHP_URL_SCHEME) !== null) ? ($baseUrl . '/login.php') : 'login.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$errors = [];
$success = false;

function ensurePasswordResetTable(PDO $pdo): void {
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
}

function fetchValidToken(PDO $pdo, string $token): ?array {
  if ($token === '') {
    return null;
  }
  ensurePasswordResetTable($pdo);
  $stmt = $pdo->prepare(
    'SELECT pr.id, pr.user_id, pr.expires_at, pr.used_at, u.email
     FROM password_resets_app pr
     JOIN usuarios u ON u.id = pr.user_id
     WHERE pr.token = :token
     LIMIT 1'
  );
  $stmt->execute(['token' => $token]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$row) {
    return null;
  }
  $isExpired = !empty($row['expires_at']) && strtotime($row['expires_at']) < time();
  $isUsed = !empty($row['used_at']);
  if ($isExpired || $isUsed) {
    return null;
  }
  return $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password = $_POST['password'] ?? '';
  $passwordConfirm = $_POST['password_confirm'] ?? '';

  if ($password === '') {
    $errors[] = 'La contrasena es obligatoria.';
  } elseif (strlen($password) < 6) {
    $errors[] = 'La contrasena debe tener al menos 6 caracteres.';
  }
  if ($passwordConfirm === '') {
    $errors[] = 'Confirma la contrasena.';
  } elseif ($password !== '' && $password !== $passwordConfirm) {
    $errors[] = 'Las contrasenas no coinciden.';
  }

  if (empty($errors)) {
    try {
      $pdo = db();
      $tokenRow = fetchValidToken($pdo, $token);
      if (!$tokenRow) {
        $errors[] = 'Token invalido o expirado.';
      } else {
        $pdo->beginTransaction();

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $updateUser = $pdo->prepare('UPDATE usuarios SET password = :pwd WHERE id = :id');
        $updateUser->execute([
          'pwd' => $hashed,
          'id' => $tokenRow['user_id'],
        ]);

        $markUsed = $pdo->prepare(
          'UPDATE password_resets_app SET used_at = NOW(), expires_at = NOW() WHERE id = :id'
        );
        $markUsed->execute(['id' => $tokenRow['id']]);

        $pdo->commit();
        $success = true;
      }
    } catch (Throwable $e) {
      if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
      }
      $errors[] = 'No pudimos actualizar tu contrasena. Intenta de nuevo.';
    }
  }
} else {
  if ($token === '') {
    $errors[] = 'Token invalido.';
  } else {
    try {
      $pdo = db();
      $tokenRow = fetchValidToken($pdo, $token);
      if (!$tokenRow) {
        $errors[] = 'Token invalido o expirado.';
      }
    } catch (Throwable $e) {
      $errors[] = 'No pudimos validar tu token.';
    }
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Elegir nueva contrasena - Mascotas y Mimos</title>
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
    .auth-card {
      width: 100%;
      max-width: 460px;
      background: rgba(255,255,255,0.94);
      backdrop-filter: blur(8px);
      border-radius: 30px;
      padding: 2.5rem 2.2rem;
      box-shadow: 0 25px 70px rgba(80, 50, 35, 0.18);
    }
    .auth-card h1 {
      margin: 0 0 0.5rem;
      font-size: 1.9rem;
      text-align: center;
    }
    .auth-card p {
      margin: 0 0 1.4rem;
      text-align: center;
      color: #6b4e43;
    }
    .form-field {
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
      margin-bottom: 1rem;
      font-size: 0.95rem;
    }
    .form-field label { font-weight: 600; }
    .form-control {
      width: 100%;
      padding: 0.85rem 1rem;
      border: 1px solid #e6d8cf;
      border-radius: 16px;
      background: #fffaf7;
      font-size: 1rem;
      transition: border 0.2s ease, box-shadow 0.2s ease;
    }
    .form-control:focus {
      outline: none;
      border-color: var(--brand);
      box-shadow: 0 0 0 3px rgba(169, 113, 85, 0.18);
      background: #ffffff;
    }
    .password-group { position: relative; }
    .password-toggle {
      position: absolute;
      top: 50%;
      right: 0.65rem;
      transform: translateY(-50%);
      border: none;
      background: transparent;
      color: #6b4e43;
      font-size: 1rem;
      cursor: pointer;
    }
    .cta {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: 20px;
      background: linear-gradient(135deg, var(--brand), var(--brand-dark));
      color: #fff;
      font-weight: 700;
      font-size: 1.05rem;
      cursor: pointer;
      transition: transform 0.15s ease, box-shadow 0.2s ease;
    }
    .cta:hover {
      transform: translateY(-1px);
      box-shadow: 0 15px 28px rgba(169, 113, 85, 0.25);
    }
    .alert {
      border-radius: 18px;
      padding: 0.9rem 1.1rem;
      font-size: 0.95rem;
      margin-bottom: 1rem;
    }
    .alert-error { background: #fef2f2; color: #b91c1c; }
    .alert-success { background: #ecfdf5; color: #047857; }
    .links { text-align: center; margin-top: 1.2rem; }
    .links a { color: var(--brand); font-weight: 600; text-decoration: none; }
    .links a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <main>
    <section class="auth-card" aria-labelledby="reset-title">
      <h1 id="reset-title">Elige una nueva contrasena</h1>
      <p>Tu enlace vence en poco tiempo. Elige una contrasena segura.</p>

      <?php if ($success): ?>
        <div class="alert alert-success" role="status">Listo. Actualizamos tu contrasena.</div>
        <div class="links"><a href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Ir a iniciar sesion</a></div>
      <?php elseif (!empty($errors)): ?>
        <div class="alert alert-error" role="alert">
          <?php foreach ($errors as $err): ?>
            <div><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (!$success): ?>
        <form method="post" novalidate>
          <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
          <div class="form-field">
            <label for="password">Nueva contrasena</label>
            <div class="password-group">
              <input id="password" name="password" type="password" class="form-control" required autocomplete="new-password" minlength="6">
              <button type="button" class="password-toggle" data-target="password" aria-label="Mostrar contrasena">👁</button>
            </div>
          </div>
          <div class="form-field">
            <label for="password_confirm">Confirmar contrasena</label>
            <div class="password-group">
              <input id="password_confirm" name="password_confirm" type="password" class="form-control" required autocomplete="new-password">
              <button type="button" class="password-toggle" data-target="password_confirm" aria-label="Mostrar contrasena confirmada">👁</button>
            </div>
          </div>
          <button type="submit" class="cta">Guardar contrasena</button>
        </form>
        <div class="links">
          <a href="/public/olvide_password.php">Volver a pedir enlace</a>
        </div>
      <?php endif; ?>
    </section>
  </main>
  <script>
    (function () {
      document.querySelectorAll('.password-toggle').forEach((btn) => {
        btn.addEventListener('click', () => {
          const targetId = btn.getAttribute('data-target');
          const field = document.getElementById(targetId);
          if (!field) return;
          const isPassword = field.type === 'password';
          field.type = isPassword ? 'text' : 'password';
          btn.setAttribute('aria-label', isPassword ? 'Ocultar contrasena' : 'Mostrar contrasena');
        });
      });
    })();
  </script>
</body>
</html>
