<?php
require __DIR__ . '/../includes/bootstrap.php';
if (empty($_SESSION['uid'])) { http_response_code(401); exit('No autenticado'); }
// TODO: validar y mover uploads
echo 'ok';