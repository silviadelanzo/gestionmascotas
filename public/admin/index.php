<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
helpers_require_admin();
?>
<main class="container">
  <h2>Admin</h2>
  <ul>
    <li><a href="/admin/usuarios.php">Usuarios</a></li>
    <li><a href="/admin/prestadores.php">Prestadores</a></li>
    <li><a href="/admin/localidades.php">Localidades</a></li>
    <li><a href="/admin/provincias.php">Provincias</a></li>
  </ul>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>