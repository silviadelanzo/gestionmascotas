# gestionmascotas

Sitio simple en PHP/HTML/JS para Gestión Mascotas, sin dependencias de Composer/Node. El despliegue a producción copia solo la carpeta `public/` al `public_html` vía cPanel Git (rama `prod`).

## Estructura publicada (public/)

- `index.php`, `servicios.php`, `prestadores.php`, `contacto.php`
- `includes/` (config y piezas comunes)
  - `config.sample.php` (plantilla)
  - `config.php` (solo en servidor, ignorado por Git)
  - `db.php`, `header.php`, `footer.php`
- `assets/` (css/js/data)
- `.htaccess`, `robots.txt`

Documentación de deploy y cPanel:
- `README_CPANEL.md` → cPanel Git: deploy solo de `public/` en rama `prod`.
- `README_DEPLOY.md` → alternativa por FTP/SFTP con GitHub Actions.

## Desarrollo local

- Con XAMPP, accedé a: `http://localhost/gestionmascotas/public`
- Si usás VirtualHost, apuntá el DocumentRoot a `.../gestionmascotas/public`.

## Configuración en servidor

En `public_html/includes/config.php` (no se versiona):

```php
<?php
const APP_URL = 'https://tu-dominio'; // sin barra final
const DB_HOST = '...';
const DB_PORT = 3306;
const DB_NAME = '...';
const DB_USER = '...';
const DB_PASS = '...';
const CONTACT_TO = '...';
const SHOW_DB_WARNING = false;
```

## Publicación

- Trabajá en `main`.
- Para publicar, merge/rebase a `prod` y hacé push → cPanel copia `public/` a `public_html`.
