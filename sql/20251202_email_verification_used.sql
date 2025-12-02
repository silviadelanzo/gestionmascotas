-- Agrega marca de uso a tokens de verificación para distinguir ya verificados.
ALTER TABLE `email_verifications_app`
  ADD COLUMN `used_at` timestamp NULL DEFAULT NULL AFTER `expires_at`;
