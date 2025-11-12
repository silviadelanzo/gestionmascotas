# Mascotas y Mimos — Plan WP

## Resumen
- País objetivo: Argentina. Búsqueda por Provincia → Localidad y por Servicio.
- Usuarios: Dueños (free/pro) con multi‑mascotas y recordatorios; Prestadores (free/pro).
- Directorio: Listados por Provincia/Localidad; Prestadores Pro destacados y en mapa. Free: nombre/teléfono/dirección sin mapa.
- Pro Dueño: recordatorios configurables (vacunas, baño, tratamientos, controles).
- Pro Prestador: destacado + reseñas 1–5 estrellas + opiniones + mapa + agenda (turnos online).
- Landing temporal: captura de emails (Dueños y Prestadores) para campañas.
- Objetivo: sitio rápido, seguro, responsive, con estética pastel.

## Roles y permisos
- `dueno`: registrar mascotas, configurar recordatorios, ver directorio.
- `prestador`: gestionar su ficha. En plan Pro: agenda, destacado, mapa.

## MVP (alcance mínimo)
- CPT `prestador` con taxonomías `provincia`, `localidad`, `servicio`.
- Listados por Provincia/Localidad/Servicio. Prestador Pro aparece destacado y con mapa (Leaflet/OSM).
- Registro Dueño + CPT `mascota` (multi‑registro) + panel "Mis Mascotas".
- Recordatorios por email (cron diario) para eventos definidos por el Dueño Pro.
- Landing “coming soon” con captación de emails (Dueños/Prestadores).

## Hitos (M0 → M8)
- M0 Portada + Captura: landing con 2 formularios (Dueños/Prestadores) a Brevo/MailerLite.
- M1 Directorio Base: CPT `prestador`, taxonomías AR, listados, Free vs Pro (destacado/mapa).
- M2 Dueño + Mascotas: registro Dueño, CPT `mascota` multi, panel “Mis Mascotas”.
- M3 Recordatorios: metacampos por mascota, preferencia de aviso, cron y emails.
- M4 Agenda y Mapas Pro: Easy Appointments (o Amelia) por prestador + Leaflet/OSM.
- M5 Membresías y Pagos: Free/Pro para Dueño/Prestador. WooCommerce + Mercado Pago (ideal AR) o PMPro inicial.
- M6 Reseñas: calificación 1–5 + opiniones en `prestador`. Destacados por especialidad.
- M7 Seguridad/SEO/Performance: cache, Wordfence, schema LocalBusiness, analítica, optimización.
- M8 Estilo y Responsive: paleta pastel, menús retráctiles, microcopys, QA móvil.

## KPIs
- Emails captados (Dueños/Prestadores).
- Prestadores Pro activos.
- Tasa de reservas completadas.
- Aperturas/clics de recordatorios.

## Fuera de alcance inicial
- App móvil nativa.
- E‑commerce de productos.
- Chat en tiempo real.

## Riesgos y mitigación
- Geocodificación: usar Nominatim con límites; permitir lat/lng manual.
- Entregabilidad email: SMTP autenticado (SPF/DKIM) + proveedor (Brevo/MailerLite).
- Rendimiento: tema liviano, cache, imágenes optimizadas, sin page builders pesados.

