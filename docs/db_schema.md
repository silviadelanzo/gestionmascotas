Base de datos `petcare_saas`
============================

Este archivo resume la estructura funcional de la base. Para ver el DDL completo y los datos semilla, revisar `sql/estructura.sql`.

Tablas principales
------------------
- usuarios_delete: usuarios finales (roles admin/profesional/prestador/paciente), estado/verificación, ubicación (provincia/localidad), plan (free/premium/pro), tokens reset/whatsapp, flags de newsletter/publico.
- prestadores: perfil extendido de prestadores (usuario_id -> usuarios_delete), datos de negocio, validación.
- servicios: oferta publicada por prestador (categoría, precio, ubicación geográfica, tipo simple/destacado/pro/premium, duración, visibilidad).
- reservas: vínculo servicio + paciente (usuario) + mascota, estados (pendiente/confirmada/cancelada/completada/no_asistio/reprogramada), horarios y datos de pago.
- mascotas: mascotas de un dueño (dueno_id -> usuarios_delete), especie/raza, datos veterinarios básicos.
- historial_servicios: eventos clínicos/servicio por mascota y reserva, opcional profesional, JSON de archivos adjuntos.
- recordatorios: alertas para usuario/mascota (fecha/hora, mensaje).

Catálogos y geografía
---------------------
- categorias: servicio/producto/especialidad, activo.
- provincias, localidades (FK provincias), ciudades (otra lista por provincias).

Auditoría y sistema
-------------------
- bitacora: tracking de acciones con JSON antes/después, referencia opcional a usuario.
- suscripciones: newsletter/registro temprano.
- sessions: sesiones Laravel.
- jobs, job_batches, failed_jobs, cache, cache_locks, email_verifications, password_reset_tokens, migrations, users, usuarios (versión simplificada).

Relaciones clave (FK)
---------------------
- usuarios_delete 1:N mascotas (cascade).
- usuarios_delete 1:1 prestadores (cascade).
- usuarios_delete 1:N prestador_fotos (cascade).
- servicios -> provincias/localidades (SET NULL), prestador_id (lógico contra usuarios_delete).
- reservas -> servicios (cascade), pacientes usuarios_delete (cascade), mascotas (cascade).
- historial_servicios -> mascotas (cascade), reservas (SET NULL), profesional usuarios_delete (SET NULL).
- recordatorios -> usuarios_delete y mascotas (cascade).
- ciudades/localidades -> provincias (cascade/SET NULL).

Datos semilla relevantes
------------------------
- provincias/localidades/ciudades precargadas.
- categorias iniciales (Veterinaria, Peluquería, Paseos).
- suscripciones de ejemplo en pruebas.

Notas de uso/mantenimiento
--------------------------
- Actualizar `sql/estructura.sql` con cada cambio de esquema.
- Si agregás tablas/campos nuevos, sumar aquí propósito, claves y relaciones.
- Si añadís reglas de negocio (triggers, constraints específicas), documentarlas debajo.
