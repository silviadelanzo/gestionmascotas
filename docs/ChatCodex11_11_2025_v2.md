# Mascotas y Mimos — Brief funcional y técnico (Actualizado)

Fecha: 2025-11-11

## 1) Idea del proyecto
Plataforma SaaS simple para vincular **dueños** y **prestadores** (veterinarias, peluquería, paseadores, adiestradores). El foco es **registro y verificación por email**, **gestión de mascotas múltiples por usuario**, y **descubrimiento de prestadores en mapa**, visible **solo a usuarios logueados**. Tendrá **landing comercial pública** separada del **app**.

### Objetivos clave
- Registro rápido: email/contraseña o social login (a futuro). Primera versión: **email + verificación**.
- Dueño puede registrar **n** mascotas. Alta guiada: “¿Deseás agregar otra mascota?”.
- Prestador crea perfil con **geolocalización**, horarios, servicios, fotos.
- Mapa de prestadores solo disponible a usuarios verificados.
- Diseño **visual, pastel, iconos redondeados**. Idioma **es-AR**.
- Pensado para **escalamiento SaaS** a futuro (multi-tenant después).

## 2) Flujos principales
### 2.1 Registro y verificación
1. Usuario elige tipo: **Dueño** o **Prestador**.
2. Completa formulario básico y confirma.
3. Se envía email desde **no-responder@mascotasymimos.com** con enlace único.
4. `verificar.php` valida token, activa cuenta y marca `email_verified_at`.
5. Redirección a **login**.

### 2.2 Dueño
- CRUD de mascotas (múltiples). Carga foto, raza, edad, notas.
- Explorar **prestadores** en **mapa** y listado. Solo si está logueado.
- Contacto al prestador (formulario in‑app, a futuro chat).

### 2.3 Prestador
- Completa perfil: nombre comercial, categoría, dirección, **lat/lng**, zona de cobertura, teléfono, web, fotos.
- Visible en mapa y resultados tras verificación.

## 3) Reglas y visibilidad
- La **landing** es pública.
- El **mapa** y el **listado de prestadores** solo se muestran a **usuarios verificados**.
- Los **perfiles de prestador** requieren verificación para ser listados.

## 4) Alcance versión inicial (MVP)
- Landing pública minimal.
- App con páginas: `login`, `registro`, `mis-mascotas`, `prestadores`, `mapa`, `dashboard`.
- Registro con verificación por email (PHPMailer sin composer aceptado).
- Alta de mascotas múltiples.
- Carga y lectura de prestadores (seed inicial o alta manual).
- Mapa con marcadores y link a perfil de prestador.

## 5) Arquitectura y estructura de carpetas
```
/gestionmascotas
  /app
    /Controllers  /Models  /Views
  /config
    config.php         # DB, BASE_URL
    mail.php           # SMTP
  /lib
    Mailer.php         # wrapper PHPMailer
    /PHPMailer         # DSNConfigurator.php, Exception.php, OAuth.php, OAuthTokenProvider.php, PHPMailer.php, POP3.php, SMTP.php, /language
  /public
    /assets/{css,js,img}  /uploads
    index.php login.php registro.php verificar.php
    prestadores.php mapa.php mis-mascotas.php dashboard.php
  /sql
  /vendor (opcional si se usa composer en local)
```

## 6) Modelo de datos (MySQL)
- `users` (id BIGINT UNSIGNED PK, name, email UNIQUE, password, role ENUM('duenio','prestador'), status ENUM('pendiente','activo'), email_verified_at DATETIME NULL, created_at, updated_at).
- `email_verifications` (id PK, user_id FK users.id ON DELETE CASCADE, token VARCHAR(255) UNIQUE, created_at).
- `mascotas` (id PK, user_id FK, nombre, especie, raza, nacimiento DATE NULL, notas TEXT, foto, created_at).
- `prestadores` (id PK, user_id FK, nombre_comercial, categoria, direccion, lat DECIMAL(10,7), lng DECIMAL(10,7), telefono, web, descripcion TEXT, horario TEXT, foto_portada).
- Índices por `users.email`, `prestadores(lat,lng)`.

## 7) Páginas y comportamiento
- `public/registro.php`: formulario tipo + datos básicos. Inserta usuario `status='pendiente'`. Genera token, guarda en `email_verifications` y envía correo.
- `public/verificar.php`: recibe token, activa usuario, marca `email_verified_at`, elimina token, muestra link a login.
- `public/prestadores.php`: si no logueado → redirige a login. Lista + filtros.
- `public/mapa.php`: requiere login. Mapa centrado por región con marcadores de prestadores activos.
- `public/mis-mascotas.php`: CRUD de mascotas del usuario.
- `public/login.php`: login simple (password_hash en próxima iteración).
- `public/index.php`: landing app mínima. **Landing comercial** separada a futuro.

## 8) Email: PHPMailer sin composer
- Ubicación server: `public_html/gestionmascotas/lib/PHPMailer/` + `lib/Mailer.php`.
- Remitente: `no-responder@mascotasymimos.com` (SMTP AUTH).
- Configuración base (server):
  - Host: `mail.mascotasymimos.com`
  - Usuario: `no-responder@mascotasymimos.com`
  - Password: ***provista por el usuario***
  - Seguridad: **SSL 465** o **TLS 587** según cPanel.
  - Autenticación obligatoria.

### Test de correo
`public/test_email.php` usa `Mailer.php` e imprime éxito/error. Activar depuración:
```php
$mail->SMTPDebug = 2;
$mail->Debugoutput = 'html';
```
Errores típicos:
- *No Such User Here*: el destinatario no existe en el servidor.
- *Could not connect to SMTP host*: probar SSL:465 o TLS:587. Revisar firewall/“sólo conexiones locales” en hosting.
- *Invalid address*: validar formato del destinatario.

## 9) Seguridad y privacidad
- Mapa y prestadores solo para usuarios logueados y verificados.
- Contraseñas: migrar a `password_hash()` y `password_verify()`.
- CSRF/token en formularios en siguientes iteraciones.
- Validación y sanitización server‑side.

## 10) Roadmap corto
1. **Correos**: terminar prueba con SSL:465 y TLS:587. Dejar funcionando test en server.
2. **Registro**: bloquear acceso a `prestadores.php` y `mapa.php` si no está logueado o verificado.
3. **CRUD Mascotas**: alta múltiple completa.
4. **Mapa**: cargar prestadores desde DB con lat/lng real.
5. **Hash de contraseñas** y recuperación por email.
6. **Landing** separada con estilo visual pastel.

## 11) Checklist de servidor
- `public_html/gestionmascotas` contiene: `app/`, `config/`, `lib/`, `public/`, `sql/`, `vendor/` (si aplica).
- Permisos: `public/uploads` escribible por PHP (ej. 755 o 775 según host).
- `.env/.config` con credenciales de DB y SMTP.
- PHPMailer copiado en `lib/PHPMailer` y cargado por `lib/Mailer.php`.

## 12) Troubleshooting SMTP
- Si cPanel muestra “SMTP Port: 465 (SSL)”, probar SSL+465 primero.
- Si falla: TLS+587, y `SMTPDebug=2` para ver handshake.
- Verificar que el **remitente** existe y coincide con las credenciales.
- Probar destinatarios internos (otro buzón del mismo dominio) y externos (Gmail).

---

Este documento resume **idea + reglas de negocio + estructura + modelo + flujos + email + próximos pasos** para seguir sin perder contexto.
