<?php
// Landing responsive (mobile-first) para pruebas sin tocar index.php
$siteUrl = 'https://mascotasymimos.com/';
$siteName = 'Mascotas y Mimos';
$siteTagline = 'Cuidamos y mimamos a tus mejores amigos en toda Argentina';
$siteDescription = 'Mascotas y Mimos conecta dueños y prestadores para organizar historiales, recordatorios y turnos de cada mascota con una experiencia gratuita y segura.';
$ogImage = $siteUrl . 'assets/logo/logo.png';
$structuredData = [
  '@context' => 'https://schema.org',
  '@type' => 'WebSite',
  'name' => $siteName,
  'url' => $siteUrl,
  'description' => $siteDescription,
  'publisher' => [
    '@type' => 'Organization',
    'name' => $siteName,
    'url' => $siteUrl,
    'logo' => [
      '@type' => 'ImageObject',
      'url' => $ogImage,
    ],
    'contactPoint' => [
      [
        '@type' => 'ContactPoint',
        'contactType' => 'Atención al cliente',
        'email' => 'hola@mascotasymimos.com',
        'areaServed' => 'AR',
        'availableLanguage' => ['es', 'en'],
      ],
    ],
  ],
  'potentialAction' => [
    '@type' => 'SubscribeAction',
    'target' => $siteUrl . '#suscripcion',
    'description' => 'Recibí novedades del lanzamiento de Mascotas y Mimos para dueños y prestadores.',
    'result' => [
      '@type' => 'Event',
      'name' => 'Lanzamiento Mascotas y Mimos',
    ],
  ],
];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?> · Plataforma para dueños y prestadores de mascotas</title>
  <meta name="description" content="<?php echo htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="keywords" content="mascotas, agenda veterinaria, recordatorios vacunas, paseadores, pet sitters, peluquería canina, bienestar animal en Argentina">
  <meta name="author" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="robots" content="index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1">
  <link rel="canonical" href="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:locale" content="es_AR">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:title" content="<?php echo htmlspecialchars($siteName . ' · Plataforma para dueños y prestadores de mascotas', ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:url" content="<?php echo htmlspecialchars($siteUrl, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">
  <meta property="og:image:alt" content="Logo de Mascotas y Mimos">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($siteName . ' · Plataforma para dueños y prestadores de mascotas', ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($siteDescription, ENT_QUOTES, 'UTF-8'); ?>">
  <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8'); ?>">
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
  <script type="application/ld+json">
    <?php echo json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
  </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#FFD6A5] to-[#FAE0C3] text-[#5A3E36]">
  <header class="w-full">
    <div class="max-w-screen-xl mx-auto px-4 py-4 flex items-center gap-3">
      <img src="assets/logo/logo.png" alt="Mascotas y Mimos" class="w-12 h-12 object-contain" />
      <h1 class="text-[clamp(1.25rem,4vw,1.75rem)] font-semibold">Mascotas y Mimos</h1>
    </div>
  </header>

  <main>
    <!-- Hero -->
    <section class="max-w-screen-xl mx-auto px-4 pt-4 pb-8 md:pt-8 md:pb-12">
      <div class="rounded-2xl bg-white/90 backdrop-blur p-6 md:p-10 shadow">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-10 items-center">
          <div class="text-center">
            <h2 class="text-[clamp(1.5rem,5vw,2.25rem)] leading-tight font-bold mb-1">
              Cuidamos y mimamos a tus mejores amigos
              <span class="block text-[#A97155] font-semibold">Gratuito para Dueños</span>
            </h2>
            <ul class="list-disc pl-5 space-y-1 text-base md:text-lg leading-relaxed mb-4">
              <li>Historial médico por mascota</li>
              <li>Agenda de tratamientos, vacunas y estudios por mascota</li>
              <li>Recordatorios por Email o WhatsApp</li>
              <li>Listado de prestadores con mejores calificaciones en toda la Argentina.</li>
            </ul>
            <div class="flex flex-col sm:flex-row gap-3 items-center justify-center mt-2">
              <a href="#beneficios-duenos" class="min-h-[44px] inline-flex items-center justify-center rounded-lg bg-[#A97155] text-white px-5 py-3 hover:bg-[#8d5f47]">Soy Dueño de Mascotas</a>
              <a href="#beneficios-prestadores" class="min-h-[44px] inline-flex items-center justify-center rounded-lg bg-white text-[#A97155] border border-[#A97155]/30 px-5 py-3 hover:bg-[#fff2ea]">Soy Prestador</a>
            </div>
          </div>
          <div>
            <img src="assets/img/hero.webp" onerror="this.src='assets/img/hero.png'" alt="Ilustración mascotas" class="w-full aspect-[4/3] object-cover rounded-xl shadow" />
          </div>
        </div>
      </div>
    </section>

    <!-- Beneficios Dueños -->
    <section id="beneficios-duenos" class="max-w-screen-xl mx-auto px-4 pb-8 md:pb-12">
      <h3 class="text-[clamp(1.25rem,4vw,1.75rem)] font-semibold mb-4 text-center underline underline-offset-4 decoration-2 decoration-[#A97155]">
        Beneficios para Dueños de una o más mascotas
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <article class="rounded-xl bg-white p-5 shadow">
          <h4 class="text-lg font-semibold mb-1">Historial por mascota</h4>
          <p class="text-sm md:text-base leading-relaxed">Registra estudios, tratamientos y notas para cada mascota (perro, gato, loro…).</p>
        </article>
        <article class="rounded-xl bg-white p-5 shadow">
          <h4 class="text-lg font-semibold mb-1">Recordatorios</h4>
          <p class="text-sm md:text-base leading-relaxed">Alertas por email/WhatsApp para vacunas, baño o controles de Lolo y compañía.</p>
        </article>
        <article class="rounded-xl bg-white p-5 shadow">
          <h4 class="text-lg font-semibold mb-1">Mapa y calificaciones</h4>
          <p class="text-sm md:text-base leading-relaxed">Buscá prestadores en tu zona en toda Argentina y elegí por reputación.</p>
        </article>
      </div>
    </section>

    <!-- Beneficios Prestadores -->
    <section id="beneficios-prestadores" class="max-w-screen-xl mx-auto px-4 pb-8 md:pb-12">
      <h3 class="text-[clamp(1.25rem,4vw,1.75rem)] font-semibold mb-4 text-center underline underline-offset-4 decoration-2 decoration-[#A97155]">
        Beneficios para Prestadores
      </h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <article class="rounded-xl bg-white p-5 shadow">
          <h4 class="text-lg font-semibold mb-1">Presencia gratuita</h4>
          <p class="text-sm md:text-base leading-relaxed">Aparecé en listados y mapa por provincia/localidad para que te contacten.</p>
        </article>
        <article class="rounded-xl bg-white p-5 shadow">
          <h4 class="text-lg font-semibold mb-1">Reputación y alcance</h4>
          <p class="text-sm md:text-base leading-relaxed">Reseñas de clientes y posicionamiento para llegar a más dueños.</p>
        </article>
        <article class="rounded-xl bg-white p-5 shadow">
          <h4 class="text-lg font-semibold mb-1">Planes Pro</h4>
          <p class="text-sm md:text-base leading-relaxed">Destacado con fotos y publicación de avisos para promociones.</p>
        </article>
      </div>
    </section>

    <!-- CTA doble -->
    <section class="max-w-screen-md mx-auto px-4 pb-8 md:pb-12">
      <div class="rounded-2xl bg-white/95 backdrop-blur p-6 md:p-8 shadow text-center space-y-3">
        <h3 class="text-[clamp(1.25rem,4vw,1.75rem)] font-semibold">¿Te avisamos cuando lancemos?</h3>
        <p class="text-sm md:text-base leading-relaxed">Elegí tu perfil y dejános tu email.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
          <a href="#beneficios-duenos" class="min-h-[44px] inline-flex items-center justify-center rounded-lg bg-[#A97155] text-white px-6 py-3 hover:bg-[#8d5f47]">Soy Dueño</a>
          <a href="#beneficios-prestadores" class="min-h-[44px] inline-flex items-center justify-center rounded-lg bg-white text-[#A97155] border border-[#A97155]/30 px-6 py-3 hover:bg-[#fff2ea]">Soy Prestador</a>
        </div>
      </div>
    </section>

    <!-- Suscripción -->
    <section id="suscripcion" class="max-w-screen-md mx-auto px-4 pb-12">
      <div class="rounded-2xl bg-white/95 backdrop-blur p-6 md:p-8 shadow text-center">
        <h3 class="text-[clamp(1.25rem,4vw,1.75rem)] font-semibold mb-2">¿Querés enterarte cuando lancemos?</h3>
        <p class="text-sm md:text-base leading-relaxed mb-4">Dejanos tu email y te avisamos. Podés darte de baja cuando quieras.</p>
        <form class="space-y-4" method="post" action="guardar_suscripcion.php" onsubmit="return validateEmail()">
          <!-- Honeypot: campo oculto para frenar bots -->
          <div class="hidden" aria-hidden="true">
            <label for="hp_telefono">No completar este campo</label>
            <input id="hp_telefono" name="hp_telefono" type="text" autocomplete="off" tabindex="-1">
          </div>
          <div>
            <label for="nombre" class="block text-sm mb-1">Nombre (opcional)</label>
            <input id="nombre" name="nombre" type="text" class="w-full rounded-lg border border-amber-200 focus:border-amber-400 focus:ring-amber-300 px-4 py-3" placeholder="Tu nombre" />
          </div>
          <div>
            <label for="email" class="block text-sm mb-1">Email</label>
            <input id="email" name="email" type="email" inputmode="email" autocomplete="email" required class="w-full rounded-lg border border-amber-200 focus:border-amber-400 focus:ring-amber-300 px-4 py-3" placeholder="tu@email.com" />
          </div>
          <div>
            <span class="block text-sm mb-1">Perfil</span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left">
              <label class="flex items-center gap-3 rounded-lg border border-amber-200 px-4 py-3 cursor-pointer hover:border-amber-400">
                <input type="radio" name="perfil" value="dueno" class="text-[#A97155] focus:ring-[#A97155]" checked required>
                <span>Soy dueño/a de mascotas</span>
              </label>
              <label class="flex items-center gap-3 rounded-lg border border-amber-200 px-4 py-3 cursor-pointer hover:border-amber-400">
                <input type="radio" name="perfil" value="prestador" class="text-[#A97155] focus:ring-[#A97155]">
                <span>Soy prestador/a de servicios</span>
              </label>
            </div>
          </div>
          <label class="flex items-start gap-3 text-sm">
            <input type="checkbox" name="autorizacion" class="mt-1" checked>
            <span>Acepto recibir comunicaciones sobre el lanzamiento.</span>
          </label>
          <div class="pt-2">
            <button type="submit" class="w-full md:w-auto min-h-[44px] rounded-lg bg-[#A97155] text-white px-6 py-3 hover:bg-[#8d5f47]">Quiero enterarme</button>
          </div>
        </form>
      </div>
    </section>
  </main>

  <footer class="py-6 text-center text-sm opacity-80">
    © <?php echo date('Y'); ?> Mascotas y Mimos
  </footer>

  <script>
    function validateEmail(){
      const el = document.getElementById('email');
      if(!el.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value)){ el.focus(); return false; }
      return true;
    }
  </script>
</body>
</html>
