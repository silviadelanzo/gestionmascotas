# Gestion Mascotas (reinicio)

Proyecto reiniciado desde cero con una estructura simple de PHP sin dependencias.

## Estructura

- public/ (raíz web)
  - index.php (router y front controller)
  - .htaccess (rewrite a index.php)
  - css/style.css
- app/
  - Controllers/
  - Models/
  - Views/
    - layout.php
    - home.php
- config/
  - app.php
  - database.php
- bootstrap.php (autoload + helpers + carga de .env)
- .env.example (variables de entorno)

## Puesta en marcha

1. Copia el archivo `.env.example` como `.env` y ajusta los valores.
2. Asegúrate de que Apache sirva la carpeta `public/`.
   - Opción A (recomendada): Configura un alias o VirtualHost para que `DocumentRoot` apunte a `d:/xampp/htdocs/gestionmascotas/public`.
   - Opción B: Accede vía `http://localhost/gestionmascotas/public` (menos limpio).
3. Abre en el navegador la URL configurada. Deberías ver la página de inicio.

## Requisitos

- PHP 8.x (incluido en XAMPP recientes)
- MySQL/MariaDB si usarás base de datos (opcional por ahora)

## Siguientes pasos (opcionales)

- Añadir CRUD de Mascotas con MySQL (controlador, vistas y tabla).
- Integrar Composer para autoload PSR-4 (si lo prefieres).
- Manejo de sesiones/Login.
