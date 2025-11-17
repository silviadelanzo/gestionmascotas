# Historial de cambios – 2025-11-14

Este documento consolida el trabajo realizado hasta hoy y define los próximos pasos. Integra y actualiza lo registrado en:
- `docs/Proyecto Mascotas Codex 11-11-2025.md`
- `docs/ChatCodex11_11_2025_v2.md`

## 1) Resumen ejecutivo
- Correos operativos con PHPMailer en local y servidor (sin Composer), cifrado correcto (SMTPS 465 / STARTTLS 587).
- Flujo de suscripción estable: crea tabla si falta, guarda registros, envía correo de agradecimiento, evita duplicados.
- Deploy por GitHub Actions → FTP con secrets; sube solo cambios; excluye configs sensibles.
- Dominio redirige temporalmente a la app en `/gestionmascotas/public/` (luego cambiar DocumentRoot en DC).

## 2) Cambios principales (código)
- `public/test_email.php`: carga `lib/PHPMailer` en vez de Composer.
- `public/test_mail_server.php`: SMTPS (465) y destinatario `contactos@mascotasymimos.com`.
- `public/guardar_suscripcion.php`:
  - Convertido a UTF‑8 (sin BOM) y textos corregidos.
  - SMTP robusto: elige `PHPMailer::ENCRYPTION_SMTPS` + 465 ó `ENCRYPTION_STARTTLS` + 587 según config.
  - Crea tabla si no existe.
  - Normaliza email (`trim` + `strtolower`) y prechequea duplicado antes de insertar.
  - `?debug=1` muestra detalle de error (DB/SMTP) solo para diagnóstico.
- `public/includes/helpers.php`:
  - DSN con puerto opcional (si no hay `port` en `config/db.php`, funciona igual).
- Raíz del proyecto:
  - `index.html` + `.htaccess` de “En construcción”.
  - `htaccess_redirect_to_app.txt` (plantilla de redirección a `/gestionmascotas/public/`).

## 3) Deploy (CI/CD)
- Archivo: `.github/workflows/deploy.yml`.
  - Targets:
    - `app`: despliega `public/` → `public_html/gestionmascotas/public/`.
    - `root`: despliega `index.html` y `.htaccess` → `public_html/`.
  - Inputs (al ejecutar “Run workflow”): `target`, `protocol` (ftp/ftps), `serverDirApp`, `serverDirRoot`.
  - Corre también en push a `main` cuando hay cambios en `public/**` (se puede desactivar si se desea manual).
  - Excluye del deploy (para no pisar credenciales del server):
    - `public/config/db.php`
    - `public/config/mail.php`
  - Sube solo archivos nuevos/modificados (usa `.ftp-deploy-sync-state.json` en el server).

## 4) Dominio y acceso
- Redirección temporal 302 en `public_html/.htaccess` a `/gestionmascotas/public/`.
- Cuando esté todo aprobado, cambiar a 301 (permanente) o pedir al DC:
  - DocumentRoot del dominio → `/home/mascotasmimoos/public_html/gestionmascotas/public`.

## 5) Base de datos y duplicados
- Config server en `public/config/db.php` (no versionado en deploy): host `localhost`, nombre y usuario con prefijo cPanel, permisos ALL PRIVILEGES.
- Tabla `suscripciones` con UNIQUE en `email` (recomendado):
  - `ALTER TABLE suscripciones MODIFY email VARCHAR(190) COLLATE utf8mb4_unicode_ci NOT NULL;`
  - `CREATE UNIQUE INDEX ux_suscripciones_email ON suscripciones (email);`
- Limpieza ejecutada: se removieron duplicados existentes.

## 6) Seguridad y buenas prácticas
- Secrets en GitHub Actions: `FTP_HOST`, `FTP_USERNAME`, `FTP_PASSWORD`.
- No versionar contraseñas en el repo; configs de server excluidas del deploy.
- Opcional: rotar contraseña SMTP si quedó expuesta en históricos.

## 7) Próximas etapas (plan)
1. Responsive (mobile‑first) sin tocar el index actual:
   - Crear `public/index_responsive.php` y ajustar secciones con Tailwind:
     - Contenedor: `max-w-screen-xl mx-auto px-4`.
     - Grillas: `grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6`.
     - Evitar `position:absolute` en móvil; aplicar en `md:`.
     - Tipografías fluidas (`clamp`), `leading-relaxed`, imágenes `w-full` + `aspect-*` + `object-cover`.
     - Formularios `w-full` y `min-h-[44px]`.
2. Pruebas locales de responsive:
   - Viewports: 360×640, 390×844, 768×1024, 1024×768.
   - Sin solapes, sin scroll horizontal; botones táctiles ≥ 44px.
3. Subida controlada:
   - Deploy “app” solo del nuevo `index_responsive.php`.
   - Validación en `…/public/index_responsive.php` en server.
   - Si aprueba, definir: reemplazar `index.php` o redirigir temporalmente.
4. Limpieza y hardening:
   - Eliminar `public/test_ftp.txt` y otros archivos de prueba del server.
   - Revisar logs y activar 301 definitivo si corresponde.

## 8) Cómo ejecutar el deploy
1. Verificar que los secrets están cargados en GitHub (repo → Settings → Secrets → Actions).
2. Actions → “Deploy via FTP” → Run workflow.
   - `target=app` (para `public/`) o `target=root` (para portada).
   - `protocol=ftp` (o `ftps` si el server lo permite).
   - `serverDirApp=public_html/gestionmascotas/public/`.
   - `serverDirRoot=public_html/`.
3. Revisar logs; si sube “0 B”, es que no hubo cambios.

## 9) Anexos y referencias
- Estado previo y plan inicial: ver `docs/Proyecto Mascotas Codex 11-11-2025.md`.
- Conversaciones y decisiones: ver `docs/ChatCodex11_11_2025_v2.md`.

## 10) Idea refinada de producto (dueños y prestadores)

### 10.1 Objetivo general
- Ser el “cerebro” de la vida de cada mascota para el dueño (historial, recordatorios, contactos, documentos), y monetizar ofreciendo visibilidad segmentada a prestadores (veterinarios, peluquerías, paseadores, etc.).

### 10.2 Servicios clave para dueños (lado gratuito / enganche)
- Historial médico por mascota:
  - Vacunas, cirugías, alergias, enfermedades crónicas, estudios, peso.
  - Línea de tiempo de eventos importantes (adopción, tratamientos, controles).
- Agenda y recordatorios:
  - Recordatorios por email (y futuro WhatsApp) para vacunas, desparasitaciones, controles anuales, medicamentos.
  - Frecuencias configurables por mascota y por tratamiento.
- Carpeta de documentos:
  - Guardar fotos del carnet, recetas, certificados, chip, papeles de viaje.
  - Pensado para que no dependa de una sola veterinaria o ciudad.
- Contactos importantes:
  - Veterinaria principal, peluquería, paseador, guardería, emergencias 24hs.
  - Botones de acción rápida (llamar, WhatsApp, ver mapa).
- Agenda compartida:
  - Permitir compartir el perfil de la mascota con familia/cuidador para que todos vean recordatorios y datos clave.
- Funciones emocionales:
  - Línea de tiempo de la mascota con fotos e hitos.
  - Cumpleaños y aniversarios con recordatorios y mensajes especiales.

### 10.3 Cómo se ve para el dueño (experiencia)
- “Tablero” por mascota:
  - Vista simple: próximos recordatorios, últimas visitas, contactos favoritos.
  - Acceso rápido a documentos y notas importantes.
- Foco en resolver problemas reales:
  - “No me olvido más de las vacunas”.
  - “Tengo todo a mano si viajo o cambio de veterinaria”.

### 10.4 Servicios para prestadores (monetización)
- Presencia básica gratuita:
  - Ficha con datos mínimos (nombre, ciudad, tipo de servicio).
  - Posibilidad de ser agregado como “contacto de confianza” por los dueños.
- Planes pagos (“Pro”):
  - Mayor visibilidad en listados y búsquedas dentro del sitio.
  - Prioridad en resultados segmentados (ej: “prestadores de La Rioja”).
  - Posibilidad de subir fotos, promociones y textos destacados.
  - Botones de contacto directo (WhatsApp, teléfono, link a agenda externa).
- Promoción segmentada:
  - Ejemplos:
    - Vets de La Rioja se muestran primero a dueños con mascotas en La Rioja.
    - Paseadores que pagan por promoción aparecen a dueños que marcaron “busco paseador”.
- Métricas para prestadores:
  - Cantidad de vistas de su ficha.
  - Contactos/derivaciones generadas desde Mascotas y Mimos (futuro).

### 10.5 Relación entre valor al dueño y negocio
- Cuanto más valor real reciba el dueño (organización, recordatorios, tranquilidad), más tiempo pasará en la plataforma y más datos de contexto existirán.
- Esos datos agregados (siempre respetando privacidad) permiten:
  - Ofrecer a los prestadores acceso a una base de dueños “activos”.
  - Ofrecer publicidad relevante (“no un banner genérico, sino estar delante del dueño correcto en el momento correcto”).

### 10.6 Prioridades para el MVP (lo primero a construir)
1. Historial por mascota + agenda de vacunas/tratamientos + recordatorios por email.
2. Carpeta básica de documentos de la mascota.
3. Gestión de contactos de confianza (vet principal, etc.).
4. Listado simple de prestadores por ciudad (sin planes pagos todavía, solo presencia).
5. Una vista de tablero para el dueño donde vea:
   - Próximos recordatorios.
   - Últimas acciones.
   - Acceso a contactos y documentos.

Con esto, la primera versión ya entrega valor concreto a los dueños y deja preparado el terreno para vender visibilidad y planes Pro a prestadores una vez que exista masa crítica de mascotas registradas.

## 11) Propuesta de home pública de mascotasymimos.com

### 11.1 Objetivo de la home
- Explicar rápidamente qué hace Mascotas y Mimos, diferenciando entre dueños y prestadores.
- Invitar a los dueños a crear cuenta gratuita.
- Presentar la propuesta de valor para prestadores sin distraer al dueño.

### 11.2 Estructura general (por secciones)
- Hero (lo primero que se ve):
  - Título: “Tu agenda completa para la salud de tus mascotas”.
  - Subtítulo: “Historial, recordatorios y contactos en un solo lugar. Gratis para dueños. Prestadores pueden destacarse donde están sus clientes.”
  - Botones destacados:
    - “Soy dueño/a de mascotas”.
    - “Soy prestador/a de servicios”.
  - Imagen/ilustración: personas con sus mascotas + captura de la app.

- Bloque “Para dueños de mascotas”:
  - Título: “Para dueños de mascotas”.
  - Beneficios con iconos:
    - Historial médico por mascota.
    - Recordatorios de vacunas y tratamientos.
    - Carpeta de documentos (carnet, estudios, certificados).
    - Contactos importantes a un clic (vet, peluquería, paseador).
  - CTA principal: “Crear mi cuenta gratis”.
  - CTA secundario: “Ver cómo funciona”.

- Bloque “Para veterinarias y prestadores”:
  - Título: “Para veterinarias y prestadores”.
  - Mensajes clave:
    - “Llegá a dueños que ya organizan la salud de sus mascotas”.
    - “Aparecé primero en tu ciudad o barrio”.
    - “Mostrá tus servicios y fotos en tu ficha”.
  - CTA: “Quiero aparecer como prestador”.

- Bloque “Cómo funciona” en 3 pasos:
  - Paso 1: “Creás tu cuenta y cargás tus mascotas”.
  - Paso 2: “Guardás historial y contactos, activás recordatorios”.
  - Paso 3: “Recibís avisos y, si querés, encontrás nuevos prestadores”.

- Bloque de confianza / prueba social:
  - Testimonios cortos de dueños (cuando existan).
  - Mensaje de privacidad: “Tus datos y los de tus mascotas están protegidos. No compartimos tu información personal sin tu permiso.”

- Footer:
  - Enlaces: Sobre nosotros, Preguntas frecuentes, Términos y condiciones, Contacto.
  - Datos de contacto y redes (Facebook, Instagram) cuando estén activos.
## 12) Flujo completo actualizado (home, registro y roles) con teléfono y planes

### 12.1 Home pública (landing) orientada a dueños y prestadores

Objetivo: explicar rápido qué es, separar bien DUEÑO vs PRESTADOR, y empujar a registrarse. Siempre mobile-first.

- Hero (primer pantallazo):
  - Título: `Tu agenda digital para la salud de tus mascotas`
  - Subtítulo: `Gratis para dueños. Planes Free, Pro y Premium para veterinarias y prestadores.`
  - Botones (apilados en mobile, lado a lado en desktop):
    - `Soy dueño/a de mascotas` → flujo de registro de dueño.
    - `Soy veterinario/a o prestador` → flujo de registro de prestador.
  - Microtexto: `Organizá vacunas, tratamientos, documentos y contactos en un solo lugar.`

- Bloque “Para dueños”:
  - Título: `Pensado para familias con mascotas`
  - Texto: `Una cuenta por familia, hasta 10 mascotas. Todo su historial y recordatorios en un solo lugar.`
  - Beneficios con iconos:
    - Historial médico por mascota (vacunas, cirugías, alergias, controles).
    - Recordatorios automáticos de vacunas, desparasitaciones y tratamientos.
    - Carpeta digital con carnets, estudios, certificados y recetas en PDF.
    - Contactos de confianza: veterinaria, paseador, peluquería, guardería, emergencias.
  - CTA:
    - Botón principal: `Crear cuenta gratuita`.
    - Link secundario: `Ver cómo funciona para dueños`.

- Bloque “Para veterinarias y prestadores”:
  - Título: `Más visibilidad donde están tus clientes`
  - Texto: `Aparecé en los listados de tu zona, enviá recetas en PDF y destacá tus servicios con planes Free, Pro y Premium.`
  - Beneficios por plan (resumen):
    - Free: estar en listados + ficha básica + contacto directo + pocas recetas PDF por mes.
    - Pro: mejor posición, ficha ampliada (fotos, servicios), más recetas PDF, estadísticas básicas.
    - Premium: posición top, más promos, recetas casi sin límite razonable, estadísticas avanzadas y mejor branding.
  - CTA:
    - Botón: `Quiero aparecer como prestador`.
    - Link: `Ver planes Free, Pro y Premium` (página de comparación futura).

- Bloque “Cómo funciona” (3 pasos):
  - `1. Elegís si sos dueño o prestador` (creás tu cuenta).
  - `2. Cargás tus mascotas o tu ficha profesional`.
  - `3. Usás la agenda de salud, documentos y listados día a día`.

- Bloque de confianza y límites de uso:
  - Mensajes clave:
    - `Tus datos y los de tus mascotas son privados. No los compartimos sin tu permiso.`
    - `Las cuentas de dueños son para uso personal (hasta 10 mascotas). Si sos clínica o negocio, usá los planes para prestadores.`
    - `Las recetas las emiten sólo veterinarios que informan su matrícula profesional; el dueño siempre puede verificar esos datos.`

- Footer:
  - Enlaces: Sobre Mascotas y Mimos, Preguntas frecuentes, Términos y condiciones, Política de privacidad, Contacto.
  - Redes: enlaces a Facebook e Instagram cuando estén activos.

### 12.2 Flujo de registro (elección de rol)

- Pantalla “Elegir rol”:
  - Título: `¿Cómo querés usar Mascotas y Mimos?`
  - Cards/botones grandes:
    - `Soy dueño/a de mascotas` → texto: `Organizá la salud y los documentos de hasta 10 mascotas.`
    - `Soy veterinario/a o prestador de servicios` → texto: `Aparecé en listados, enviá recetas en PDF y destacá tu negocio.`

### 12.3 Registro de dueño (con teléfono preparado para WhatsApp)

- Pantalla 1: datos de cuenta
  - Título: `Crear cuenta para dueños`
  - Campos:
    - Nombre y apellido.
    - Email.
    - Contraseña.
    - Teléfono móvil:
      - Input con prefijo fijo `+54` (Argentina) y bandera AR.
      - Validación para formato numérico sin letras (se puede mostrar `+54 9 11 1234-5678`, pero guardar normalizado).
      - Guardar el teléfono pensando en futuros contactos por WhatsApp con prestadores (recordatorios o mensajes opcionales).
    - País (Argentina), Provincia y Localidad (para sugerir prestadores cercanos más adelante).
  - Texto de contexto:
    - `Podés registrar hasta 10 mascotas en tu cuenta.`
  - Checkbox:
    - `Acepto los Términos y la Política de Privacidad.`
  - CTA:
    - `Crear mi cuenta`.

- Pantalla 2: agregar primera mascota
  - Título: `Agregá tu primera mascota`
  - Campos:
    - Nombre.
    - Especie (perro, gato, otro).
    - Fecha de nacimiento (o selector con “no la sé exactamente”).
    - Sexo.
    - Foto (opcional).
  - CTA:
    - `Guardar y continuar`.
  - Luego pasa al dashboard de dueño.

- Nota de futuro: plan Pro para dueños
  - En versiones posteriores se puede ofrecer un plan Pro para dueños con beneficios como:
    - Consultas virtuales con veterinarios.
    - Más tipos de recordatorios avanzados.
    - Soporte prioritario.
  - Por ahora sólo se deja previsto conceptualmente, sin implementarlo aún.

### 12.4 Registro de prestador (con matrícula y teléfono para WhatsApp)

- Pantalla 1: datos de cuenta del prestador
  - Título: `Crear cuenta para veterinarias y prestadores`
  - Campos:
    - Nombre del negocio o profesional.
    - Tipo de prestador (desplegable: Veterinaria, Veterinario independiente, Peluquería, Paseador, Guardería, Otros).
    - Email.
    - Contraseña.
    - Teléfono/WhatsApp de contacto:
      - Input con prefijo fijo `+54` (bandera AR).
      - Normalización similar a la de dueños para facilitar el click‑to‑WhatsApp.
    - País (Argentina), Provincia, Localidad/Barrio.
  - Campos adicionales para veterinarios (obligatorios si el tipo es “Veterinaria” o “Veterinario independiente”):
    - Número de matrícula profesional.
    - Provincia/entidad que otorga la matrícula.
  - Checkbox:
    - `Declaro que la matrícula profesional informada es correcta y vigente (si corresponde).`
    - `Acepto los Términos, la Política de Privacidad y las condiciones para prestadores.`
  - CTA:
    - `Crear mi cuenta`.
  - Nota:
    - La responsabilidad de la matrícula recae en el profesional: si carga una matrícula falsa, queda expuesto porque la receta llevará su nombre, matrícula y datos de contacto.

- Pantalla 2: elegir plan (Free / Pro / Premium)
  - Título: `Elegí cómo querés empezar`
  - Cards con resumen:
    - **Plan Free**:
      - Aparece en listados generales de su zona.
      - Ficha básica (datos + 1 foto).
      - Contacto directo (teléfono/WhatsApp).
      - Cupo reducido de recetas PDF al mes.
      - Sin estadísticas o sólo un contador básico.
      - CTA: `Empezar con Free` (opción recomendada para probar).
    - **Plan Pro**:
      - Mejor posición en listados que Free.
      - Ficha ampliada con varias fotos, servicios, descripción.
      - Más recetas PDF al mes.
      - Estadísticas básicas (vistas de ficha, clics a WhatsApp).
      - CTA: `Elegir Pro`.
    - **Plan Premium**:
      - Posición top en resultados de su zona.
      - Recetas PDF con plantillas personalizadas (logo, datos de la clínica).
      - Cupo alto de recetas (prácticamente sin límite razonable).
      - Más promociones simultáneas.
      - Estadísticas avanzadas (por período, por tipo de servicio).
      - CTA: `Elegir Premium`.
  - Todos los planes se pueden cambiar luego desde el panel.

- Pantalla 3: completar ficha profesional mínima
  - Título: `Completá tu ficha profesional`
  - Campos mínimos:
    - Descripción corta del servicio.
    - Servicios principales (selección de lista prediseñada + otros).
    - Horarios de atención.
    - Foto de perfil/logo.
  - CTA:
    - `Guardar y entrar al panel`.

### 12.5 Lista base de prestaciones para veterinarios (para usar en fichas y recetas)

- Consultas generales:
  - Consulta clínica general.
  - Consulta de control anual.
  - Consulta de urgencia.
  - Consulta prequirúrgica / posquirúrgica.

- Vacunas y prevención:
  - Vacuna antirrábica.
  - Vacunas séxtuple / quíntuple / triple felina (según corresponda).
  - Vacunas contra leptospirosis, tos de las perreras, etc.
  - Desparasitación interna.
  - Desparasitación externa (pulgas, garrapatas).

- Estudios y diagnósticos:
  - Análisis de sangre.
  - Análisis de orina.
  - Coproparasitológico.
  - Radiografías.
  - Ecografías.
  - Electrocardiograma.

- Procedimientos y cirugías:
  - Castración / esterilización.
  - Limpieza dental.
  - Cirugías menores.
  - Cirugías mayores (a detallar según necesidad).

- Otros servicios:
  - Certificados de salud para viaje.
  - Certificados para trámites municipales.
  - Planes de control de peso.
  - Planes de vacunación y prevención personalizados.

Esta lista sirve como base de selección rápida en la ficha de servicios del veterinario y como referencia al generar recetas/indicaciones en PDF. Cada receta incluirá:
- Nombre y apellido del profesional.
- Número de matrícula profesional y entidad que la otorga.
- Datos del negocio (si aplica).
- Datos de la mascota y del dueño.
- Descripción del tratamiento/medicación/indicación.

La validación final siempre la hace el dueño: ve quién emitió la receta (nombre, matrícula, dirección), y decide si confía o consulta a otro profesional.
