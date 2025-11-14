<?php
// Landing responsive (mobile-first) para pruebas sin tocar index.php
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mascotas y Mimos – Prueba Responsive</title>
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
          <div>
            <h2 class="text-[clamp(1.5rem,5vw,2.25rem)] leading-tight font-bold mb-3">Cuidamos y mimamos a tus mejores amigos</h2>
            <p class="text-base md:text-lg leading-relaxed">Pronto vas a poder gestionar turnos, descubrir prestadores y organizar el cuidado de tus mascotas desde un solo lugar.</p>
          </div>
          <div>
            <img src="assets/img/hero.webp" onerror="this.src='assets/img/hero.png'" alt="Ilustración mascotas" class="w-full aspect-[4/3] object-cover rounded-xl shadow" />
          </div>
        </div>
      </div>
    </section>

    <!-- Tarjetas / beneficios -->
    <section class="max-w-screen-xl mx-auto px-4 pb-8 md:pb-12">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <article class="rounded-xl bg-white p-5 shadow">
          <img src="assets/img/beneficio1.webp" onerror="this.src='assets/img/beneficio1.png'" alt="Agenda" class="w-full aspect-[4/3] object-cover rounded-lg mb-3" />
          <h3 class="text-lg font-semibold mb-1">Agenda fácil</h3>
          <p class="text-sm md:text-base leading-relaxed">Recordatorios y turnos para que nada se te pase.</p>
        </article>
        <article class="rounded-xl bg-white p-5 shadow">
          <img src="assets/img/beneficio2.webp" onerror="this.src='assets/img/beneficio2.png'" alt="Prestadores" class="w-full aspect-[4/3] object-cover rounded-lg mb-3" />
          <h3 class="text-lg font-semibold mb-1">Prestadores cerca</h3>
          <p class="text-sm md:text-base leading-relaxed">Encontrá veterinarias, paseadores y cuidadores.</p>
        </article>
        <article class="rounded-xl bg-white p-5 shadow">
          <img src="assets/img/comunidad.png.webp" onerror="this.src='assets/img/comunidad.png.png'" alt="Comunidad" class="w-full aspect-[4/3] object-cover rounded-lg mb-3" />
          <h3 class="text-lg font-semibold mb-1">Comunidad</h3>
          <p class="text-sm md:text-base leading-relaxed">Consejos y bienestar para tus compañeros de vida.</p>
        </article>
      </div>
    </section>

    <!-- Suscripción -->
    <section class="max-w-screen-md mx-auto px-4 pb-12">
      <div class="rounded-2xl bg-white/95 backdrop-blur p-6 md:p-8 shadow">
        <h3 class="text-[clamp(1.25rem,4vw,1.75rem)] font-semibold mb-2">¿Querés enterarte cuando lancemos?</h3>
        <p class="text-sm md:text-base leading-relaxed mb-4">Dejanos tu email y te avisamos. Podés darte de baja cuando quieras.</p>
        <form class="space-y-4" method="post" action="guardar_suscripcion.php" onsubmit="return validateEmail()">
          <div>
            <label for="nombre" class="block text-sm mb-1">Nombre (opcional)</label>
            <input id="nombre" name="nombre" type="text" class="w-full rounded-lg border border-amber-200 focus:border-amber-400 focus:ring-amber-300 px-4 py-3" placeholder="Tu nombre" />
          </div>
          <div>
            <label for="email" class="block text-sm mb-1">Email</label>
            <input id="email" name="email" type="email" inputmode="email" autocomplete="email" required class="w-full rounded-lg border border-amber-200 focus:border-amber-400 focus:ring-amber-300 px-4 py-3" placeholder="tu@email.com" />
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

