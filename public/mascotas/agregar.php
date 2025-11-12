<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container">
  <h2>Agregar mascota</h2>
  <form method="post" action="/api/registrar_mascota.php" enctype="multipart/form-data">
    <label>Nombre <input type="text" name="nombre" required></label><br>
    <label>Especie <input type="text" name="especie" required></label><br>
    <label>Raza <input type="text" name="raza"></label><br>
    <label>Fecha de nacimiento <input type="date" name="nacimiento"></label><br>
    <button type="submit">Guardar</button>
  </form>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>