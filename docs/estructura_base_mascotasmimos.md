# Estructura de Base de Datos — Mascotas y Mimos
Versión: 2025-11-20  
Estado: Estructura sincronizada entre servidor y entorno local.

Este documento describe **el propósito real** de cada tabla y columna,  
para que cualquier herramienta (incluyendo Codex) pueda generar código  
correcto sin inventar campos o tablas.

---

# 1) Tabla: usuarios
Usuarios del sistema, incluyendo:
- dueños de mascotas
- prestadores de servicios
- administrador

**Campos principales**
- id — clave primaria
- nombre — nombre completo
- email — único en el sistema
- telefono — para WhatsApp o contacto
- rol — {admin, dueno, prestador}
- password — hash bcrypt
- provincia_id / localidad_id — ubicación
- created_at / updated_at — timestamps

**Notas para Codex**
- Esta es la tabla que se usa para login y registros.
- No existe email_verified_at ni remember_token (no usar).
- El rol determina qué vistas se habilitan.

---

# 2) Tabla: mascotas
Mascotas registradas por un usuario dueño.

Campos:
- id
- usuario_id → relaciona con usuarios
- nombre
- especie (ej: perro, gato)
- fecha_nacimiento
- sexo
- foto (ruta)
- created_at

**Notas**
- Un dueño puede tener varias mascotas.
- Esta tabla se usa para el "dashboard del dueño".

---

# 3) Tabla: prestadores
Prestadores y veterinarias que ofrecen servicios.

Campos:
- id
- usuario_id — vínculo con usuarios (prestador es un usuario con rol prestador)
- nombre_comercial
- tipo — vet, paseador, peluquería, etc.
- matricula — solo para veterinarios
- provincia_id, localidad_id
- direccion
- telefono
- latitud / longitud — para mapa
- plan — free, pro, premium
- activo — 1/0
- created_at

**Notas**
- No confundir con usuarios; el usuario es la cuenta, el prestador es la ficha pública.
- Un usuario prestador puede tener una sola ficha.

---

# 4) Tabla: prestador_fotos
Fotos adicionales del prestador.

Campos:
- id
- prestador_id
- ruta

---

# 5) Tabla: servicios
Servicios ofrecidos por prestadores.

Campos:
- id
- prestador_id
- nombre
- descripcion
- precio

**Notas**
- Se relaciona directo a la ficha del prestador.

---

# 6) Tabla: recordatorios
Recordatorios de la mascota (vacunas, controles, medicación).

Campos:
- id
- mascota_id
- tipo
- fecha
- notificado — 0/1

**Notas**
- Se usarán para enviar emails automáticos (en versión futura).

---

# 7) Tabla: reservas
Relación entre dueños y prestadores.

Campos:
- id
- usuario_id (dueño)
- prestador_id
- servicio_id
- fecha
- estado — {pendiente, confirmada, cancelada}

---

# 8) Tabla: bitacora
Registro interno de acciones (para auditoría).

Campos:
- id
- usuario_id
- accion
- detalle
- created_at

---

# 9) Tabla: suscripciones
Emails reunidos desde la landing pública.

Campos:
- id
- email (único)
- fecha_alta

---

# 10) Tablas de ubicación
Vacías por ahora (se pueden cargar más adelante).

## provincias
- id
- nombre

## ciudades
- id
- provincia_id
- nombre

## localidades
- id
- ciudad_id
- nombre

---

# Consideraciones generales para Codex
- No usar tablas viejas como “usuarios_delete”.
- No usar campos inexistentes (email_verified_at, token, etc.).
- Usar PDO siempre con consultas preparadas.
- Para login → validar email + password (bcrypt).
- Para registro:
  - dueños → usuarios.rol = 'dueno'
  - prestadores → usuarios.rol = 'prestador' + crear fila en prestadores
- No asumir Laravel, es PHP 8 + PDO + Tailwind.

---

# Fin del documento
