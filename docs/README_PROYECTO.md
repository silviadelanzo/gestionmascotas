# Mascotas y Mimos — README operativo
Version: 2025-12-04  
Alcance: estado actual, como correr localmente, deploy y pendientes inmediatos.

## 1) Vision rapida
- Que resuelve: agenda digital para familias con mascotas; prestadores ganan visibilidad en listados y recetas PDF futuras.
- Estado: landing actual (`public/index.php`), home extendida de prueba (`public/index_v2_1.php`), nueva variante con video de fondo (`public/index_c2_2.php`), mapas demo con Leaflet (`public/mapa_prestadores*.php`), suscripcion por correo con PHPMailer sin Composer, registro/login basico en `public/registro.php` y `public/login.php`.
- Proximo hito: maquetar `public/index_responsive.php` (mobile-first) y alinear registro con el esquema real de BD.

## 2) Uso local rapido
- Requisitos: PHP 8+, MySQL, extensiones PDO MySQL habilitadas; servidor local tipo XAMPP.
- Config:
  - Copiar/crear `public/config/db.php` y `public/config/mail.php` con credenciales locales (no se versionan).
  - `public/config/env.php` deja `base_url` vacio por defecto; `app_base_url()` autodetecta host/carpeta y evita links a `localhost` en produccion.
- Rutas de prueba:
  - Home actual: `http://localhost/gestionmascotas/public/index.php`
  - Home nueva (prueba): `http://localhost/gestionmascotas/public/index_v2_1.php`
  - Home con video de fondo: `http://localhost/gestionmascotas/public/index_c2_2.php`
  - Registro: `http://localhost/gestionmascotas/public/registro.php`
  - Login: `http://localhost/gestionmascotas/public/login.php`
  - Suscripcion: `http://localhost/gestionmascotas/public/guardar_suscripcion.php`
  - Mapas: `http://localhost/gestionmascotas/public/mapa_prestadores.php` (y variantes `_focus`, `_area`)
- Correo de prueba: `public/test_email.php` y `public/test_mail_server.php` usan `lib/PHPMailer`.

## 3) Arquitectura y codigo
- PHP 8 sin frameworks; Tailwind por CDN; overrides en `assets/css/style_tailwind_overrides.css`.
- Dependencias locales: carpeta `lib/PHPMailer` (sin Composer).
- Helpers: `public/includes/helpers.php` maneja DSN y utilidades; `public/includes/bootstrap.php` inicializa.
- Formulario de registro en `public/registro.php`: valida campos, guarda usuario, intenta email de verificacion (ver seccion 6 para ajustes pendientes).

## 4) Deploy (GitHub Actions + FTP)
- Workflow: `.github/workflows/deploy.yml`.
- Targets:
  - `app`: sube `public/` -> `public_html/gestionmascotas/public/`.
  - `root`: sube portada (`index.html`, `.htaccess`) -> `public_html/`.
- Inputs al ejecutar: `target`, `protocol` (ftp/ftps), `serverDirApp`, `serverDirRoot`.
- Exclusiones para no pisar credenciales: `public/config/db.php`, `public/config/mail.php`.
- Sincroniza solo cambios (usa `.ftp-deploy-sync-state.json` en el servidor).
- Redireccion actual: 302 en `public_html/.htaccess` hacia `/gestionmascotas/public/`; cambiar a 301 o mover DocumentRoot cuando se apruebe.

## 5) Base de datos
- Documento de referencia vigente: `docs/estructura_base_mascotasmimos.md` (version 2025-11-20). Tablas clave: `usuarios`, `mascotas`, `prestadores`, `prestador_fotos`, `servicios`, `recordatorios`, `reservas`, `bitacora`, `suscripciones`, tablas de ubicacion.
- Scripts/respaldos: `docs/petcare_saas-20251120-172441_LOCAL.sql`, `docs/database_mascotasymimos_clean.sql`.
- Conflictos conocidos: `db_schema.md` lista tablas viejas (`usuarios_delete`, `email_verifications`) que no coinciden con el doc vigente. Definir una unica fuente de verdad antes de migrar.

## 6) Estado de funcionalidades (detalle)
- Suscripcion (`public/guardar_suscripcion.php`): crea tabla si falta, normaliza email, evita duplicados, SMTP robusto, `?debug=1` da detalle.
- Registro (`public/registro.php`):
  - Valida nombre/email/password y rol (dueno/prestador); usa `?role=dueno|prestador` para preseleccionar la opcion en el select.
  - Inserta en `usuarios` y genera token en `email_verifications_app` + correo de verificacion.
  - Estilo alineado a login (fondo difuso + tarjeta central).
  - Pendientes: usa columnas no documentadas (`email_verified_at`, `estado`), no guarda telefono/provincia/localidad ni crea fila en `prestadores` cuando el rol es prestador. Definir esquema y tabla de verificaciones o ajustar codigo.
- Login (`public/login.php`):
  - Rediseño completo con fondo difuso, tarjeta, toggle de contrasena; envia a `/api/login.php`.
  - Usa `app_base_url()` para redirecciones/links sin depender de `localhost`, funciona en subcarpetas o dominio final.
- Launchpads tras login:
  - `public/api/login.php`: valida usuario/rol (dueno, prestador, admin) y redirige a `launchpad_dueno.php` o `launchpad_prestador.php`.
  - `public/launchpad_dueno.php`: tarjeta + modal con accesos rápidos (mis mascotas, agregar mascota, recordatorios, documentos, contactos, mapa prestadores).
  - `public/launchpad_prestador.php`: tarjeta + modal con accesos (ficha, fotos, publicar servicios, reservas, recetas PDF, estadísticas).
- Verificacion (`public/verificar.php`):
  - Pagina autonoma sin navbar, mismo estilo que login/registro.
  - Usa `app_base_url()` para enlaces de verificacion/login sin romper en produccion; marca tokens en `email_verifications_app` y setea `estado`/`email_verified_at` en `usuarios` (mismas columnas pendientes de documentar).
- Recupero de contrasena:
  - Solicitud: `public/olvide_password.php` (formulario) -> `public/api/password_forgot.php` genera token y envia email con enlace.
  - Token y expiracion en tabla `password_resets_app` (se crea si falta).
  - Reset: `public/reset_password.php` valida token, permite nueva contrasena (bcrypt) y marca token usado; estilo igual a login/registro.
  - Mensaje siempre generico al pedir el mail (no revela si el usuario existe).
- Home v2 (`public/index_v2.php`):
  - Eliminado el modal de registro duplicado; todos los CTAs (hero, bloque duenos, bloque prestadores) son enlaces directos a `registro.php?role=...`.
  - Se mantiene script ligero que redirige por `data-register-role` para degradar con JS, pero sin formularios embebidos.
- Se conserva la variante `public/index_v2_1.php` (landing extendida) ademas de `public/index_v2.php`.
- Mapas: tres demos con Leaflet y datos hardcodeados para validar UX.

## 7) Roadmap corto
1. Maquetar `public/index_responsive.php` (mobile-first Tailwind) y probar viewports 360-1024 sin scroll horizontal.
2. Alinear registro con la BD definitiva:
   - Campos: telefono, provincia_id, localidad_id.
   - Si rol es prestador, crear fila en `prestadores`.
   - Eliminar/ajustar columnas ausentes y decidir estrategia de verificacion de email (tabla y DDL documentados).
   - Limpiar el fragmento final suelto en `registro.php`.
3. Revisar y limpiar archivos de prueba en servidor (`public/test_ftp.txt`, etc.).
4. Decidir si `index_v2.php` reemplaza o convive; preparar 301 o cambio de DocumentRoot cuando se apruebe.

## 8) Roles y credenciales
- Secrets de deploy en GitHub: `FTP_HOST`, `FTP_USERNAME`, `FTP_PASSWORD`.
- Config sensibles locales (no versionar): `public/config/db.php`, `public/config/mail.php`.
- Contacto/responsable: definir en este bloque quien revisa deploy/BD/correos para flujos de aprobacion.

## 9) Anexos y referencias
- Historial y decisiones: `docs/HISTORIAL_2025-11-14.md`, `docs/Proyecto Mascotas Codex 11-11-2025.md`, `docs/ChatCodex11_11_2025_v2.md`.
- Briefs/plan: `docs/INSTRUCCIONES_CODEX.md`, `docs/NOTAS_CODEX.md`, `docs/DECISIONES_PENDIENTES.md`.
- Plantillas: `htaccess_redirect_to_app.txt`, `index.html` de portada “en construccion”.

## Notas finales
- Elegir y consolidar el esquema (usar `estructura_base_mascotasmimos.md` como base) antes de tocar registro/login o migrar datos.
- Mantener este README actualizado con cada cambio que afecte deploy, BD o endpoints publicos.
