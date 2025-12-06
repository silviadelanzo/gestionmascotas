<?php
require __DIR__ . '/../includes/bootstrap.php';

// Destruir sesión
session_destroy();

// Redirigir al index
$baseUrl = rtrim(dirname($_SERVER['PHP_SELF']), '/');
$baseUrl = rtrim(str_replace('/api', '', $baseUrl), '/');
header('Location: ' . $baseUrl . '/index_v2_5.php');
exit;
