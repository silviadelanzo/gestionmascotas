# gestionmascotas

Sitio simple en PHP/HTML/JS (sin frameworks). El despliegue a producción copia `public/` al docroot (por ejemplo `public_html`) vía cPanel Git (rama `prod`).

## Estructura publicada (public/)

- Páginas PHP en raíz (`index.php`, `login.php`, `registro.php`, etc.)
- `includes/` (bootstrap, helpers, auth)
- `config/` (credenciales del entorno)
- `assets/` (css/js/img)
- `.htaccess`, `robots.txt`, `sitemap.xml`

## Desarrollo local

- Con XAMPP: `http://localhost/gestionmascotas/public`
- Ideal: configurar el DocumentRoot al directorio `.../gestionmascotas/public`

## Configuración en servidor (no se versiona)

En tu hosting, como la app está en `https://mascotasymimos.com/gestionmascotas/public/`, las configs van en:

- `public_html/gestionmascotas/public/config/db.php` (requerido)
- `public_html/gestionmascotas/public/config/mail.php` (si usás envíos de email)

Plantillas:

- `public/config/db.sample.php`
- `public/config/mail.sample.php`

## Publicación

- Trabajá en `main`.
- Para publicar: merge/rebase a `prod` y push; cPanel copia `public/` a `public_html`.
