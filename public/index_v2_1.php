<?php
$siteUrl = 'https://mascotasymimos.com/';
$siteName = 'Mascotas y Mimos';
$siteDescription = 'Agenda digital para la salud y la vida diaria de tus mascotas. Gratis para dueños, con planes para veterinarias y prestadores.';
$ogImage = $siteUrl . 'assets/logo/logo.png';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?> · Agenda digital para mascotas</title>
  <meta name="description" content="<?php echo htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="robots" content="index,follow">
  <link rel="canonical" href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:title" content="<?php echo htmlspecialchars($siteName . ' · Agenda digital para mascotas', ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:url" content="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:image:alt" content="Logo de Mascotas y Mimos">
  <meta name="theme-color" content="#FFD6A5">
  <meta name="application-name" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
  <link rel="icon" type="image/png" sizes="192x192" href="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">

  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style_tailwind_overrides.css">
  <style>
    html, body { font-family: Poppins, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#FFD6A5] to-[#FAE0C3] text-[#5A3E36]">
    <header class="w-full">
    <div class="max-w-screen-xl mx-auto px-4 py-4 flex items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <img src="assets/logo/logo.png" alt="Mascotas y Mimos" class="w-10 h-10 md:w-12 md:h-12 object-contain" />
        <div>
          <span class="block text-base md:text-lg font-semibold">Mascotas y Mimos</span>
          <span class="block text-xs md:text-sm opacity-80">Agenda digital para familias con mascotas</span>
        </div>
      </div>
      <nav class="hidden md:flex items-center gap-4 text-sm">
        <a href="#duenos" class="hover:underline">Para dueños</a>
        <a href="#prestadores" class="hover:underline">Para prestadores</a>
        <a href="#como-funciona" class="hover:underline">Cómo funciona</a>
        <a href="login.php" class="ml-3 inline-flex items-center justify-center rounded-full bg-[#A97155] px-4 py-2 text-sm font-semibold text-white shadow hover:bg-[#8d5f47]">
          Ingresar a mi cuenta
        </a>
      </nav>
    </div>
  </header>

  <main>
    <!-- Hero -->
    <section class="max-w-screen-xl mx-auto px-4 pt-4 pb-8 md:pt-8 md:pb-12">
      <div class="rounded-2xl bg-white/90 backdrop-blur p-6 md:p-10 shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10 items-center">
          <div class="text-center md:text-left">
            <h1 class="text-[clamp(1.7rem,5vw,2.5rem)] leading-tight font-bold mb-2">
              Tu agenda digital para la salud de tus mascotas
            </h1>
            <p class="text-sm md:text-base leading-relaxed mb-4">
              Gratis para dueños. Planes Free, Pro y Premium para veterinarias y prestadores que quieren estar donde están sus clientes.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 items-center md:items-start justify-center md:justify-start mt-2">
              <a href="#registro-duenos" class="min-h-[44px] inline-flex items-center justify-center rounded-lg bg-[#A97155] text-white px-5 py-3 hover:bg-[#8d5f47] w-full sm:w-auto">
                Soy dueño/a de mascotas
              </a>
              <a href="#registro-prestadores" class="min-h-[44px] inline-flex items-center justify-center rounded-lg bg-white text-[#A97155] border border-[#A97155]/30 px-5 py-3 hover:bg-[#fff2ea] w-full sm:w-auto">
                Soy veterinario/a o prestador
              </a>
            </div>
          </div>
          <div>
            <img src="assets/img/hero.webp" onerror="this.src='assets/img/hero.png'" alt="Familia con mascotas usando una app" class="w-full aspect-[4/3] object-cover rounded-xl shadow" />
          </div>
        </div>
      </div>
    </section>

    <!-- Introducción general -->
    <section id="intro-general" class="max-w-screen-xl mx-auto px-4 pb-8 md:pb-10">
      <div class="bg-white/95 border border-white/60 shadow-lg rounded-2xl p-6 md:p-8 text-center">
        <p class="text-xs uppercase tracking-[0.3em] text-[#A97155] mb-2">Mascotas y Mimos</p>
        <h2 class="text-[clamp(1.6rem,4vw,2.4rem)] font-semibold text-[#5A3E36] mb-3">
          Un mismo lugar para dueños de mascotas y prestadores de servicios
        </h2>
        <p class="max-w-3xl mx-auto text-sm md:text-base leading-relaxed text-[#5A3E36]/90">
          Si sos dueño, usás la agenda para recordar vacunas, guardar documentos y tener a mano tus contactos de confianza.
          Si sos prestador, mostrás tus servicios a esas mismas familias y ganás visibilidad donde ya están organizando la salud de sus mascotas.
        </p>
      </div>
    </section>

    <!-- Bloque dueños -->
    <section id="duenos" class="max-w-screen-xl mx-auto px-4 pb-8 md:pb-12">
      <h2 class="text-[clamp(1.4rem,4vw,2.1rem)] font-semibold mb-4 text-center underline underline-offset-4 decoration-2 decoration-[#A97155]">
        Si sos dueño de mascotas, esta es tu agenda
      </h2>
      <p class="max-w-2xl mx-auto text-sm md:text-base leading-relaxed text-center mb-6">
        Una cuenta por familia, hasta 10 mascotas. Creás tu usuario como dueño, cargás tus mascotas
        y usás la agenda para recordar vacunas, visitas y documentos importantes, incluso si cambiás de ciudad o de veterinaria.
      </p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <article class="bg-white/95 rounded-xl p-4 md:p-5 shadow">
          <div class="mb-2">
            <img src="assets/img/agenda_vacunas.webp" onerror="this.src='assets/img/agenda_vacunas.jpg'" alt="Agenda de vacunas" class="w-full aspect-[4/3] object-cover rounded-lg mb-2" />
          </div>
          <h3 class="text-base md:text-lg font-semibold mb-1">Historial médico por mascota</h3>
          <p class="text-sm md:text-sm leading-relaxed">Vacunas, cirugías, alergias y controles en una ficha digital por cada mascota.</p>
        </article>
        <article class="bg-white/95 rounded-xl p-4 md:p-5 shadow">
          <div class="mb-2">
            <img src="assets/img/recordatorio_mail..webp" onerror="this.src='assets/img/recordatorio_mail..png'" alt="Recordatorios" class="w-full aspect-[4/3] object-cover rounded-lg mb-2" />
          </div>
          <h3 class="text-base md:text-lg font-semibold mb-1">Recordatorios automáticos</h3>
          <p class="text-sm md:text-sm leading-relaxed">Avisos por email (y a futuro WhatsApp) para vacunas, desparasitaciones y tratamientos.</p>
        </article>
        <article class="bg-white/95 rounded-xl p-4 md:p-5 shadow">
          <div class="mb-2">
            <img src="assets/img/veterinario_consultorio.webp" onerror="this.src='assets/img/veterinario_consultorio.png'" alt="Documentos digitales" class="w-full aspect-[4/3] object-cover rounded-lg mb-2" />
          </div>
          <h3 class="text-base md:text-lg font-semibold mb-1">Carpeta de documentos</h3>
          <p class="text-sm md:text-sm leading-relaxed">Carnets, estudios y recetas en PDF siempre disponibles en la nube, sin papeles perdidos.</p>
        </article>
        <article class="bg-white/95 rounded-xl p-4 md:p-5 shadow">
          <div class="mb-2">
            <img src="assets/img/mapa_prestadores.webp" onerror="this.src='assets/img/mapa_prestadores.png'" alt="Mapa de prestadores" class="w-full aspect-[4/3] object-cover rounded-lg mb-2" />
          </div>
          <h3 class="text-base md:text-lg font-semibold mb-1">Contactos de confianza</h3>
          <p class="text-sm md:text-sm leading-relaxed">Veterinaria, paseador, peluquería, guardería y emergencias con acceso rápido desde la ficha de cada mascota.</p>
        </article>
      </div>
      <div id="registro-duenos" class="mt-6 text-center">
        <a href="registro.php" class="inline-flex min-h-[44px] items-center justify-center rounded-lg bg-[#A97155] text-white px-6 py-3 hover:bg-[#8d5f47]">
          Crear cuenta gratuita de dueño
        </a>
        <p class="mt-2 text-xs md:text-sm opacity-80">En la versión inicial, el registro se enfocará en dueños de Argentina.</p>
      </div>
    </section>

    <!-- Bloque prestadores -->
    <section id="prestadores" class="max-w-screen-xl mx-auto px-4 pb-8 md:pb-12">
      <h2 class="text-[clamp(1.4rem,4vw,2.1rem)] font-semibold mb-4 text-center underline underline-offset-4 decoration-2 decoration-[#A97155]">
        Si prestás servicios para mascotas, te ayudamos a estar donde te buscan
      </h2>
      <p class="max-w-2xl mx-auto text-sm md:text-base leading-relaxed text-center mb-6">
        Como prestador (veterinaria, peluquería, paseador, guardería, etc.) creás tu cuenta y tu ficha.
        Elegís un plan, cargás tus servicios y datos de contacto, y aparecés frente a dueños que ya usan la agenda para cuidar a sus mascotas.
      </p>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
        <article class="bg-white/95 rounded-xl p-5 shadow border border-amber-100">
          <h3 class="text-base md:text-lg font-semibold mb-1">Plan Free</h3>
          <p class="text-sm md:text-sm leading-relaxed mb-2">Punto de partida para cualquier prestador.</p>
          <ul class="text-sm list-disc pl-5 space-y-1">
            <li>Aparecer en listados generales de tu ciudad.</li>
            <li>Ficha básica con datos de contacto.</li>
            <li>Enlace directo a teléfono/WhatsApp.</li>
            <li>Cupo reducido de recetas PDF al mes.</li>
          </ul>
        </article>
        <article class="bg-white/98 rounded-xl p-5 shadow-lg border-2 border-[#A97155]">
          <h3 class="text-base md:text-lg font-semibold mb-1">Plan Pro</h3>
          <p class="text-sm md:text-sm leading-relaxed mb-2">Para prestadores que atienden dueños con frecuencia.</p>
          <ul class="text-sm list-disc pl-5 space-y-1">
            <li>Mejor posición en listados que Free.</li>
            <li>Ficha ampliada con fotos y servicios.</li>
            <li>Más recetas PDF al mes.</li>
            <li>Estadísticas básicas (vistas y clics a WhatsApp).</li>
          </ul>
        </article>
        <article class="bg-white/95 rounded-xl p-5 shadow border border-amber-100">
          <h3 class="text-base md:text-lg font-semibold mb-1">Plan Premium</h3>
          <p class="text-sm md:text-sm leading-relaxed mb-2">Para clínicas que quieren máxima visibilidad.</p>
          <ul class="text-sm list-disc pl-5 space-y-1">
            <li>Posición top en resultados de tu zona.</li>
            <li>Recetas PDF con plantilla personalizada.</li>
            <li>Más promociones activas al mismo tiempo.</li>
            <li>Estadísticas avanzadas por período y tipo de servicio.</li>
          </ul>
        </article>
      </div>
      <div id="registro-prestadores" class="mt-6 text-center">
        <a href="registro.php" class="inline-flex min-h-[44px] items-center justify-center rounded-lg bg-[#A97155] text-white px-6 py-3 hover:bg-[#8d5f47]">
          Crear cuenta gratuita de prestador
        </a>
      </div>
    </section>

    <!-- Cómo funciona -->
    <section id="como-funciona" class="max-w-screen-xl mx-auto px-4 pb-10 md:pb-14">
      <h2 class="text-[clamp(1.4rem,4vw,2rem)] font-semibold mb-4 text-center underline underline-offset-4 decoration-2 decoration-[#A97155]">
        ¿Cómo funciona para cada rol?
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        <article class="bg-white/95 rounded-xl p-5 shadow">
          <h3 class="text-base md:text-lg font-semibold mb-2">Si sos dueño de mascotas</h3>
          <ol class="text-sm md:text-sm leading-relaxed list-decimal list-inside space-y-1">
            <li>Elegís registrarte como dueño y completás tus datos básicos.</li>
            <li>Cargás tus mascotas y su historial (vacunas, cirugías, controles, contactos).</li>
            <li>Usás la agenda y los recordatorios para organizar la salud de toda tu familia animal.</li>
          </ol>
        </article>
        <article class="bg-white/95 rounded-xl p-5 shadow">
          <h3 class="text-base md:text-lg font-semibold mb-2">Si sos prestador de servicios</h3>
          <ol class="text-sm md:text-sm leading-relaxed list-decimal list-inside space-y-1">
            <li>Creás tu cuenta como prestador y completás tu ficha profesional.</li>
            <li>Definís tus servicios, horarios y datos de contacto (teléfono, WhatsApp, dirección).</li>
            <li>Aparecés en los listados y, en etapas siguientes, en mapas y búsquedas usadas por los dueños.</li>
          </ol>
        </article>
      </div>
      <p class="max-w-2xl mx-auto mt-4 text-xs md:text-sm text-center opacity-80">
        En próximas etapas se integrarán los tableros por rol, mapas de prestadores con geolocalización y más opciones de agenda y comunicación.
      </p>
    </section>
  </main>

  <footer class="py-6 text-center text-xs md:text-sm opacity-80">
    © <?php echo date('Y'); ?> Mascotas y Mimos · Esta es una versión de prueba de la nueva home (index_v2.php) en entorno local.
  </footer>
</body>
</html>

