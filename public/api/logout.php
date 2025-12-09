<?php
require __DIR__ . '/../includes/bootstrap.php';

// Cerrar sesión con el nuevo sistema de tokens
auth_logout();

// También destruir sesión PHP
session_destroy();

// Redirigir al home
header('Location: ' . home_url());
exit;
