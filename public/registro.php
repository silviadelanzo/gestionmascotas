<?php
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/../config/db.php';

$errors = [];
$successMessage = '';
$shouldRedirect = false;

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$tipoUsuario = $_POST['tipo_usuario'] ?? 'dueño';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($nombre === '') {
    $errors[] = 'El nombre es obligatorio.';
  }

  if ($email === '') {
    $errors[] = 'El email es obligatorio.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Ingresa un email válido.';
  }

  if ($password === '') {
    $errors[] = 'La contraseña es obligatoria.';
  } elseif (strlen($password) < 6) {
    $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
  }

  $allowedTypes = ['dueño', 'prestador'];
  if ($tipoUsuario === '' || !in_array($tipoUsuario, $allowedTypes, true)) {
    $errors[] = 'Selecciona un tipo de usuario válido.';
  }

  if (empty($errors)) {
    try {
      $pdo = db();
      $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
      $stmt->execute(['email' => $email]);
      if ($stmt->fetch()) {
        $errors[] = 'Ya existe una cuenta con ese email.';
      }
    } catch (PDOException $e) {
      $errors[] = 'Ocurrió un error al validar el email. Intenta más tarde.';
    }
  }

  if (empty($errors)) {
    try {
      if (!isset($pdo)) {
        $pdo = db();
      }
      $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nombre, email, password, tipo_usuario, estado, creado_en)
         VALUES (:nombre, :email, :password, :tipo_usuario, :estado, NOW())'
      );

      $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'password' => $password,
        'tipo_usuario' => $tipoUsuario,
        'estado' => 'pendiente',
      ]);

      $newUserId = (int)$pdo->lastInsertId();

      require_once __DIR__ . '/../lib/Mailer.php';

      $token = bin2hex(random_bytes(32));

      $stmt = $pdo->prepare('INSERT INTO email_verifications (user_id, token) VALUES (:user_id, :token)');
      $stmt->execute([
        'user_id' => $newUserId,
        'token' => $token,
      ]);

      $verifyUrl = "https://mascotasymimos.com/public/verificar.php?token=$token";

      $mail = new Mailer();
      $mail->send(
        $email,
        'Verificación de Cuenta',
        "<p>Hola,</p>
    <p>Gracias por registrarte. Haz clic en el siguiente enlace para activar tu cuenta:</p>
    <p><a href='$verifyUrl'>$verifyUrl</a></p>"
      );

      $successMessage = 'Tu cuenta fue creada. Te redirigiremos al inicio de sesión.';
      $shouldRedirect = true;
      $nombre = '';
      $email = '';
      $password = '';
      $tipoUsuario = 'dueño';
    } catch (PDOException $e) {
      $errors[] = 'No pudimos crear tu cuenta. Intenta nuevamente.';
    }
  }
}

if ($successMessage && $shouldRedirect) {
  header('Refresh: 2; URL=/public/login.php');
}

require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<style>
  body {
    background: #f5f6fa;
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }
  .auth-layout {
    min-height: calc(100vh - 120px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem 3rem;
  }
  .auth-card {
    width: 100%;
    max-width: 420px;
    background: #ffffff;
    border-radius: 28px;
    box-shadow: 0 20px 60px rgba(15, 23, 42, 0.12);
    padding: 2.5rem 2rem;
  }
  .auth-card h1 {
    margin-bottom: 0.5rem;
    font-size: 1.9rem;
    text-align: center;
    color: #1f2937;
  }
  .auth-card p {
    margin: 0 0 1.8rem;
    text-align: center;
    color: #6b7280;
  }
  .alert {
    border-radius: 18px;
    padding: 0.9rem 1.1rem;
    font-size: 0.95rem;
    margin-bottom: 1.2rem;
  }
  .alert ul {
    margin: 0.35rem 0 0;
    padding-left: 1.2rem;
  }
  .alert-error {
    background: #fef2f2;
    color: #b91c1c;
  }
  .alert-success {
    background: #ecfdf5;
    color: #047857;
  }
  .auth-form .field {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    margin-bottom: 1rem;
    font-size: 0.95rem;
    color: #475467;
  }
  .auth-form input,
  .auth-form select {
    width: 100%;
    padding: 0.85rem 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    font-size: 1rem;
    background: #f8fafc;
    transition: border 0.2s ease, box-shadow 0.2s ease;
  }
  .auth-form input:focus,
  .auth-form select:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    background: #ffffff;
  }
  .cta-button {
    width: 100%;
    padding: 1rem;
    border: none;
    border-radius: 20px;
    font-size: 1.05rem;
    font-weight: 600;
    background: linear-gradient(135deg, #7c3aed, #6366f1);
    color: #ffffff;
    cursor: pointer;
    transition: transform 0.15s ease, box-shadow 0.2s ease;
    margin-top: 0.5rem;
  }
  .cta-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 15px 20px rgba(99, 102, 241, 0.25);
  }
  .login-link {
    margin-top: 1.4rem;
    text-align: center;
    font-size: 0.95rem;
    color: #6b7280;
  }
  .login-link a {
    color: #4f46e5;
    font-weight: 600;
    text-decoration: none;
  }
  .login-link a:hover {
    text-decoration: underline;
  }
</style>
<main class="auth-layout">
  <section class="auth-card">
    <h1>Mascotas y Mimos</h1>
    <p>Crea tu cuenta para comenzar.</p>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-error">
        <strong>Revisa la información:</strong>
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
        setTimeout(function () {
          window.location.href = '/public/login.php';
        }, 1800);
      </script>
    <?php endif; ?>

    <form method="post" class="auth-form" novalidate>
      <label class="field" for="nombre">
        <span>Nombre</span>
        <input
          type="text"
          id="nombre"
          name="nombre"
          value="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
          required
        >
      </label>

      <label class="field" for="email">
        <span>Email</span>
        <input
          type="email"
          id="email"
          name="email"
          value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
          required
        >
      </label>

      <label class="field" for="password">
        <span>Contraseña</span>
        <input
          type="password"
          id="password"
          name="password"
          required
        >
      </label>

      <label class="field" for="tipo_usuario">
        <span>Tipo de usuario</span>
        <select id="tipo_usuario" name="tipo_usuario" required>
          <option value="dueño" <?= $tipoUsuario === 'dueño' ? 'selected' : '' ?>>Dueño</option>
          <option value="prestador" <?= $tipoUsuario === 'prestador' ? 'selected' : '' ?>>Prestador</option>
        </select>
      </label>

      <button type="submit" class="cta-button">Registrar</button>
    </form>

    <div class="login-link">
      ¿Ya tienes cuenta? <a href="/public/login.php">Inicia sesión</a>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
