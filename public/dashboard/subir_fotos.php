<?php
require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/navbar.php';
?>
<main class="container">
  <h2>Subir fotos (prestador destacado)</h2>
  <form method="post" action="/api/subir_imagen.php" enctype="multipart/form-data">
    <input type="file" name="foto[]" multiple accept="image/*">
    <button type="submit">Subir</button>
  </form>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>