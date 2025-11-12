<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container">
  <h2>Panel</h2>
  <ul>
    <li><a href="/dashboard/citas.php">Citas</a></li>
    <li><a href="/dashboard/recordatorios.php">Recordatorios</a></li>
    <li><a href="/dashboard/perfil.php">Mi perfil</a></li>
  </ul>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>