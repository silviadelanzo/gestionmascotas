<?php
require __DIR__ . '/../includes/bootstrap.php';
$nombre = trim($_POST['nombre'] ?? '');
$email  = trim($_POST['email'] ?? '');
$pass   = trim($_POST['password'] ?? '');
$prov   = intval($_POST['provincia_id'] ?? 0);
$loc    = intval($_POST['ciudad_id'] ?? 0);
if ($nombre==='' || $email==='' || $pass==='' || !$prov || !$loc) { http_response_code(400); exit('Faltan datos'); }
// TODO: insertar en usuarios con hash y FK provincia/ciudad
header('Location: /login.php');