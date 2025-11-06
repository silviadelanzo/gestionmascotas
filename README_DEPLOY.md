# Deploy automático de `public/` al servidor

Este repo está preparado para subir SOLO la carpeta `public/` al servidor cuando hacés push a la rama `prod`.

## 1) Preparar secretos en GitHub
En GitHub: Settings → Secrets and variables → Actions → New repository secret:

- `FTP_HOST` → host del FTP/FTPS/SFTP (ej: ftp.tu-dominio.com o la IP)
- `FTP_USERNAME` → usuario
- `FTP_PASSWORD` → contraseña
- `FTP_SERVER_DIR` → carpeta de destino en el server (docroot, ej: `/public_html/`)
- (opcional) `FTP_PROTOCOL` → `sftp` o `ftps` (recomendado). Si no, queda `ftp`.
- (opcional) `FTP_PORT` → `22` para SFTP, `21` para FTP

## 2) Rama `prod`
- Creá la rama `prod` a partir de lo que quieras publicar.
- Cada push a `prod` dispara el workflow `.github/workflows/deploy-prod.yml` y sube los archivos de `public/` al server.

## 3) Config del servidor (no se sube por Git)
- En el servidor, editar `includes/config.php` con:
  - `APP_URL` → `https://tu-dominio` (sin barra final)
  - `DB_*` con las credenciales del host
- Este archivo está ignorado por Git: `public/includes/config.php`
- Se provee `public/includes/config.sample.php` como plantilla.

## 4) Estructura publicada
Se suben estos contenidos (relativo a `public/`):

- `index.php`, `servicios.php`, `prestadores.php`, `contacto.php`
- `includes/` (excepto `config.php` que queda en el server)
- `assets/` (css/js/data)
- `.htaccess`, `robots.txt`

## 5) Uso diario
- Desarrollás en `main` (o tu rama de trabajo).
- Cuando quieras publicar, merge/rebase a `prod` y hacé push → el deploy corre solo.
- Si el host no soporta FTPS/SFTP, contactá al proveedor para habilitar uno seguro.

## 6) Troubleshooting
- Links apuntan a `localhost` → revisar `APP_URL` en `includes/config.php` del server.
- 500 Internal Server Error → probar versión mínima del `.htaccess` (solo `DirectoryIndex index.php` y `Options -Indexes`).
- No aparecen cambios → verificá que `FTP_SERVER_DIR` sea el docroot real del dominio.
