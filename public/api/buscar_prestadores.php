<?php
require __DIR__ . '/../includes/bootstrap.php';
header('Content-Type: application/json');
// TODO: SELECT con filtros provincia/localidad/q
echo json_encode(['items' => []]);