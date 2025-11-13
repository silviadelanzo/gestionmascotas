<?php
// Landing temporal (Tailwind) con carrusel y UI pastel, con paneles laterales mejorados

$imgDirFS = __DIR__ . '/assets/img/';
$imgDirURL = 'assets/img/';

$map = [
  'hero.png' => 'slide-hero',
  'beneficio1.png' => 'slide-beneficio1',
  'beneficio2.png' => 'slide-beneficio2',
  'relajacion.png' => 'slide-relajacion',
  'adopcion.png' => 'slide-adopcion',
  'belleza.png' => 'slide-belleza',
  'comunidad.png' => 'slide-comunidad',
  'veterinario.png' => 'slide-veterinario',
  'paseador.png' => 'slide-paseador',
  'footer.png' => 'slide-footer',
];

$slides = [];
foreach ($map as $file => $cls) {
  $path = $imgDirFS . $file;
  if (!file_exists($path) && $file === 'comunidad.png') {
    $alt = $imgDirFS . 'comunidad.png.png';
    if (file_exists($alt)) { $path = $alt; $file = 'comunidad.png.png'; }
  }
  if (file_exists($path)) {
    $slides[] = ['url' => $imgDirURL . $file, 'cls' => $cls];
  }
}
if (count($slides) < 3 && is_dir($imgDirFS)) {
  foreach (glob($imgDirFS . '*.png') as $p) {
    $slides[] = ['url' => $imgDirURL . basename($p), 'cls' => ''];
  }
}
// Helper paths for decorative images (prefer WebP)
$imgAgenda = file_exists($imgDirFS.'agenda_vacunas.webp') ? $imgDirURL.'agenda_vacunas.webp'
  : (file_exists($imgDirFS.'agenda_vacunas.jpg') ? $imgDirURL.'agenda_vacunas.jpg'
  : (file_exists($imgDirFS.'agenda_vacunas.png') ? $imgDirURL.'agenda_vacunas.png' : ''));
$imgRecordatorio = file_exists($imgDirFS.'recordatorio_mail.webp') ? $imgDirURL.'recordatorio_mail.webp'
  : (file_exists($imgDirFS.'recordatorio_mail..webp') ? $imgDirURL.'recordatorio_mail..webp'
  : (file_exists($imgDirFS.'recordatorio_mail.png') ? $imgDirURL.'recordatorio_mail.png' : ''));
$imgVetCons = file_exists($imgDirFS.'veterinario_consultorio.webp') ? $imgDirURL.'veterinario_consultorio.webp'
  : (file_exists($imgDirFS.'veterinario_consultorio.png') ? $imgDirURL.'veterinario_consultorio.png' : '');
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mascotas y Mimos | Cuidamos y mimamos a tus mejores amigos</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style_tailwind_overrides.css">
  <meta name="description" content="Plataforma para dueños y prestadores de servicios de mascotas. Gestioná turnos, servicios y cuidadores en un entorno cálido y seguro. Muy pronto disponible.">
  <meta name="keywords" content="mascotas, veterinaria, turnos, paseadores, cuidadores, gatos, perros, bienestar animal, adopción, peluquería canina">
  <meta name="author" content="Mascotas y Mimos">
  <meta name="robots" content="index,follow" />
  <link rel="canonical" href="https://mascotasymimos.com/" />
  <meta property="og:title" content="Mascotas y Mimos – Tu espacio de cuidado animal" />
  <meta property="og:description" content="Muy pronto la red que conecta dueños y profesionales del cuidado animal." />
  <meta property="og:image" content="assets/logo/logo.png" />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://mascotasymimos.com/" />
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Mascotas y Mimos",
    "url": "https://mascotasymimos.com",
    "logo": "assets/logo/logo.png",
    "description": "Plataforma integral para el cuidado y bienestar de mascotas.",
    "foundingDate": "2025",
    "contactPoint": [{
      "@type": "ContactPoint",
      "contactType": "Atención al cliente",
      "email": "info@mascotasymimos.com"
    }]
  }
  </script>
  <link rel="icon" type="image/png" href="assets/logo/logo.png" />
  <meta name="theme-color" content="#FFD6A5" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="format-detection" content="telephone=no" />
  <link rel="sitemap" type="application/xml" href="/sitemap.xml" />
  <meta name="robots" content="index, follow" />
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Mascotas y Mimos",
    "url": "https://mascotasymimos.com",
    "logo": "assets/logo/logo.png",
    "description": "Plataforma integral para el cuidado y bienestar de mascotas.",
    "foundingDate": "2025",
    "sameAs": [
      "https://www.instagram.com/mascotasymimos",
      "https://www.facebook.com/mascotasymimos"
    ],
    "contactPoint": [{
      "@type": "ContactPoint",
      "contactType": "Atención al cliente",
      "email": "info@mascotasymimos.com"
    }]
  }
  </script>
</head>
<body class="min-h-screen font-brand" style="background: linear-gradient(135deg,#FFD6A5 0%,#FAE0C3 100%);">
  <!-- Logo centrado arriba -->
  <div class="fixed top-4 left-1/2 -translate-x-1/2 z-30 logo-badge">
    <img src="assets/logo/logo.png" alt="Mascotas y Mimos" class="w-[150px] md:w-[180px] h-auto object-contain mb-5" />
  </div>

  <!-- Fondo carrusel -->
  <div class="absolute inset-0 -z-10 overflow-hidden">
    <div id="carousel" class="relative w-full h-full">
      <?php foreach ($slides as $i => $item): ?>
        <div class="carousel-slide <?php echo htmlspecialchars($item['cls']); ?> <?php echo $i===0?'active':''; ?>" style="background-image:url('<?php echo htmlspecialchars($item['url'], ENT_QUOTES); ?>');"></div>
      <?php endforeach; ?>
      <div class="absolute inset-0 bg-white/20"></div>
    </div>
  </div>

  <!-- Paneles laterales (responsivos) -->
  <div class="pointer-events-none absolute inset-0 flex flex-col md:flex-row items-start md:items-center justify-center md:justify-between gap-4 md:gap-0 px-[5%] pt-24 md:pt-0">
    <div class="pointer-events-auto panel-blur w-[260px] p-5 animate-fade-in">
      <h2 class="text-xl font-semibold mb-2" style="color:#6B3F24;">Para dueños</h2>
      <ul class="list-none text-left text-[1.1rem] font-medium leading-[1.6]" style="color:#5A3723;">
        <li><span style="color:#A97155;">•</span> Gestioná tus mascotas fácilmente.</li>
        <li><span style="color:#A97155;">•</span> Reservá turnos con tu veterinario de confianza.</li>
        <li><span style="color:#A97155;">•</span> Guardá su historial clínico y vacunas.</li>
        <li><span style="color:#A97155;">•</span> Encontrá cuidadores y paseadores cerca tuyo.</li>
        <li><span style="color:#A97155;">•</span> Buscá prestadores por zona con mapa interactivo de Google.</li>
      </ul>
      <div class="card-images">
        <?php if ($imgAgenda): ?>
          <img src="<?php echo htmlspecialchars($imgAgenda, ENT_QUOTES); ?>" alt="Agenda de vacunas para mascotas" loading="lazy" />
        <?php endif; ?>
        <?php if ($imgRecordatorio): ?>
          <img src="<?php echo htmlspecialchars($imgRecordatorio, ENT_QUOTES); ?>" alt="Recordatorio por correo para mascotas" loading="lazy" />
        <?php endif; ?>
      </div>
    </div>
    <div class="pointer-events-auto panel-blur w-[260px] p-5 animate-fade-in" style="animation-delay:.15s">
      <h2 class="text-xl font-semibold mb-2" style="color:#6B3F24;">Para prestadores</h2>
      <ul class="list-none text-left text-[1.1rem] font-medium leading-[1.6]" style="color:#5A3723;">
        <li><span style="color:#A97155;">•</span> Mostrá tus servicios y experiencia profesional.</li>
        <li><span style="color:#A97155;">•</span> Administrá tu agenda y recibí reservas online.</li>
        <li><span style="color:#A97155;">•</span> Gestioná tus pacientes y recordatorios automáticos.</li>
        <li><span style="color:#A97155;">•</span> Aumentá tu visibilidad en el mapa de prestadores.</li>
        <li><span style="color:#A97155;">•</span> Conectá con nuevos clientes y fidelizá los actuales.</li>
      </ul>
      <div class="card-images">
        <?php if ($imgVetCons): ?>
          <img src="<?php echo htmlspecialchars($imgVetCons, ENT_QUOTES); ?>" alt="Veterinario en su consultorio" loading="lazy" />
        <?php endif; ?>
        <div class="map-container">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3283.819657913306!2d-58.4500362847697!3d-34.610146280459665!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95bccb45c2b4f88b%3A0x5a3b9a4d6e9a5d19!2sVeterinaria!5e0!3m2!1ses-419!2sar!4v1700000000000!5m2!1ses-419!2sar"
            width="100%" height="250" style="border:0; border-radius:10px;" allowfullscreen="" loading="lazy">
          </iframe>
        </div>
      </div>
    </div>
  </div>

  <!-- Contenido central y formulario en tercio inferior -->
  <main class="relative min-h-screen flex flex-col">
    <div class="flex-1 flex flex-col items-center justify-center text-center px-[5%] pt-32 mt-6">
      <h1 class="text-[2.2rem] font-semibold" style="color:#6B3F24;">Sitio en construcción</h1>
      <p class="text-[1.25rem] font-semibold" style="color:#A97155;">Muy pronto Mascotas y Mimos</p>
    </div>

    <div class="pb-12 flex items-end justify-center">
      <form class="w-full max-w-md rounded-2xl p-5 shadow-soft mb-2 border border-brand" style="background-color:#ffffff80;backdrop-filter:blur(8px);" method="post" action="guardar_suscripcion.php" onsubmit="return validateEmail();">
        <p class="text-[1.05rem] mb-2 font-semibold" style="color:#6B3F24;">Si sos Dueño o Prestador, suscribite para mantenerte informado.</p>
        <div class="mb-3 text-left">
          <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre (opcional)</label>
          <input id="nombre" name="nombre" type="text" class="mt-1 w-full rounded-md border border-brand focus:border-[#8B5E3C] focus:ring-[#8B5E3C]" />
        </div>
        <div class="mb-3 text-left">
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input id="email" name="email" type="email" required class="mt-1 w-full rounded-md border border-brand focus:border-[#8B5E3C] focus:ring-[#8B5E3C]" />
        </div>
        <div class="mb-4 flex items-start text-left">
          <input id="autorizacion" name="autorizacion" type="checkbox" class="mt-1 mr-2" checked />
          <label for="autorizacion" class="text-sm text-gray-700">Autorizo a recibir información y novedades de Mascotas y Mimos</label>
        </div>
        <button type="submit" class="btn-sub w-full py-2.5 rounded-md text-white font-medium transition">Suscribirme</button>
        <p class="mt-3 text-[11px] leading-snug text-gray-700">Tus datos se almacenan conforme a la Ley 25.326 de Protección de Datos Personales.</p>
      </form>
    </div>
  </main>

  <footer class="text-center text-brand pb-4 text-[0.9rem]">© 2025 Mascotas y Mimos. Todos los derechos reservados.</footer>

  <script>
    // Carrusel con fade entre background slides
    const slides = Array.from(document.querySelectorAll('#carousel .carousel-slide'));
    let idx = 0;
    setInterval(() => {
      if (slides.length === 0) return;
      slides[idx].classList.remove('active');
      idx = (idx + 1) % slides.length;
      slides[idx].classList.add('active');
    }, 6000);

    function validateEmail() {
      const email = document.getElementById('email').value.trim();
      const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      if (!ok) { alert('Por favor, ingresa un email válido.'); }
      return ok;
    }
  </script>
</body>
</html>
