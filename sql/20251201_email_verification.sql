-- Agrega verificación de email para usuarios (ambientes local y server).

-- Campos de estado y verificación en usuarios
ALTER TABLE `usuarios`
  ADD COLUMN `email_verified_at` timestamp NULL DEFAULT NULL AFTER `password`,
  ADD COLUMN `estado` enum('pendiente','activo','suspendido') NOT NULL DEFAULT 'pendiente' AFTER `email_verified_at`;

-- Tabla de tokens de verificación (vinculada a usuarios)
CREATE TABLE IF NOT EXISTS `email_verifications_app` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_token` (`token`),
  KEY `idx_user` (`user_id`),
  CONSTRAINT `fk_email_verifications_app_user`
    FOREIGN KEY (`user_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
