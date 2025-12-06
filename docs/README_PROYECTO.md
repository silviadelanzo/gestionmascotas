# Mascotas y Mimos — README operativo
Version: 2025-12-04  
Alcance: estado actual, como correr localmente, deploy y pendientes inmediatos.

## 1) Vision rapida
- Que resuelve: agenda digital para familias con mascotas; prestadores ganan visibilidad en listados y recetas PDF futuras.
- Estado: landing actual (`public/index.php`), home extendida de prueba (`public/index_v2_1.php`), nueva variante con video de fondo (`public/index_c2_2.php`), mapas demo con Leaflet (`public/mapa_prestadores*.php`), suscripcion por correo con PHPMailer sin Composer, registro/login basico en `public/registro.php` y `public/login.php`.
- Proximo hito: maquetar `public/index_responsive.php` (mobile-first) y alinear registro con el esquema real de BD.
- Variante activa en pruebas: `public/index_v2_2.php` (video de fondo, header con Launchpad + icono de cuenta, hero con CTAs de registro).

## 2) Uso local rapido
- Requisitos: PHP 8+, MySQL, extensiones PDO MySQL habilitadas; servidor local tipo XAMPP.
- Config:
  - Copiar/crear `public/config/db.php` y `public/config/mail.php` con credenciales locales (no se versionan).
  - `public/config/env.php` deja `base_url` vacio por defecto; `app_base_url()` autodetecta host/carpeta y evita links a `localhost` en produccion.
- Rutas de prueba:
  - Home actual: `http://localhost/gestionmascotas/public/index.php`
  - Home nueva (prueba): `http://localhost/gestionmascotas/public/index_v2_1.php`
  - Home v2_2 (video, CTA registro): `http://localhost/gestionmascotas/public/index_v2_2.php`
  - Home con video de fondo: `http://localhost/gestionmascotas/public/index_c2_2.php`
  - Registro: `http://localhost/gestionmascotas/public/registro.php`
  - Login: `http://localhost/gestionmascotas/public/login.php`
  - Suscripcion: `http://localhost/gestionmascotas/public/guardar_suscripcion.php`
  - Mapas: `http://localhost/gestionmascotas/public/mapa_prestadores.php` (y variantes `_focus`, `_area`)
- Correo de prueba: `public/test_email.php` y `public/test_mail_server.php` usan `lib/PHPMailer`.

## 3) Arquitectura y codigo
- PHP 8 sin frameworks; Tailwind por CDN; overrides en `assets/css/style_tailwind_overrides.css`.
- Dependencias locales: carpeta `lib/PHPMailer` (sin Composer).
- Helpers: `public/includes/helpers.php` maneja DSN y utilidades; `public/includes/bootstrap.php` inicializa.
- Formulario de registro en `public/registro.php`: valida campos, guarda usuario, intenta email de verificacion (ver seccion 6 para ajustes pendientes).

## 4) Deploy (GitHub Actions + FTP)
- Workflow: `.github/workflows/deploy.yml`.
- Targets:
  - `app`: sube `public/` -> `public_html/gestionmascotas/public/`.
  - `root`: sube portada (`index.html`, `.htaccess`) -> `public_html/`.
- Inputs al ejecutar: `target`, `protocol` (ftp/ftps), `serverDirApp`, `serverDirRoot`.
- Exclusiones para no pisar credenciales: `public/config/db.php`, `public/config/mail.php`.
- Sincroniza solo cambios (usa `.ftp-deploy-sync-state.json` en el servidor).
- Redireccion actual: 302 en `public_html/.htaccess` hacia `/gestionmascotas/public/`; cambiar a 301 o mover DocumentRoot cuando se apruebe.
- Flujo local -> repo -> server:
  - Editar en `D:\xampp\htdocs\gestionmascotas`.
  - Probar local con Apache en `http://localhost/gestionmascotas/public/...` (asegurar que el puerto de Apache esté activo).
  - Commits usando git (se usa MinGit portátil en `%TEMP%\mingit` si no hay git global).
  - `git push origin main` dispara el workflow FTP que sube `public/` al server con los secretos `FTP_*`.
  - Verificar en `https://mascotasymimos.com/gestionmascotas/public/...`.

## 5) Base de datos
- Documento de referencia vigente: `docs/estructura_base_mascotasmimos.md` (version 2025-11-20). Tablas clave: `usuarios`, `mascotas`, `prestadores`, `prestador_fotos`, `servicios`, `recordatorios`, `reservas`, `bitacora`, `suscripciones`, tablas de ubicacion.
- Scripts/respaldos: `docs/petcare_saas-20251120-172441_LOCAL.sql`, `docs/database_mascotasymimos_clean.sql`.
- Conflictos conocidos: `db_schema.md` lista tablas viejas (`usuarios_delete`, `email_verifications`) que no coinciden con el doc vigente. Definir una unica fuente de verdad antes de migrar.

## 6) Estado de funcionalidades (detalle)
- Suscripcion (`public/guardar_suscripcion.php`): crea tabla si falta, normaliza email, evita duplicados, SMTP robusto, `?debug=1` da detalle.
- Registro (`public/registro.php`):
  - Valida nombre/email/password y rol (dueno/prestador); usa `?role=dueno|prestador` para preseleccionar la opcion en el select.
  - Inserta en `usuarios` y genera token en `email_verifications_app` + correo de verificacion.
  - Estilo alineado a login (fondo difuso + tarjeta central).
  - Videos por rol: `dueno_ingresando.mp4` para dueños y `RegistroPrestador.mp4` para prestadores.
  - Si el rol viene en la URL, el selector queda bloqueado; link “Volver al home” en el pie del formulario.
  - Pendientes: usa columnas no documentadas (`email_verified_at`, `estado`), no guarda telefono/provincia/localidad ni crea fila en `prestadores` cuando el rol es prestador. Definir esquema y tabla de verificaciones o ajustar codigo.
- Login (`public/login.php`):
  - Rediseño completo con fondo difuso, tarjeta, toggle de contrasena; envia a `/api/login.php`.
  - Usa `app_base_url()` para redirecciones/links sin depender de `localhost`, funciona en subcarpetas o dominio final.
- Launchpads tras login:
  - `public/api/login.php`: valida usuario/rol (dueno, prestador, admin) y redirige a `launchpad_dueno.php` o `launchpad_prestador.php`.
  - `public/launchpad_dueno.php`: tarjeta + modal con accesos rápidos (mis mascotas, agregar mascota, recordatorios, documentos, contactos, mapa prestadores).
  - `public/launchpad_prestador.php`: tarjeta + modal con accesos (ficha, fotos, publicar servicios, reservas, recetas PDF, estadísticas).
- Verificacion (`public/verificar.php`):
  - Pagina autonoma sin navbar, mismo estilo que login/registro.
  - Usa `app_base_url()` para enlaces de verificacion/login sin romper en produccion; marca tokens en `email_verifications_app` y setea `estado`/`email_verified_at` en `usuarios` (mismas columnas pendientes de documentar).
- Recupero de contrasena:
  - Solicitud: `public/olvide_password.php` (formulario) -> `public/api/password_forgot.php` genera token y envia email con enlace.
  - Token y expiracion en tabla `password_resets_app` (se crea si falta).
  - Reset: `public/reset_password.php` valida token, permite nueva contrasena (bcrypt) y marca token usado; estilo igual a login/registro.
  - Mensaje siempre generico al pedir el mail (no revela si el usuario existe).
- Home v2 (`public/index_v2.php`):
  - Eliminado el modal de registro duplicado; todos los CTAs (hero, bloque duenos, bloque prestadores) son enlaces directos a `registro.php?role=...`.
  - Se mantiene script ligero que redirige por `data-register-role` para degradar con JS, pero sin formularios embebidos.
- Se conserva la variante `public/index_v2_1.php` (landing extendida) ademas de `public/index_v2.php`.
- Mapas: tres demos con Leaflet y datos hardcodeados para validar UX.

## 7) Roadmap corto
1. Maquetar `public/index_responsive.php` (mobile-first Tailwind) y probar viewports 360-1024 sin scroll horizontal.
2. Alinear registro con la BD definitiva:
   - Campos: telefono, provincia_id, localidad_id.
   - Si rol es prestador, crear fila en `prestadores`.
   - Eliminar/ajustar columnas ausentes y decidir estrategia de verificacion de email (tabla y DDL documentados).
   - Limpiar el fragmento final suelto en `registro.php`.
3. Revisar y limpiar archivos de prueba en servidor (`public/test_ftp.txt`, etc.).
4. Decidir si `index_v2.php` reemplaza o convive; preparar 301 o cambio de DocumentRoot cuando se apruebe.

## 8) Roles y credenciales
- Secrets de deploy en GitHub: `FTP_HOST`, `FTP_USERNAME`, `FTP_PASSWORD`.
- Config sensibles locales (no versionar): `public/config/db.php`, `public/config/mail.php`.
- Contacto/responsable: definir en este bloque quien revisa deploy/BD/correos para flujos de aprobacion.
- Login (`public/login.php`):
  - Rediseño completo con fondo difuso, tarjeta, toggle de contrasena; envia a `/api/login.php`.
  - Usa `app_base_url()` para redirecciones/links sin depender de `localhost`, funciona en subcarpetas o dominio final.
- Launchpads tras login:
  - `public/api/login.php`: valida usuario/rol (dueno, prestador, admin) y redirige a `launchpad_dueno.php` o `launchpad_prestador.php`.
  - `public/launchpad_dueno.php`: tarjeta + modal con accesos rápidos (mis mascotas, agregar mascota, recordatorios, documentos, contactos, mapa prestadores).
  - `public/launchpad_prestador.php`: tarjeta + modal con accesos (ficha, fotos, publicar servicios, reservas, recetas PDF, estadísticas).
- Verificacion (`public/verificar.php`):
  - Pagina autonoma sin navbar, mismo estilo que login/registro.
  - Usa `app_base_url()` para enlaces de verificacion/login sin romper en produccion; marca tokens en `email_verifications_app` y setea `estado`/`email_verified_at` en `usuarios` (mismas columnas pendientes de documentar).
- Recupero de contrasena:
  - Solicitud: `public/olvide_password.php` (formulario) -> `public/api/password_forgot.php` genera token y envia email con enlace.
  - Token y expiracion en tabla `password_resets_app` (se crea si falta).
  - Reset: `public/reset_password.php` valida token, permite nueva contrasena (bcrypt) y marca token usado; estilo igual a login/registro.
  - Mensaje siempre generico al pedir el mail (no revela si el usuario existe).
- Home v2 (`public/index_v2.php`):
  - Eliminado el modal de registro duplicado; todos los CTAs (hero, bloque duenos, bloque prestadores) son enlaces directos a `registro.php?role=...`.
  - Se mantiene script ligero que redirige por `data-register-role` para degradar con JS, pero sin formularios embebidos.
- Se conserva la variante `public/index_v2_1.php` (landing extendida) ademas de `public/index_v2.php`.
- Mapas: tres demos con Leaflet y datos hardcodeados para validar UX.

## 7) Roadmap corto
1. Maquetar `public/index_responsive.php` (mobile-first Tailwind) y probar viewports 360-1024 sin scroll horizontal.
2. Alinear registro con la BD definitiva:
   - Campos: telefono, provincia_id, localidad_id.
   - Si rol es prestador, crear fila en `prestadores`.
   - Eliminar/ajustar columnas ausentes y decidir estrategia de verificacion de email (tabla y DDL documentados).
   - Limpiar el fragmento final suelto en `registro.php`.
3. Revisar y limpiar archivos de prueba en servidor (`public/test_ftp.txt`, etc.).
4. Decidir si `index_v2.php` reemplaza o convive; preparar 301 o cambio de DocumentRoot cuando se apruebe.

## 8) Roles y credenciales
- Secrets de deploy en GitHub: `FTP_HOST`, `FTP_USERNAME`, `FTP_PASSWORD`.
- Config sensibles locales (no versionar): `public/config/db.php`, `public/config/mail.php`.
- Contacto/responsable: definir en este bloque quien revisa deploy/BD/correos para flujos de aprobacion.

## 9) Anexos y referencias
- Historial y decisiones: `docs/HISTORIAL_2025-11-14.md`, `docs/Proyecto Mascotas Codex 11-11-2025.md`, `docs/ChatCodex11_11_2025_v2.md`.
- Briefs/plan: `docs/INSTRUCCIONES_CODEX.md`, `docs/NOTAS_CODEX.md`, `docs/DECISIONES_PENDIENTES.md`.
- Plantillas: `htaccess_redirect_to_app.txt`, `index.html` de portada “en construccion”.

## Notas finales
- Elegir y consolidar el esquema (usar `estructura_base_mascotasmimos.md` como base) antes de tocar registro/login o migrar datos.
- Mantener este README actualizado con cada cambio que afecte deploy, BD o endpoints publicos.

---

## 10) Actualizaciones 06/12/2025 - Landing Pages v2.3-v2.5 y Sistema de Navegación

### 10.1) Landing Pages Iterativas (v2.3 → v2.4 → v2.5)

#### **index_v2_3.php** (Mejoras visuales y reducción de redundancias)
- **Ubicación**: `public/index_v2_3.php`
- **Base**: Copia de `index_v2_2.php` con mejoras
- **Cambios implementados**:
  1. **Eliminación de botones redundantes**:
     - Removido botón "Ingresar a mi cuenta" duplicado del hero (se mantiene solo en header)
     - Solo un punto de ingreso en toda la página
  2. **Mejora de visibilidad del video**:
     - Reducción de opacidad del overlay de `0.5/0.4` a `0.3/0.25` en CSS
     - Video de fondo `FondoBosque.mp4` más visible
  3. **Imágenes coloridas agregadas**:
     - Sección "Dueños": 4 imágenes ilustrativas (recordatorio_mail, agenda_vacunas, veterinario_consultorio, mapa_prestadores)
     - Sección "Prestadores": 3 imágenes (veterinario, hero, beneficio1)
     - Formato WebP con fallback a PNG/JPG
  4. **Header centrado**: Ajustes de padding y flexbox para mejor balance visual

#### **index_v2_4.php** (Refinamiento UX y menú mejorado)
- **Ubicación**: `public/index_v2_4.php`
- **Base**: Copia de `index_v2_3.php` con refinamientos
- **Cambios implementados**:
  1. **Eliminación de botones "Crear cuenta" redundantes**:
     - Removido "Crear cuenta de dueño" del header de sección Dueños
     - Removido "Crear cuenta de prestador" del header de sección Prestadores
     - Únicos botones de registro quedan en hero central
  2. **Centrado de títulos en tarjetas**:
     - Clase `text-center` agregada a todas las tarjetas de características
     - Afecta secciones Dueños, Prestadores y Cómo funciona
  3. **Menú de navegación mejorado**:
     - Tamaño de fuente aumentado de `text-sm` (0.875rem) a `1rem`
     - Clase `.nav-link` con padding `0.5rem 1rem`
     - Peso de fuente `500` (medium)
     - Hover effect con fondo `rgba(255, 255, 255, 0.1)`
     - Transiciones suaves de 0.2s
  4. **Headers de secciones centrados**:
     - Layout cambiado de flex (izquierda/derecha) a `text-center`
     - Badges, títulos y descripciones todos centrados

#### **index_v2_5.php** (Ocultamiento de URLs en hover)
- **Ubicación**: `public/index_v2_5.php`
- **Base**: Copia de `index_v2_4.php` con sistema anti-URLs
- **Cambios implementados**:
  1. **Conversión de `href` a `data-href`**:
     - TODOS los botones/links usan `data-href` en lugar de `href`
     - Afecta: Launchpad, Ingresar, Crear cuenta, Logout, Mi perfil
     - Resultado: **NO se muestra URL** en barra de estado al hacer hover
  2. **Links de navegación con scroll suave**:
     - Menú (Dueños, Prestadores, Cómo funciona) usa `data-scroll` en lugar de `href="#..."`
     - JavaScript implementa `scrollIntoView({ behavior: "smooth" })`
     - **NO muestra URLs** al hacer hover
  3. **CSS agregado**:
     ```css
     a[data-href], a[data-scroll] { cursor: pointer; }
     ```
  4. **JavaScript handlers**:
     ```javascript
     // Handler para data-href (navegación externa)
     document.querySelectorAll("a[data-href]").forEach(function(link) {
       link.addEventListener("click", function(e) {
         e.preventDefault();
         window.location.href = link.getAttribute("data-href");
       });
     });
     
     // Handler para data-scroll (navegación interna)
     document.querySelectorAll("a[data-scroll]").forEach(function(link) {
       link.addEventListener("click", function(e) {
         e.preventDefault();
         const targetId = link.getAttribute("data-scroll");
         const targetElement = document.getElementById(targetId);
         if (targetElement) {
           targetElement.scrollIntoView({ behavior: "smooth", block: "start" });
         }
       });
     });
     ```

### 10.2) Sistema de Menú de Usuario con Dropdown

#### **Ubicación**: Implementado en `index_v2_4.php` y `index_v2_5.php`

#### **Componentes**:

1. **Avatar circular** (header, solo cuando usuario está logueado):
   - Tamaño: 2.5rem (40px)
   - Gradiente: `linear-gradient(135deg, var(--brand), #c78867)`
   - Ícono SVG de usuario en blanco
   - Borde: 2px, transparencia 20%, hover al 40%
   - Efecto hover: `transform: scale(1.05)`

2. **Dropdown menu** (aparece al hover sobre avatar):
   - Posición: `absolute`, alineado a la derecha
   - Fondo: `rgba(24, 18, 16, 0.95)` con `backdrop-filter: blur(12px)`
   - Animación: fade + translateY(-10px) → translateY(0)
   - Contenido:
     - **Saludo**: "Hola, [Nombre del Usuario]"
     - **Mi perfil**: Link al launchpad correspondiente (dueño/prestador)
     - **Divisor visual**
     - **Cerrar sesión**: Logout con redirección automática

3. **CSS del dropdown**:
   ```css
   .user-menu {
     position: relative;
   }
   .dropdown {
     position: absolute;
     top: calc(100% + 0.5rem);
     right: 0;
     background: rgba(24, 18, 16, 0.95);
     backdrop-filter: blur(12px);
     border: 1px solid rgba(255, 255, 255, 0.1);
     border-radius: 1rem;
     padding: 0.75rem;
     min-width: 200px;
     opacity: 0;
     visibility: hidden;
     transform: translateY(-10px);
     transition: all 0.2s ease;
     z-index: 100;
   }
   .user-menu:hover .dropdown {
     opacity: 1;
     visibility: visible;
     transform: translateY(0);
   }
   ```

4. **Lógica condicional**:
   - **Usuario NO logueado**: Muestra botón "Ingresar a mi cuenta"
   - **Usuario SÍ logueado**: Muestra botón "Launchpad" + Avatar con dropdown

### 10.3) Sistema de Logout

#### **Endpoint**: `public/api/logout.php`

**Archivo NUEVO creado** con la siguiente funcionalidad:

```php
<?php
require __DIR__ . '/../includes/bootstrap.php';

// Destruir sesión
session_destroy();

// Redirigir al home actual
header('Location: ' . home_url());
exit;
```

**Características**:
- Destruye completamente la sesión del usuario
- Usa función `home_url()` para redirigir a la landing actual
- Sin dependencias de rutas hardcodeadas
- Funciona en local y producción sin cambios

### 10.4) Sistema Automático de Detección de Versión de Home

#### **Ubicación**: `public/includes/helpers.php`

#### **Función agregada**: `home_url()`

**Objetivo**: **NUNCA** tener que actualizar manualmente los links cuando se crea una nueva versión del landing page.

**Implementación**:
```php
/**
 * Devuelve la URL completa a la landing page actual.
 * Detecta automáticamente la versión más reciente de index_v2_*.php
 */
function home_url(): string {
  static $cachedUrl = null;
  
  if ($cachedUrl !== null) {
    return $cachedUrl;
  }
  
  $publicDir = __DIR__ . '/..';
  $versions = [];
  
  // Buscar todos los archivos index_v2_*.php
  foreach (glob($publicDir . '/index_v2_*.php') as $file) {
    if (preg_match('/index_v2_(\d+)\.php$/', basename($file), $matches)) {
      $versions[] = (int)$matches[1];
    }
  }
  
  if (empty($versions)) {
    // Fallback si no hay versiones
    return $cachedUrl = app_base_url() . '/index.php';
  }
  
  // Obtener la versión más alta
  $latestVersion = max($versions);
  
  return $cachedUrl = app_base_url() . '/index_v2_' . $latestVersion . '.php';
}
```

**Funcionamiento**:
1. Escanea carpeta `public/` buscando archivos `index_v2_*.php`
2. Extrae los números de versión (3, 4, 5, etc.)
3. Retorna la URL con el número **MÁS ALTO** encontrado
4. Usa cache estático para no recalcular en cada llamada
5. Fallback a `index.php` si no encuentra versiones

**Archivos que usan `home_url()`**:
- `public/registro.php` - Link "Volver al home"
- `public/login.php` - Link "Volver al home"  
- `public/api/logout.php` - Redirección después del logout

**Ventajas**:
- ✅ **Cero mantenimiento**: Crear `index_v2_6.php` automáticamente actualiza TODOS los links
- ✅ **Sin errores**: Imposible olvidar actualizar algún archivo
- ✅ **Performance**: Cache estático, se calcula solo una vez por request
- ✅ **Robusto**: Funciona aunque se borren versiones intermedias

### 10.5) Actualización de Páginas de Autenticación

#### **login.php** (Agregado link al home)
- **Ubicación**: `public/login.php`
- **Cambios**:
  1. Link "← Volver al home" agregado usando `data-href` + `home_url()`
  2. CSS para `a[data-href]` agregado
  3. JavaScript handler para navegación sin mostrar URLs
  
#### **registro.php** (Actualizado a usar home_url())
- **Ubicación**: `public/registro.php`
- **Cambios**:
  1. Variable `$homeUrl` cambiada de hardcoded a `home_url()`
  2. Link "Volver al home" usa `data-href` para ocultar URL
  3. JavaScript handler ya estaba implementado desde commit anterior

### 10.6) Badges "Para dueños" y "Para prestadores" Mejorados

#### **Ubicación**: CSS en `index_v2_4.php` y `index_v2_5.php`

**Cambios en `.badge`**:
- **Antes**: 
  - Tamaño: 0.75rem (pequeño)
  - Color: Marrón sobre fondo semi-transparente
  - Peso: 600
  - Padding: 0.25rem 0.75rem

- **Ahora**:
  - Tamaño: 0.9rem (20% más grande)
  - Color: Blanco sobre gradiente marrón vibrante
  - Peso: 700 (bold)
  - Padding: 0.5rem 1.25rem
  - Sombra: `box-shadow: 0 4px 12px rgba(169, 113, 85, 0.3)`
  - Letter-spacing: 0.08em (más espaciado)

**CSS actualizado**:
```css
.badge {
  display: inline-block;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #fff;
  background: linear-gradient(135deg, var(--brand), #c78867);
  padding: 0.5rem 1.25rem;
  border-radius: 999px;
  font-weight: 700;
  box-shadow: 0 4px 12px rgba(169, 113, 85, 0.3);
}
```

### 10.7) Commits y Deploy

**Todos los cambios fueron comiteados y desplegados automáticamente via GitHub Actions:**

| Commit | Descripción | Archivos |
|--------|-------------|----------|
| `af6bfa5` | feat: nueva landing v2.3 con imágenes coloridas y sin redundancias | `index_v2_3.php` |
| `0017527` | feat: landing v2.4 - títulos centrados, menú más grande, sin botones redundantes | `index_v2_4.php` |
| `8515ce9` | style: badges más visibles en secciones Dueños y Prestadores | `index_v2_4.php` |
| `5f8e39f` | feat: menú de usuario con dropdown, perfil y logout | `index_v2_4.php`, `api/logout.php` |
| `4d0eb23` | fix: registro apunta a index_v2_4 y oculta URLs en hover | `registro.php` |
| `b557e0e` | fix: JavaScript para navegación con data-href en registro | `registro.php` |
| `99b1fe0` | feat: ocultar URLs en hover en index_v2_4 | `index_v2_4.php` |
| `cec3259` | feat: index v2.5 con data-href en todos los links | `index_v2_5.php` |
| `742ec88` | feat: login con link a home v2.5 y urls ocultas | `login.php`, `api/logout.php` |
| `92ec1ae` | feat: agregar home_url() helper para centralizar versión del landing | `helpers.php`, `registro.php`, `login.php`, `logout.php` |
| `60a7e67` | feat: home_url() detecta automáticamente la versión más reciente | `helpers.php` |
| `99d2bcb` | feat: ocultar URLs del menú de navegación con scroll suave | `index_v2_5.php` |

### 10.8) URLs de Prueba Actualizadas

**Local**:
- Landing actual (automático): `http://localhost/gestionmascotas/public/` → redirige a v2.5
- Landing v2.3: `http://localhost/gestionmascotas/public/index_v2_3.php`
- Landing v2.4: `http://localhost/gestionmascotas/public/index_v2_4.php`
- Landing v2.5: `http://localhost/gestionmascotas/public/index_v2_5.php`
- Registro dueño: `http://localhost/gestionmascotas/public/registro.php?role=dueno`
- Registro prestador: `http://localhost/gestionmascotas/public/registro.php?role=prestador`
- Login: `http://localhost/gestionmascotas/public/login.php`
- Logout: `http://localhost/gestionmascotas/public/api/logout.php`

**Producción**:
- Landing actual: `https://mascotasymimos.com/gestionmascotas/public/index_v2_5.php`
- Registro: `https://mascotasymimos.com/gestionmascotas/public/registro.php?role=dueno`
- Login: `https://mascotasymimos.com/gestionmascotas/public/login.php`

### 10.9) Roadmap Inmediato Post-06/12/2025

1. **Decidir versión final del landing**:
   - `index_v2_5.php` es la más completa actualmente
   - Considerar hacer que `index.php` redirija a `index_v2_5.php` o viceversa
   - Actualizar `.htaccess` para servir la versión definitiva

2. **Siguiente versión (v2.6 o superior)**:
   - **NO requiere cambios manuales** - `home_url()` detecta automáticamente
   - Solo crear el archivo `index_v2_6.php` y listo

3. **Optimizaciones posibles**:
   - Minificar CSS inline en landing pages
   - Lazy loading de imágenes
   - Optimizar video de fondo (compresión, tamaños)
   - Progressive Web App (PWA) manifest

### 10.10) Notas Técnicas Importantes

#### **Sistema de URLs Ocultas**
- **Técnica**: Uso de `data-href` en lugar de `href`
- **JavaScript**: Event listener que intercepta clicks y hace `window.location.href`
- **CSS**: `cursor: pointer` para mantener UX
- **Ventaja**: Barra de estado del navegador **NO muestra URLs** al hacer hover
- **Compatibilidad**: Todos los navegadores modernos

#### **Scroll Suave**
- **Técnica**: `data-scroll` + `scrollIntoView({ behavior: "smooth" })`
- **Reemplaza**: Enlaces tradicionales con `href="#seccion"`
- **Ventaja adicional**: También oculta URLs del menú de navegación

#### **Cache Estático en PHP**
- `home_url()` usa `static $cachedUrl` para evitar múltiples llamadas a `glob()`
- Primera llamada: busca archivos y cachea resultado
- Llamadas siguientes: retorna valor cacheado instantáneamente

#### **Autodetección de Versión**
- Usa `glob()` para buscar archivos que coincidan con patrón `index_v2_*.php`
- Extrae números con regex: `/index_v2_(\d+)\.php$/`
- `max($versions)` garantiza siempre la versión más alta
- Funciona aunque se borren versiones intermedias (ej: si solo existen v2.3 y v2.5, usa v2.5)

### 10.11) Troubleshooting

**Problema**: Links no funcionan después de actualizar
- **Causa**: JavaScript no cargó o hay error en consola
- **Solución**: Verificar consola del navegador (F12), revisar que scripts estén antes de `</body>`

**Problema**: `home_url()` no encuentra la versión correcta
- **Causa**: Permisos de lectura en carpeta `public/`
- **Solución**: Verificar permisos, comprobar que archivos `index_v2_*.php` existan

**Problema**: Logout no funciona
- **Causa**: Sesión no se destruye correctamente
- **Solución**: Verificar que `session_destroy()` se ejecute en `api/logout.php`

**Problema**: Badges no se ven mejorados
- **Causa**: CSS no aplicado, caché del navegador
- **Solución**: Hard refresh (Ctrl+F5), limpiar caché, verificar archivo correcto (v2.4+)
