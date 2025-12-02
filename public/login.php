<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
?>
<main class="container">
  <h2>Iniciar sesión</h2>
  <form method="post" action="/api/login.php">
    <label>Email <input type="email" name="email" required></label><br>
    <label>Contraseña <input type="password" name="password" required></label><br>
    <button type="submit">Entrar</button>
  </form>
  <p><a href="/public/olvide_password.php">¿Olvidaste tu contraseña?</a></p>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
