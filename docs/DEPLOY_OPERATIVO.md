# Deploy operativo (GitHub → FTP → cPanel)

Este proyecto se publica así:

1) Vos (o el asistente) hace cambios en el repo local.
2) Se hace `git push` al repo de GitHub.
3) GitHub Actions ejecuta el workflow `.github/workflows/deploy.yml` y sube por FTP al servidor.

## Requisitos (una sola vez)

- Tener Git instalado y el repo clonado.
- Tener el repo en GitHub (ideal: privado).
- Tener configurados los secretos del workflow en GitHub:
  - `FTP_HOST`
  - `FTP_USERNAME`
  - `FTP_PASSWORD`

Importante: **no pegar credenciales en chats**. Si se filtran, hay que cambiarlas.

## Publicar cambios (rápido)

Desde la carpeta del proyecto:

1) `git add -A`
2) `git commit -m "deploy: cambios"`
3) `git push origin main`

Luego en GitHub:

- Tab “Actions” → workflow “Deploy via FTP” → verificar que el último run termine en verde.

## Si `git push` pide login

GitHub ya no permite contraseña de la cuenta para `git push`. Usar una de estas opciones:

- **GitHub Desktop** (recomendado): hace login una vez y después “Push” es un botón.
- **Personal Access Token (PAT)**:
  1) GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic) → Generate.
  2) Scope mínimo: `repo`.
  3) Cuando Git pida contraseña, pegar el token.

Una vez autenticado, el asistente puede ejecutar `git push` desde tu PC si vos lo permitís.

## Qué se sube al servidor

- `public/` → `public_html/gestionmascotas/public/`
- `lib/PHPMailer/` → `public_html/gestionmascotas/lib/PHPMailer/`

## Configuración en el servidor (NO se versiona)

Crear/editar:

- `public_html/gestionmascotas/public/config/db.php`
- `public_html/gestionmascotas/public/config/mail.php` (si se envían emails)

Plantillas en el repo:

- `public/config/db.sample.php`
- `public/config/mail.sample.php`

## Pruebas rápidas post-deploy

- DB: `https://mascotasymimos.com/gestionmascotas/public/test_db.php`
- Home: `https://mascotasymimos.com/gestionmascotas/public/index_v2_6.php`

