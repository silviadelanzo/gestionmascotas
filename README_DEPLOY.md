# Deploy automático de `public/` al servidor

Este repo está preparado para subir solo la carpeta `public/` al servidor cuando hacés push a la rama `prod`.

## 1) Preparar secretos en GitHub

En GitHub: Settings → Secrets and variables → Actions → New repository secret:

- `FTP_HOST`: host del FTP/FTPS/SFTP (ej: `ftp.tu-dominio.com`)
- `FTP_USERNAME`: usuario
- `FTP_PASSWORD`: contraseña
- `FTP_SERVER_DIR`: carpeta destino (docroot, ej: `/public_html/`)
- (opcional) `FTP_PROTOCOL`: `sftp` o `ftps` (recomendado). Si no, queda `ftp`.
- (opcional) `FTP_PORT`: `22` para SFTP, `21` para FTP

## 2) Rama `prod`

- Crear la rama `prod` a partir de lo que quieras publicar.
- Cada push a `prod` dispara el workflow y sube `public/` al server.

## 3) Config del servidor (no se sube por Git)

En el servidor, crear/editar dentro de la carpeta `config/` del despliegue (en tu caso: `public_html/gestionmascotas/public/config/`):

- `public_html/gestionmascotas/public/config/db.php` (requerido)
- `public_html/gestionmascotas/public/config/mail.php` (si usás envíos de email)

Plantillas (en el repo):

- `public/config/db.sample.php`
- `public/config/mail.sample.php`

## 4) Troubleshooting rápido

- Página en blanco / 500: revisar `Errors` en cPanel y probar comentando temporalmente `Options -Indexes` en `public/.htaccess`.
- Error de DB: abrir `public/test_db.php` en el servidor y confirmar credenciales/permiso de MySQL remoto.
- Links a `localhost`: dejar `base_url` vacío en `public/config/env.php` para autodetección (o setearlo al dominio real).
