<?php require_once __DIR__.'/includes/header.php'; ?>
<?php
$sent = false; $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require_once __DIR__.'/includes/config.php';
  $nombre = trim($_POST['nombre'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $mensaje = trim($_POST['mensaje'] ?? '');
  if ($nombre && filter_var($email, FILTER_VALIDATE_EMAIL) && $mensaje) {
    $to = CONTACT_TO;
    $subject = 'Nuevo contacto desde el sitio';
    $body = "Nombre: $nombre\nEmail: $email\n\n$mensaje\n";
    $headers = 'From: '.$email; // simple; en cPanel suele funcionar con mail()
    if (@mail($to, $subject, $body, $headers)) { $sent = true; } else { $error = 'No se pudo enviar el mensaje. Probá nuevamente.'; }
  } else {
    $error = 'Completá nombre, email y mensaje válidos.';
  }
}
?>
<h1 class="h4 mb-3">Contacto</h1>
<?php if($sent): ?>
  <div class="alert alert-success">¡Gracias! Te responderemos a la brevedad.</div>
<?php else: ?>
  <?php if($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post" class="row g-3" action="">
    <div class="col-12 col-md-6">
      <label class="form-label">Nombre</label>
      <input class="form-control" type="text" name="nombre" required>
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">Email</label>
      <input class="form-control" type="email" name="email" required>
    </div>
    <div class="col-12">
      <label class="form-label">Mensaje</label>
      <textarea class="form-control" rows="4" name="mensaje" required></textarea>
    </div>
    <div class="col-12">
      <button class="btn btn-primary" type="submit">Enviar</button>
    </div>
  </form>
<?php endif; ?>
<?php require_once __DIR__.'/includes/footer.php'; ?>
