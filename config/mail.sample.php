<?php
// Plantilla de configuración SMTP (NO versionar credenciales reales).
return [
  'host' => 'smtp.example.com',
  'port' => 587,
  'username' => 'user@example.com',
  'password' => 'your-smtp-password',
  'encryption' => 'tls', // tls | ssl
  'from_email' => 'noreply@example.com',
  'from_name' => 'Mascotas y Mimos',
  'notify_email' => 'mascotasymimos@gmail.com',
];
