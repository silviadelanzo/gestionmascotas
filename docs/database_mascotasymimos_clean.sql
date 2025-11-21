SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

/* ========================================
   TABLA: usuarios (dueños, prestadores, admin)
   ======================================== */
CREATE TABLE `usuarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL UNIQUE,
  `telefono` varchar(50) DEFAULT NULL,
  `rol` ENUM('admin','dueno','prestador') NOT NULL DEFAULT 'dueno',
  `password` varchar(255) NOT NULL,
  `provincia_id` int unsigned DEFAULT NULL,
  `localidad_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ========================================
   TABLA: mascotas
   ======================================== */
CREATE TABLE `mascotas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int unsigned NOT NULL,
  `nombre` varchar(120) NOT NULL,
  `especie` varchar(50) DEFAULT NULL,
  `sexo` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ========================================
   TABLA: prestadores
   ======================================== */
CREATE TABLE `prestadores` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int unsigned DEFAULT NULL,
  `nombre` varchar(255) NOT NULL,
  `tipo` varchar(120) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `provincia_id` int unsigned DEFAULT NULL,
  `localidad_id` int unsigned DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `matricula` varchar(120) DEFAULT NULL,
  `descripcion` text,
  `plan` ENUM('free','pro','premium') NOT NULL DEFAULT 'free',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ========================================
   TABLA: prestador_fotos
   ======================================== */
CREATE TABLE `prestador_fotos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `prestador_id` int unsigned NOT NULL,
  `ruta` varchar(255) NOT NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ========================================
   TABLA: servicios
   ======================================== */
CREATE TABLE `servicios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `prestador_id` int unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ========================================
   TABLA: recordatorios
   ======================================== */
CREATE TABLE `recordatorios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `mascota_id` int unsigned NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `fecha_recordatorio` date NOT NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ========================================
   TABLA: suscripciones (email marketing)
   ======================================== */
CREATE TABLE `suscripciones` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(190) NOT NULL UNIQUE,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ========================================
   GEO: provincias, ciudades, localidades
   ======================================== */
CREATE TABLE `provincias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `ciudades` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `provincia_id` int unsigned NOT NULL,
  `nombre` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `localidades` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ciudad_id` int unsigned NOT NULL,
  `nombre` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ========================================
   TABLA: bitacora (logs internos)
   ======================================== */
CREATE TABLE `bitacora` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int unsigned DEFAULT NULL,
  `accion` varchar(255) DEFAULT NULL,
  `detalle` text,
  `created_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/* ========================================
   TABLA: reservas (turnos) – futura expansión
   ======================================== */
CREATE TABLE `reservas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int unsigned NOT NULL,
  `prestador_id` int unsigned NOT NULL,
  `servicio_id` int unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` ENUM('pendiente','confirmado','cancelado') DEFAULT 'pendiente',
  `created_at` timestamp NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


SET FOREIGN_KEY_CHECKS = 1;
