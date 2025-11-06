<?php require_once __DIR__.'/config.php'; ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mascotas y Mimos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= APP_URL ?>/assets/css/styles.css" rel="stylesheet">
  <link rel="preconnect" href="https://unpkg.com">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= APP_URL ?>/index.php">
      <span class="fw-semibold">Mascotas y Mimos</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/servicios.php">Servicios</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/prestadores.php">Prestadores</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>/contacto.php">Contacto</a></li>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
