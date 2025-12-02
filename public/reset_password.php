<?php
require __DIR__ . '/includes/bootstrap.php';

$envCfg = require __DIR__ . '/config/env.php';
$baseUrl = rtrim((string)($envCfg['base_url'] ?? ''), '/');
$baseUrl = $baseUrl !== '' ? $baseUrl : 'http://localhost/public';

$email = trim($_GET['email'] ?? $_POST['email'] ?? '');
$token = $_GET['token'] ?? $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$passwordConfirm = $_POST['password_confirm'] ?? '';

$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email inválido.';
  }
  if ($token === '') {
    $errors[] = 'Token inválido.';
  }
  if ($password === '') {
    $errors[] = 'La contraseña es obligatoria.';
  } elseif (strlen($password) < 6) {
    $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
  }
  if ($passwordConfirm === '') {
    $errors[] = 'Confirma la contraseña.';
  } elseif ($password !== '' && $password !== $passwordConfirm) {
    $errors[] = 'Las contraseñas no coinciden.';
  }

  if (empty($errors)) {
    try {
      $pdo = db();
      $stmt = $pdo->prepare('SELECT token, created_at FROM password_reset_tokens WHERE email = :email LIMIT 1');
      $stmt->execute(['email' => $email]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$row) {
        $errors[] = 'Enlace inválido o expirado.';
      } else {
        $storedHash = $row['token'];
        $createdAt = strtotime($row['created_at'] ?? '1970-01-01');
        $isExpired = (time() - $createdAt) > 3600; // 1 hora
        if ($isExpired || !hash_equals($storedHash, hash('sha256', $token))) {
          $errors[] = 'Enlace inválido o expirado.';
        }
      }

      if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $update = $pdo->prepare('UPDATE usuarios SET password = :password, updated_at = NOW() WHERE email = :email');
        $update->execute([
          'password' => $hash,
          'email' => $email,
        ]);

        $del = $pdo->prepare('DELETE FROM password_reset_tokens WHERE email = :email');
        $del->execute(['email' => $email]);

        $successMessage = 'Tu contraseña fue restablecida. Ahora puedes iniciar sesión.';
      }
    } catch (PDOException $e) {
      $errors[] = 'No pudimos restablecer la contraseña. Intenta más tarde.';
    }
  }
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<style>
  .auth-wrapper {
    max-width: 640px;
    margin: 0 auto;
    padding: 2rem 1rem 3rem;
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }
  .auth-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 1.6rem;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    border: 1px solid #e5e7eb;
  }
  .auth-card h1 { margin: 0 0 0.75rem; font-size: 1.5rem; color: #111827; }
  .auth-card p { margin: 0 0 1.25rem; color: #4b5563; }
  .alert { border-radius: 12px; padding: 0.9rem 1rem; margin-bottom: 1rem; }
  .alert-error { background: #fef2f2; color: #b91c1c; }
  .alert-success { background: #ecfdf5; color: #047857; }
  form { display: flex; flex-direction: column; gap: 0.75rem; }
  label { display: flex; flex-direction: column; gap: 0.35rem; color: #374151; font-weight: 500; }
  input[type="password"] { padding: 0.8rem; border: 1px solid #e5e7eb; border-radius: 12px; font-size: 1rem; }
  button { padding: 0.9rem 1rem; border: none; border-radius: 12px; background: linear-gradient(135deg,#7c3aed,#6366f1); color: #fff; font-weight: 600; cursor: pointer; }
  button:hover { opacity: 0.95; }
</style>
<main class="auth-wrapper">
  <section class="auth-card">
    <h1>Elige una nueva contraseña</h1>
    <p>Ingresa tu nueva contraseña. El enlace expira en 1 hora.</p>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php elseif ($successMessage): ?>
    <div class="alert alert-success">
      <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <script>
      setTimeout(function () { window.location.href = '<?= $baseUrl ?>/login.php'; }, 2000);
    </script>
  <?php endif; ?>

  <?php if (!$successMessage): ?>
    <form method="post" novalidate>
      <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">

      <label>
        Nueva contraseña
        <input type="password" name="password" required>
      </label>
      <label>
        Confirmar contraseña
        <input type="password" name="password_confirm" required>
      </label>
      <button type="submit">Guardar contraseña</button>
    </form>
  <?php endif; ?>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
