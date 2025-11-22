# Reglas del proyecto Mascotas y Mimos (Codex)

## 1. Archivos que NO se pueden tocar jamás
- public/config/db.php
- public/config/mail.php
- public/config/env.php
- public/.htaccess (solo modificar si yo lo apruebo)

## 2. Reglas para el index
- No modificar public/index.php directamente.
- Cualquier nueva versión se llama: index_vX.php (ejemplo: index_v2.php)

## 3. Ubicación obligatoria del código
- Front y páginas públicas: dentro de public/
- APIs: dentro de public/api/
- Includes: dentro de public/includes/
- Lógica interna/MVC: app/ (Controllers, Models, Views)

## 4. Estándares de programación
- PHP 8+
- PDO obligatorio usando db() en includes/bootstrap.php
- Sanitizado: usar htmlspecialchars siempre en valores mostrados
- Validaciones del lado del servidor obligatorias
- No escribir consultas SQL sin parámetros preparados

## 5. Estándares de diseño
- Usar Tailwind CSS (CDN) para nuevas pantallas
- Diseño mobile-first
- Imágenes optimizadas en public/assets/img/

## 6. Manejo del repositorio
- Cada modificación debe anunciarse antes de ejecutarse
- Debés pedirme autorización antes de tocar cualquier archivo
- Después de cada tarea: commit + push automático a main

## 7. Deploy
- El deploy se hace con .github/workflows/deploy.yml
- Solo subir public/ al servidor
- Nunca subir archivos sensibles
- Nunca subir SQL ni carpetas docs/ al servidor

## 8. Condición global
**No ejecutar ninguna acción sin autorización explícita del usuario.**
