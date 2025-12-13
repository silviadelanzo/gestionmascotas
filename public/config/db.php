<?php
/**
 * Config de DB (local por defecto).
 *
 * En producción, editá este archivo directamente en el servidor y evitá que el deploy lo pise.
 *
 * Si tu hosting permite variables de entorno, también podés definir:
 * DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS
 */

$envHost = getenv('DB_HOST') ?: '';
$envName = getenv('DB_NAME') ?: '';
$envUser = getenv('DB_USER') ?: '';

if ($envHost !== '' && $envName !== '' && $envUser !== '') {
  return [
    'host' => $envHost,
    'port' => (int)(getenv('DB_PORT') ?: 3306),
    'name' => $envName,
    'user' => $envUser,
    'pass' => (string)(getenv('DB_PASS') ?: ''),
  ];
}

return [
  'host' => 'localhost',
  'port' => 3306,
  'name' => 'petcare_saas',
  'user' => 'root',
  'pass' => '',
];
