<?php
require __DIR__ . '/../includes/bootstrap.php';

// Destruir sesión
session_destroy();

// Redirigir al home actual
header('Location: ' . home_url());
exit;
