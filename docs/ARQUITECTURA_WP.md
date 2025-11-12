# Arquitectura WP — Mascotas y Mimos

## Tema y estilo
- Tema: GeneratePress (o Blocksy) + editor de bloques. Sin builders pesados.
- Paleta: pasteles suaves, alto contraste de texto, tipografía legible.

## Plugins base
- Seguridad: Wordfence, reCAPTCHA, 2FA; deshabilitar XML‑RPC.
- Backups: UpdraftPlus (nube semanal + antes de updates).
- Caché: LiteSpeed Cache (si disponible) o WP Super Cache.
- SMTP: WP Mail SMTP (dominio autenticado SPF/DKIM/DMARC).
- SEO/Analytics: RankMath + Site Kit (Analytics/Search Console).
- Email marketing: Brevo o MailerLite (listas Dueños/Prestadores).

## Contenido y datos
- CPT `mascota`: owner_id, especie, raza, nacimiento, sexo, peso, chip, foto.
- CPT `prestador`: user_id, servicios, dirección, provincia/localidad (tax), lat/lng, tel, WhatsApp, horarios, destacado, rating promedio.
- Taxonomías: `provincia` (padre), `localidad` (hija), `servicio` (jerárquica).
- Comentarios/valoraciones: habilitados en `prestador` (1–5 estrellas + opinión, moderación).

## Directorio y búsqueda
- Listados por Provincia → Localidad → Servicio (paginados).
- Filtros: Search & Filter Pro (o FacetWP) para taxonomías y campos clave.
- Free vs Pro: Pro aparece con badge "Destacado" y en mapa; Free sin mapa.

## Mapas y geocodificación
- Leaflet + OpenStreetMap (costo cero). Geocodificación con Nominatim (uso responsable: límites, caching, botón "Obtener coords" y edición manual).

## Agenda/Turnos
- Easy Appointments (gratis) por prestador (servicios/horarios). Alternativa premium: Amelia/Bookly.

## Membresías y pagos
- Opción AR: WooCommerce + Mercado Pago + Subscriptions (pago) para recurrencia.
- Opción rápida: Paid Memberships Pro con pasarela internacional (Stripe/PayPal) y sumar MP luego.

## Recordatorios
- Metacampos por mascota con próximas fechas (vacuna/baño/ctrl) y preferencia de aviso (días).
- WP‑Cron diario (migrable a cron real cPanel) envía emails al Dueño Pro.

## Seguridad adicional
- Forzar HTTPS, headers de seguridad (HSTS, X‑Frame‑Options, CSP básica), ocultar versión, limitar REST público, bloqueo de intentos.

## Importación Provincias/Localidades
- Fuente: catálogo AR (provincia/localidad). Cargar como taxonomías jerárquicas.
- Herramienta sugerida: importador CSV de taxonomías o script puntual (WP‑CLI o pequeño plugin de seed).

## Roadmap técnico
- v0.1: CPTs + taxonomías + listados básicos.
- v0.2: Panel “Mis Mascotas” + recordatorios email.
- v0.3: Membresías Free/Pro + gating de mapa y agenda.
- v0.4: Reseñas con calificación y moderación.
- v1.0: hardening, performance, SEO y QA responsive.

