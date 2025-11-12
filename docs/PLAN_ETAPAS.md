# Plan por Etapas — Mascotas y Mimos (WordPress)

Este plan es operativo y a prueba de errores. Marcá cada casilla al completar.

## Etapa 1 — Base WP (tema + portada)
- [ ] Instalar tema: WP Admin → Apariencia → Temas → Añadir → “Kadence” → Instalar → Activar.
- [ ] Starter: Plugins → Añadir → “Kadence Starter Templates” → Instalar → Activar.
- [ ] Importar plantilla: Apariencia → Starter Templates → Gutenberg → “Local Services” (o similar) → Import (completo).
- [ ] Ajustes → Lectura → “Tu portada muestra” = “Una página estática”. Portada = “Inicio”.
- [ ] Ajustes → Enlaces permanentes → “Nombre de la entrada” → Guardar.
- [ ] Menú: Apariencia → Editor → Navegación. Dejar: Inicio, Servicios, Prestadores, Contacto.
- [ ] Verificar Home en https://mascotasymimos.com/

## Etapa 2 — Directorio de Prestadores (CPT + taxonomías + campos)
Herramientas: “Custom Post Type UI (CPT UI)” + “Advanced Custom Fields (ACF)” + “Filter Everything”.

1) Instalar plugins
- [ ] Plugins → Añadir: “Custom Post Type UI”, “Advanced Custom Fields”, “Filter Everything” → Instalar/Activar.

2) Crear Post Type “prestador” (CPT UI → Add/Edit Post Types)
- [ ] Slug: `prestador`
- [ ] Plural: Prestadores | Singular: Prestador
- [ ] Supports: title, editor, thumbnail, excerpt, custom-fields, comments
- [ ] Has archive: Sí | Public: Sí | Rewrite: Sí (`prestador`)

3) Crear taxonomías (CPT UI → Add/Edit Taxonomies) y asociarlas a `prestador`
- [ ] `servicio` (no jerárquica): Peluquería, Paseo, Veterinaria, Guardería, Adiestramiento, Pet shop.
- [ ] `provincia` (jerárquica): cargar provincias de Argentina (lista en BITACORA.md).
- [ ] `localidad` (jerárquica): crear bajo cada provincia según necesidad (por ahora on‑demand).

4) Campos con ACF (ACF → Field Groups → Add New)
- [ ] Grupo: “Prestador” (Location: Post Type == Prestador)
- [ ] Campos: telefono (Texto), whatsapp (Texto, formato E.164), direccion (Texto), lat (Número), lng (Número), es_pro (True/False), web (URL), galeria (Galería, máximo 10).

5) Listado y filtros (Filter Everything)
- [ ] Página “Prestadores” → añadir bloque de listado (Query Loop → tipo `prestador`).
- [ ] Filter Everything → Filter Sets → Add New → Post type: `prestador` → Display en página “Prestadores”.
- [ ] Controles: `provincia` (Select), `localidad` (Select), `servicio` (Select). Guardar y probar.

6) Mapa en ficha (opcional rápido)
- [ ] Instalar “Code Snippets”. Añadir snippet (Frontend only) con el código de Leaflet indicado en BITACORA.md para mostrar mapa si hay `lat/lng`.

7) Carga de prueba
- [ ] Crear 3 prestadores con servicios distintos, provincias/localidades y al menos 1 PRO (es_pro = verdadero).
- [ ] Verificar filtros y mapa.

## Etapa 3 — Legales y WhatsApp
- [ ] Páginas: “Política de Privacidad” y “Términos y Condiciones”.
- [ ] Joinchat: número +5191133376183, mensaje: “Hola, vengo desde {SITE}. Quiero más información sobre {TITLE}. {URL}”, info emergente: “¿Consultas o turnos? 💬”.
- [ ] Ocultar Joinchat en páginas legales.
- [ ] Formularios (WPForms/CF7): checkbox de consentimiento.

## Etapa 4 — SEO y Medición
- [ ] Instalar SEOPress. Configurar títulos/OG, sitemap y negocio local.
- [ ] GA4: crear propiedad, pegar `gtag` en cabecera (o vía plugin). Evento de clic en botón WhatsApp.

## Etapa 5 — Rendimiento y seguridad
- [ ] 2FA para administradores (WP 2FA).
- [ ] Optimizar imágenes (subir comprimidas), lazy‑load (nativo), cache (si disponible en hosting).

## Próximas (del Plan WP)
- Dueños + Mascotas (CPT `mascota`) y recordatorios por email.
- Membresías y pagos (Woo + Mercado Pago o PMPro temporal).
- Reseñas 1–5 en `prestador` y destacados.

Notas rápidas
- Joinchat ya instalado. Número en formato E.164 para Perú: `+5191133376183`.
- Si necesitás import masivo de localidades, lo armamos más adelante.

