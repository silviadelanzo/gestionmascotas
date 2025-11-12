<?php
require __DIR__ . '/../includes/bootstrap.php';
$email = trim($_POST['email'] ?? '');
$pass  = trim($_POST['password'] ?? '');
if ($email === '' || $pass === '') { http_response_code(400); exit('Faltan datos'); }
// TODO: validar contra tabla usuarios
$_SESSION['uid'] = 1; // mock
$_SESSION['is_admin'] = false;
header('Location: /dashboard/');