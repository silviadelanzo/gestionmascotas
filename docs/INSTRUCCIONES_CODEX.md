# Instrucciones para Codex – Proyecto Mascotas y Mimos

## 1. Objetivo de este documento

Este documento sirve para que cualquier sesión nueva de Codex entienda rápidamente:

- Qué es este proyecto.
- Cómo está organizado el repositorio.
- Qué reglas debe respetar (archivos sensibles, ramas, deploy).
- Cómo reanudar el trabajo si el chat anterior se perdió.

Antes de hacer cambios, **lee este archivo completo** y sigue estas reglas.

---

## 2. Descripción general del proyecto

- Proyecto: **Mascotas y Mimos** – agenda digital para dueños de mascotas y visibilidad para prestadores (veterinarias, paseadores, etc.).
- Tech principal: **PHP** (sin frameworks grandes), HTML, Tailwind CSS (vía CDN), JavaScript y MySQL.
- Emails: **PHPMailer sin Composer**, cargado desde `public/lib/PHPMailer`.
- Deploy: vía **GitHub Actions → FTP** usando `.github/workflows/deploy.yml`.
- Documentación clave:
  - `docs/HISTORIAL_YYYY-MM-DD.md` → histórico de cambios y plan vigente.
  - `docs/Proyecto Mascotas Codex 11-11-2025.md`
  - `docs/ChatCodex11_11_2025_v2.md`

Siempre que necesites contexto funcional o de roadmap, consulta primero el **HISTORIAL más reciente** en `docs/`.

---

## 3. Repositorio y entorno

- Repo GitHub: `silviadelanzo/gestionmascotas`.
- Entorno local típico:
  - Ruta: `D:\xampp\htdocs\gestionmascotas` (Windows + XAMPP).
  - URL local base: `http://localhost/gestionmascotas/public/`.

Al iniciar trabajo en una sesión nueva, da por hecho que el usuario trabaja en ese entorno o uno equivalente.

---

## 4. Ramas y estrategia de trabajo

- `main`: rama estable. Solo debe contener código ya probado y listo para ir a producción.
- Ramas de trabajo:
  - Ejemplo actual: `pruebas/index_responsive`.
  - En general, cualquier rama de trabajo debería llamarse tipo `pruebas/...` o `feature/...`.

Regla para Codex:

1. No trabajar directamente sobre `main` salvo que el usuario lo pida explícitamente.
2. Usar o crear una rama de trabajo (por ejemplo `pruebas/index_responsive`) y hacer allí todos los cambios.
3. Cuando el trabajo esté maduro, proponer un Pull Request desde la rama de trabajo hacia `main`.

---

## 5. Archivos sensibles / solo lectura

Hay archivos y carpetas que no deben modificarse, borrarse ni moverse salvo que el usuario lo pida de forma explícita y consciente:

- Configuración de base de datos y mail:
  - `public/config/db.php`
  - `public/config/mail.php`
  - `config/mail.php`
- Librería de correo:
  - `public/lib/PHPMailer/*`
- Cualquier archivo de configuración del servidor que contenga credenciales o rutas específicas.

En su lugar, se pueden usar archivos de ejemplo o plantillas, por ejemplo:

- `public/config/mail.sample.php`
- Otros `*.sample.php` que existan.

Si necesitas cambiar algo de configuración (DB, SMTP, PHPMailer):

- Propón el código o los cambios en un archivo de ejemplo.
- Explica los pasos para que el usuario lo aplique manualmente.
- No escribas ni sobreescribas archivos con credenciales reales.

---

## 6. Reglas de interacción y idioma

- Idioma: responder siempre en español (español neutro).
- Antes de modificar archivos:
  - Explica brevemente el plan de cambios.
  - Indica exactamente qué archivos piensas tocar.
  - Pregunta:  
    **«¿Confirmás que aplique estos cambios en los archivos X, Y, Z?»**  
    y espera un “sí” explícito del usuario.
- Si el usuario solo quiere análisis/explicación (no cambios), respeta eso y no generes parches ni comandos.

---

## 7. Flujo de trabajo recomendado (Codex + repo + deploy)

### 7.1 Al iniciar una sesión nueva

Indica al usuario que ejecute en su terminal, dentro del repo:

```bash
cd D:\xampp\htdocs\gestionmascotas   # o ruta equivalente
git status
git branch
git log --oneline -5
```

Con esa información:

- Verifica en qué rama está (idealmente una rama de trabajo, no `main`).
- Comprueba si hay cambios sin commitear.
- Ve los últimos commits para entender en qué estado quedó el proyecto.

Si se estuvo trabajando en Codex nube en una rama específica, pide también un:

```bash
git pull
```

para traer lo último de GitHub.

### 7.2 Desarrollo y pruebas

- Hacer cambios en la rama de trabajo (por ejemplo `pruebas/index_responsive`).
- Probar siempre en local, con URLs como:
  - `http://localhost/gestionmascotas/public/index.php`
  - `http://localhost/gestionmascotas/public/index_responsive.php`
  - `http://localhost/gestionmascotas/public/index_v2.php`
- No tocar:
  - `public/index.php` (landing actual) salvo decisión explícita.
  - Rutas `public/api/*`.
  - Scripts de mail/suscripción en producción sin respaldo.

### 7.3 Commit y push

Una vez que los cambios locales funcionan:

```bash
git status
git add <archivos-relevantes>
git commit -m "descripcion corta y clara"
git push origin <nombre-rama>
```

Codex puede sugerir mensajes y el conjunto de archivos a añadir, pero el usuario decide qué commitear.

### 7.4 Deploy a servidor (FTP vía GitHub Actions)

- El deploy se hace con `.github/workflows/deploy.yml`.
- No se deben subir:
  - `public/config/db.php`
  - `public/config/mail.php`
- El workflow tiene dos targets:
  - `app`: despliega `public/` → `public_html/gestionmascotas/public/`.
  - `root`: despliega `index.html` + `.htaccess` → `public_html/`.
- El usuario lanza el workflow desde GitHub Actions con los parámetros:
  - `target`, `protocol`, `serverDirApp`, `serverDirRoot`.

Codex no tiene acceso directo al FTP ni a cPanel, solo genera y ajusta el código en el repo.

---

## 8. Documentos de estado y planificación

El documento de referencia principal es el último:

- `docs/HISTORIAL_YYYY-MM-DD.md`

Contiene:

- Resumen ejecutivo (qué ya funciona).
- Cambios de código recientes.
- Estado del deploy y del dominio.
- Plan de próximas etapas (responsive, nuevas homes, mapas, registro por roles, etc.).

Regla para Codex:

- Antes de proponer nuevas features o refactor grandes, lee el `HISTORIAL_...` más reciente.
- Usa ese historial como guía de prioridades (qué va primero, qué está en prueba, qué solo es idea).

---

## 9. Qué hacer si se perdió el contexto del chat

Si comienzas a ayudar en un chat nuevo (nube o CLI) y no hay contexto previo:

1. Lee este archivo `docs/INSTRUCCIONES_CODEX.md`.
2. Pide al usuario:
   - En qué rama está (`git branch`).
   - Resultado de `git status`.
   - Qué fue lo último que se hizo (puede referirse al último `HISTORIAL_...` o PR).
3. Verifica si hay cambios nuevos en GitHub:
   - Sugiere `git pull` en la rama de trabajo.
4. A partir de ahí, continúa siguiendo las reglas de este documento.

---

Con este documento deberías poder:

- Reanudar trabajo después de un corte de luz o cambio de sesión.
- Mantener el mismo estilo de colaboración entre Codex nube, Codex CLI, el repo local y GitHub.
- Proteger archivos sensibles y el entorno de producción mientras el proyecto sigue creciendo.

