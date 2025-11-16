# Historial de cambios – 2025-11-14

Este documento consolida el trabajo realizado hasta hoy y define los próximos pasos. Integra y actualiza lo registrado en:
- `docs/Proyecto Mascotas Codex 11-11-2025.md`
- `docs/ChatCodex11_11_2025_v2.md`

## 1) Resumen ejecutivo
- Correos operativos con PHPMailer en local y servidor (sin Composer), cifrado correcto (SMTPS 465 / STARTTLS 587).
- Flujo de suscripción estable: crea tabla si falta, guarda registros, envía correo de agradecimiento, evita duplicados.
- Deploy por GitHub Actions → FTP con secrets; sube solo cambios; excluye configs sensibles.
- Dominio redirige temporalmente a la app en `/gestionmascotas/public/` (luego cambiar DocumentRoot en DC).

## 2) Cambios principales (código)
- `public/test_email.php`: carga `lib/PHPMailer` en vez de Composer.
- `public/test_mail_server.php`: SMTPS (465) y destinatario `contactos@mascotasymimos.com`.
- `public/guardar_suscripcion.php`:
  - Convertido a UTF‑8 (sin BOM) y textos corregidos.
  - SMTP robusto: elige `PHPMailer::ENCRYPTION_SMTPS` + 465 ó `ENCRYPTION_STARTTLS` + 587 según config.
  - Crea tabla si no existe.
  - Normaliza email (`trim` + `strtolower`) y prechequea duplicado antes de insertar.
  - `?debug=1` muestra detalle de error (DB/SMTP) solo para diagnóstico.
- `public/includes/helpers.php`:
  - DSN con puerto opcional (si no hay `port` en `config/db.php`, funciona igual).
- Raíz del proyecto:
  - `index.html` + `.htaccess` de “En construcción”.
  - `htaccess_redirect_to_app.txt` (plantilla de redirección a `/gestionmascotas/public/`).

## 3) Deploy (CI/CD)
- Archivo: `.github/workflows/deploy.yml`.
  - Targets:
    - `app`: despliega `public/` → `public_html/gestionmascotas/public/`.
    - `root`: despliega `index.html` y `.htaccess` → `public_html/`.
  - Inputs (al ejecutar “Run workflow”): `target`, `protocol` (ftp/ftps), `serverDirApp`, `serverDirRoot`.
  - Corre también en push a `main` cuando hay cambios en `public/**` (se puede desactivar si se desea manual).
  - Excluye del deploy (para no pisar credenciales del server):
    - `public/config/db.php`
    - `public/config/mail.php`
  - Sube solo archivos nuevos/modificados (usa `.ftp-deploy-sync-state.json` en el server).

## 4) Dominio y acceso
- Redirección temporal 302 en `public_html/.htaccess` a `/gestionmascotas/public/`.
- Cuando esté todo aprobado, cambiar a 301 (permanente) o pedir al DC:
  - DocumentRoot del dominio → `/home/mascotasmimoos/public_html/gestionmascotas/public`.

## 5) Base de datos y duplicados
- Config server en `public/config/db.php` (no versionado en deploy): host `localhost`, nombre y usuario con prefijo cPanel, permisos ALL PRIVILEGES.
- Tabla `suscripciones` con UNIQUE en `email` (recomendado):
  - `ALTER TABLE suscripciones MODIFY email VARCHAR(190) COLLATE utf8mb4_unicode_ci NOT NULL;`
  - `CREATE UNIQUE INDEX ux_suscripciones_email ON suscripciones (email);`
- Limpieza ejecutada: se removieron duplicados existentes.

## 6) Seguridad y buenas prácticas
- Secrets en GitHub Actions: `FTP_HOST`, `FTP_USERNAME`, `FTP_PASSWORD`.
- No versionar contraseñas en el repo; configs de server excluidas del deploy.
- Opcional: rotar contraseña SMTP si quedó expuesta en históricos.

## 7) Próximas etapas (plan)
1. Responsive (mobile‑first) sin tocar el index actual:
   - Crear `public/index_responsive.php` y ajustar secciones con Tailwind:
     - Contenedor: `max-w-screen-xl mx-auto px-4`.
     - Grillas: `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6`.
     - Evitar `position:absolute` en móvil; aplicar en `md:`.
     - Tipografías fluidas (`clamp`), `leading-relaxed`, imágenes `w-full` + `aspect-*` + `object-cover`.
     - Formularios `w-full` y `min-h-[44px]`.
2. Pruebas locales de responsive:
   - Viewports: 360×640, 390×844, 768×1024, 1024×768.
   - Sin solapes, sin scroll horizontal; botones táctiles ≥ 44px.
3. Subida controlada:
   - Deploy “app” solo del nuevo `index_responsive.php`.
   - Validación en `…/public/index_responsive.php` en server.
   - Si aprueba, definir: reemplazar `index.php` o redirigir temporalmente.
4. Limpieza y hardening:
   - Eliminar `public/test_ftp.txt` y otros archivos de prueba del server.
   - Revisar logs y activar 301 definitivo si corresponde.

## 8) Cómo ejecutar el deploy
1. Verificar que los secrets están cargados en GitHub (repo → Settings → Secrets → Actions).
2. Actions → “Deploy via FTP” → Run workflow.
   - `target=app` (para `public/`) o `target=root` (para portada).
   - `protocol=ftp` (o `ftps` si el server lo permite).
   - `serverDirApp=public_html/gestionmascotas/public/`.
   - `serverDirRoot=public_html/`.
3. Revisar logs; si sube “0 B”, es que no hubo cambios.

## 9) Anexos y referencias
- Estado previo y plan inicial: ver `docs/Proyecto Mascotas Codex 11-11-2025.md`.
- Conversaciones y decisiones: ver `docs/ChatCodex11_11_2025_v2.md`.

## 10) Idea refinada de producto (dueños y prestadores)

### 10.1 Objetivo general
- Ser el “cerebro” de la vida de cada mascota para el dueño (historial, recordatorios, contactos, documentos), y monetizar ofreciendo visibilidad segmentada a prestadores (veterinarios, peluquerías, paseadores, etc.).

### 10.2 Servicios clave para dueños (lado gratuito / enganche)
- Historial médico por mascota:
  - Vacunas, cirugías, alergias, enfermedades crónicas, estudios, peso.
  - Línea de tiempo de eventos importantes (adopción, tratamientos, controles).
- Agenda y recordatorios:
  - Recordatorios por email (y futuro WhatsApp) para vacunas, desparasitaciones, controles anuales, medicamentos.
  - Frecuencias configurables por mascota y por tratamiento.
- Carpeta de documentos:
  - Guardar fotos del carnet, recetas, certificados, chip, papeles de viaje.
  - Pensado para que no dependa de una sola veterinaria o ciudad.
- Contactos importantes:
  - Veterinaria principal, peluquería, paseador, guardería, emergencias 24hs.
  - Botones de acción rápida (llamar, WhatsApp, ver mapa).
- Agenda compartida:
  - Permitir compartir el perfil de la mascota con familia/cuidador para que todos vean recordatorios y datos clave.
- Funciones emocionales:
  - Línea de tiempo de la mascota con fotos e hitos.
  - Cumpleaños y aniversarios con recordatorios y mensajes especiales.

### 10.3 Cómo se ve para el dueño (experiencia)
- “Tablero” por mascota:
  - Vista simple: próximos recordatorios, últimas visitas, contactos favoritos.
  - Acceso rápido a documentos y notas importantes.
- Foco en resolver problemas reales:
  - “No me olvido más de las vacunas”.
  - “Tengo todo a mano si viajo o cambio de veterinaria”.

### 10.4 Servicios para prestadores (monetización)
- Presencia básica gratuita:
  - Ficha con datos mínimos (nombre, ciudad, tipo de servicio).
  - Posibilidad de ser agregado como “contacto de confianza” por los dueños.
- Planes pagos (“Pro”):
  - Mayor visibilidad en listados y búsquedas dentro del sitio.
  - Prioridad en resultados segmentados (ej: “prestadores de La Rioja”).
  - Posibilidad de subir fotos, promociones y textos destacados.
  - Botones de contacto directo (WhatsApp, teléfono, link a agenda externa).
- Promoción segmentada:
  - Ejemplos:
    - Vets de La Rioja se muestran primero a dueños con mascotas en La Rioja.
    - Paseadores que pagan por promoción aparecen a dueños que marcaron “busco paseador”.
- Métricas para prestadores:
  - Cantidad de vistas de su ficha.
  - Contactos/derivaciones generadas desde Mascotas y Mimos (futuro).

### 10.5 Relación entre valor al dueño y negocio
- Cuanto más valor real reciba el dueño (organización, recordatorios, tranquilidad), más tiempo pasará en la plataforma y más datos de contexto existirán.
- Esos datos agregados (siempre respetando privacidad) permiten:
  - Ofrecer a los prestadores acceso a una base de dueños “activos”.
  - Ofrecer publicidad relevante (“no un banner genérico, sino estar delante del dueño correcto en el momento correcto”).

### 10.6 Prioridades para el MVP (lo primero a construir)
1. Historial por mascota + agenda de vacunas/tratamientos + recordatorios por email.
2. Carpeta básica de documentos de la mascota.
3. Gestión de contactos de confianza (vet principal, etc.).
4. Listado simple de prestadores por ciudad (sin planes pagos todavía, solo presencia).
5. Una vista de tablero para el dueño donde vea:
   - Próximos recordatorios.
   - Últimas acciones.
   - Acceso a contactos y documentos.

Con esto, la primera versión ya entrega valor concreto a los dueños y deja preparado el terreno para vender visibilidad y planes Pro a prestadores una vez que exista masa crítica de mascotas registradas.
