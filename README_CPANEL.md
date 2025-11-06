# cPanel Git: deploy solo de `public/` (rama `prod`)

Este repositorio está listo para que cPanel haga deploy SOLO de la carpeta `public/` al `public_html` cuando empujás a la rama `prod`.

## 1) Preparar el repo en cPanel
1. cPanel → Git Version Control → Create
2. Clone URL: `https://github.com/silviadelanzo/gestionmascotas.git`
3. Clone path: `~/repositories/gestionmascotas` (o similar)
4. Branch to track: `prod`
5. Enable Automatic Deployment: ON

cPanel clona el repo. Cada push a `prod` ejecuta `.cpanel.yml`.

## 2) ¿Qué hace `.cpanel.yml`?
- Verifica que la rama sea `prod`. Si no, salta el deploy.
- Copia (rsync) el contenido de `public/` → `~/public_html/`
- Excluye `includes/config.php` (ese queda del lado del servidor)

## 3) Configurar `includes/config.php` en el servidor
En `public_html/includes/config.php` (no viaja por Git):

```php
<?php
const APP_URL = 'https://tu-dominio'; // sin barra final
const DB_HOST = '45.143.162.54';
const DB_PORT = 3306;
const DB_NAME = 'mascotasmimoos_app';
const DB_USER = 'mascotasmimoos_app';
const DB_PASS = '(?gygC8wgQr+l^MPO';
const SHOW_DB_WARNING = true; // opcional
const CONTACT_TO = 'no-reply@mascotasymimos.com';
```

> Nota: `public/includes/config.php` está en `.gitignore` para que no se suba por accidente.

## 4) Flujo de trabajo
- Desarrollás en `main` o tu rama.
- Cuando quieras publicar, merge/rebase a `prod` y hacé push.
- cPanel recibe el push y despliega `public/` en `public_html`.

## 5) Diagnóstico rápido
- Si los enlaces apuntan a `localhost`: corregir `APP_URL` en `includes/config.php` del servidor.
- Si `servicios.php` no muestra tipos: asegurate que tu tabla `servicios` tiene `rubro` o `tipo` (el código detecta la disponible).
- Si falla el `.htaccess`: probá una versión mínima con `DirectoryIndex index.php` y `Options -Indexes`.
