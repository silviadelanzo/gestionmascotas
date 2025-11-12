<?php
// Temporary landing page with logo, carousel, and subscription form
// Tailwind via CDN; images from assets/img

$imgBase = __DIR__ . '/assets/img/';
$urlBase = 'assets/img/';
$candidates = [
  'hero.png','beneficio1.png','beneficio2.png','footer.png','adopcion.png','belleza.png','relajacion.png','comunidad.png','veterinario.png','paseador.png'
];
$slides = [];
foreach ($candidates as $f) {
  $p = $imgBase . $f;
  if (!file_exists($p) && $f === 'comunidad.png' && file_exists($imgBase . 'comunidad.png.png')) {
    $p = $imgBase . 'comunidad.png.png';
    $f = 'comunidad.png.png';
  }
  if (file_exists($p)) { $slides[] = $urlBase . $f; }
}
// Fallback to any pngs if fewer than 3 slides
if (count($slides) < 3 && is_dir($imgBase)) {
  foreach (glob($imgBase . '*.png') as $any) {
    $slides[] = $urlBase . basename($any);
  }
  $slides = array_unique($slides);
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mascotas y Mimos — Sitio en construcción</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <meta name="robots" content="noindex,nofollow" />
  <style>
    .fade-slide { position:absolute; inset:0; opacity:0; transition: opacity 1000ms ease-in-out; }
    .fade-slide.active { opacity: 1; }
  </style>
  <link rel="icon" type="image/png" href="assets/logo/logo.png" />
  <meta name="description" content="Sitio en construcción – Muy pronto Mascotas y Mimos" />
</head>
<body class="min-h-screen">
  <div class="min-h-screen relative overflow-hidden" style="background: linear-gradient(135deg,#FFD6A5 0%,#FAE0C3 100%);">
    <!-- Carousel container -->
    <div class="absolute inset-0">
      <div id="carousel" class="w-full h-full relative">
        <?php foreach ($slides as $idx => $src): ?>
          <img src="<?php echo htmlspecialchars($src, ENT_QUOTES); ?>" alt="slide" class="fade-slide object-cover w-full h-full <?php echo $idx===0?'active':''; ?>">
        <?php endforeach; ?>
      </div>
      <div class="absolute inset-0 bg-white/10"></div>
    </div>

    <!-- Centered content -->
    <div class="relative z-10 flex flex-col items-center justify-center text-center min-h-screen px-4">
      <img src="assets/logo/logo.png" alt="Mascotas y Mimos" class="w-44 h-44 object-contain mb-6 drop-shadow"/>
      <h1 class="text-2xl md:text-3xl font-semibold text-gray-800 mb-2">Sitio en construcción</h1>
      <p class="text-lg md:text-xl text-gray-700 mb-8">Muy pronto Mascotas y Mimos</p>

      <!-- Subscription form -->
      <form class="w-full max-w-md bg-white/80 backdrop-blur rounded-xl p-5 shadow" method="post" action="guardar_suscripcion.php" onsubmit="return validateEmail();">
        <div class="mb-3 text-left">
          <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre (opcional)</label>
          <input id="nombre" name="nombre" type="text" class="mt-1 w-full rounded-md border-gray-300 focus:border-orange-400 focus:ring-orange-400" />
        </div>
        <div class="mb-3 text-left">
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input id="email" name="email" type="email" required class="mt-1 w-full rounded-md border-gray-300 focus:border-orange-400 focus:ring-orange-400" />
        </div>
        <div class="mb-4 flex items-start text-left">
          <input id="autorizacion" name="autorizacion" type="checkbox" class="mt-1 mr-2" checked />
          <label for="autorizacion" class="text-sm text-gray-700">Autorizo a recibir información y novedades de Mascotas y Mimos</label>
        </div>
        <button type="submit" class="w-full py-2.5 rounded-md bg-orange-400 hover:bg-orange-500 text-white font-medium">Suscribirme</button>
      </form>

      <p class="mt-6 text-xs text-gray-600">© <?php echo date('Y'); ?> Mascotas y Mimos</p>
    </div>
  </div>

  <script>
    // Simple fade carousel
    const slides = Array.from(document.querySelectorAll('#carousel .fade-slide'));
    let idx = 0;
    setInterval(() => {
      if (slides.length === 0) return;
      slides[idx].classList.remove('active');
      idx = (idx + 1) % slides.length;
      slides[idx].classList.add('active');
    }, 5000);

    function validateEmail() {
      const email = document.getElementById('email').value.trim();
      const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      if (!ok) { alert('Por favor, ingresa un email válido.'); }
      return ok;
    }
  </script>
</body>
</html>

