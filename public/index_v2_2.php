<?php
require __DIR__ . '/includes/bootstrap.php';

$baseUrl = app_base_url();
$isLogged = !empty($_SESSION['uid']);
$role = $_SESSION['rol'] &#128100; null;
$launchUrl = $role === 'prestador'
$registroDueno = $baseUrl . '/registro.php?role=dueno';
$registroPrestador = $baseUrl . '/registro.php?role=prestador';
$loginUrl = $baseUrl . '/login.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mascotas y Mimos Â· Agenda digital</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root {
      --brand: #A97155;
      --brand-2: #7A9E7E;
      --dark: #2B1D18;
    }
    body {
      margin: 0;
      font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', sans-serif;
      color: #f8f7f4;
      background: #0f0c0c;
    }
    .video-bg {
      position: fixed;
      inset: 0;
      z-index: -2;
      overflow: hidden;
    }
    .video-bg video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      filter: brightness(0.85);
    }
    .overlay {
      position: fixed;
      inset: 0;
      background: radial-gradient(circle at 20% 20%, rgba(169, 113, 85, 0.25), transparent 35%),
                  radial-gradient(circle at 80% 0%, rgba(122, 158, 126, 0.2), transparent 30%),
                  linear-gradient(120deg, rgba(15, 12, 12, 0.5), rgba(15, 12, 12, 0.4));
      z-index: -1;
    }
    .glass {
      background: rgba(24, 18, 16, 0.55);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .pill {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      padding: 0.7rem 1.1rem;
      border-radius: 999px;
      font-weight: 600;
      font-size: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.15);
      transition: transform 0.15s ease, box-shadow 0.2s ease;
    }
    .pill:hover { transform: translateY(-1px); }
    .btn-primary {
      background: linear-gradient(135deg, var(--brand), #c78867);
      color: #fff;
      box-shadow: 0 10px 30px rgba(169, 113, 85, 0.35);
    }
    .btn-secondary {
      background: rgba(255, 255, 255, 0.08);
      color: #f2e9e4;
    }
    .btn-brown {
      background: linear-gradient(135deg, var(--brand), #8d5f47);
      color: #fff;
      box-shadow: 0 15px 30px rgba(169, 113, 85, 0.45);
    }
    .section {
      padding: clamp(2rem, 4vw, 3.5rem) 1rem;
    }
  </style>
</head>
<body>
  <div class="video-bg">
    <video src="assets/videos/FondoBosque.mp4" autoplay loop muted playsinline poster="assets/img/hero.webp"></video>
  </div>
  <div class="overlay"></div>

  <header class="sticky top-0 z-20">
    <div class="glass mx-auto max-w-6xl px-4 py-3 rounded-2xl mt-3 flex items-center justify-between gap-3">
      <div class="flex items-center gap-3">
        <img src="assets/logo/logo.png" alt="Mascotas y Mimos" class="w-10 h-10 rounded-full bg-white/80 p-1">
        <div class="text-sm leading-tight">
          <div class="font-semibold text-white">Mascotas y Mimos</div>
          <div class="text-white/70">Agenda digital para familias y prestadores</div>
        </div>
      </div>
      <nav class="hidden md:flex items-center gap-3 text-white/85 text-sm">
        <a href="#duenos" class="hover:text-white">DueÃ±os</a>
        <a href="#prestadores" class="hover:text-white">Prestadores</a>
        <a href="#como-funciona" class="hover:text-white">Cómo funciona</a>
      </nav>
      <div class="flex items-center gap-2">
        <a class="pill btn-secondary" aria-label="Ingresar a mi cuenta" title="Ingresar a mi cuenta" href="<?= htmlspecialchars($isLogged ? $launchUrl : $loginUrl, ENT_QUOTES, 'UTF-8') ?>">&#128100;</a>
      </div>

      </div>
    </div>
  </header>

  <main class="text-white">
    <!-- Hero -->
    <section class="section pt-10 md:pt-16">
      <div class="max-w-3xl mx-auto">
        <div class="glass rounded-3xl p-6 md:p-8 shadow-2xl text-center">
          <h1 class="text-3xl md:text-4xl font-bold leading-tight mb-3">
            Un solo lugar para dueÃ±os y prestadores de mascotas.
          </h1>
          <p class="text-white/80 text-base md:text-lg mb-6">
            Gratis para dueÃ±os (agenda y recordatorios). Planes escalables para veterinarias y prestadores que quieren visibilidad y organizaciÃ³n.
          </p>
          <div class="flex flex-wrap gap-3 justify-center">

            <a class="pill btn-brown" href="<?= htmlspecialchars($registroDueno, ENT_QUOTES, 'UTF-8') ?>">Crear cuenta dueÃ±o/a</a>
            <a class="pill btn-brown" href="<?= htmlspecialchars($registroPrestador, ENT_QUOTES, 'UTF-8') ?>">Crear cuenta prestador/a</a>
            <?php if ($isLogged): ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- DueÃ±os -->
    <section id="duenos" class="section">
      <div class="max-w-6xl mx-auto glass rounded-3xl p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
          <div class="w-full text-center md:text-left">
            <p class="badge mb-2">Para dueÃ±os</p>
            <h2 class="text-2xl md:text-3xl font-bold">Agenda y tranquilidad para tus mascotas</h2>
            <p class="text-white/75 mt-2">Recordatorios, documentos y contactos en un solo lugar.</p>
          </div>
        </div>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          <?php
          $duenoFeatures = [
            ['title' => 'Recordatorios automÃ¡ticos', 'desc' => 'Vacunas, turnos y tratamientos con alertas por email.', 'img' => 'assets/img/recordatorio_mail..webp'],
            ['title' => 'Historial por mascota', 'desc' => 'Vacunas, cirugÃ­as, alergias y notas siempre accesibles.', 'img' => 'assets/img/agenda_vacunas.webp'],
            ['title' => 'Documentos en la nube', 'desc' => 'Carnets, estudios y recetas en PDF sin perderlos.', 'img' => 'assets/img/veterinario_consultorio.webp'],
            ['title' => 'Contactos de confianza', 'desc' => 'Veterinaria, paseador, guarderÃ­a y emergencias a un clic.', 'img' => 'assets/img/mapa_prestadores.webp'],
          ];
          foreach ($duenoFeatures as $f): ?>
            <article class="glass rounded-2xl p-4">
              <div class="mb-2">
                <img src="<?= htmlspecialchars($f['img'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($f['title'], ENT_QUOTES, 'UTF-8') ?>" class="w-full aspect-[4/3] object-cover rounded-lg" onerror="this.src='assets/img/hero.png'">
              </div>
              <h3 class="font-semibold mb-1"><?= htmlspecialchars($f['title'], ENT_QUOTES, 'UTF-8') ?></h3>
              <p class="text-white/75 text-sm"><?= htmlspecialchars($f['desc'], ENT_QUOTES, 'UTF-8') ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Prestadores -->
    <section id="prestadores" class="section">
      <div class="max-w-6xl mx-auto glass rounded-3xl p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
          <div class="w-full text-center md:text-left">
            <p class="badge mb-2">Para prestadores</p>
            <h2 class="text-2xl md:text-3xl font-bold">Visibilidad y organizaciÃ³n en el mismo lugar</h2>
            <p class="text-white/75 mt-2">Tu ficha, servicios y reservas listos para dueÃ±os que ya usan la agenda.</p>
          </div>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
          <?php
          $prestador = [
            ['title' => 'Ficha visible en mapas/listados', 'desc' => 'Tus datos y servicios frente a dueÃ±os organizados.', 'img' => 'assets/img/mapa_prestadores.webp'],
            ['title' => 'Recetas y reservas', 'desc' => 'Recetas PDF y prÃ³ximos turnos en un mismo flujo.', 'img' => 'assets/img/veterinario_consultorio.webp'],
            ['title' => 'EstadÃ­sticas bÃ¡sicas', 'desc' => 'Vistas y clics a WhatsApp para medir resultados.', 'img' => 'assets/img/recordatorio_mail..webp'],
          ];
          foreach ($prestador as $f): ?>
            <article class="glass rounded-2xl p-4">
              <div class="mb-2">
                <img src="<?= htmlspecialchars($f['img'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($f['title'], ENT_QUOTES, 'UTF-8') ?>" class="w-full aspect-[4/3] object-cover rounded-lg" onerror="this.src='assets/img/hero.png'">
              </div>
              <h3 class="font-semibold mb-1"><?= htmlspecialchars($f['title'], ENT_QUOTES, 'UTF-8') ?></h3>
              <p class="text-white/75 text-sm"><?= htmlspecialchars($f['desc'], ENT_QUOTES, 'UTF-8') ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- CÃ³mo funciona -->
    <section id="como-funciona" class="section pb-16">
      <div class="max-w-6xl mx-auto glass rounded-3xl p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
          <div>
            <p class="badge mb-2">Flujo simple</p>
            <h2 class="text-2xl md:text-3xl font-bold">CÃ³mo funciona para cada rol</h2>
          </div>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <div class="glass rounded-2xl p-4">
            <h3 class="font-semibold mb-2">Si sos dueÃ±o/a</h3>
            <ol class="list-decimal list-inside space-y-1 text-white/80 text-sm">
              <li>Creamos tu cuenta como dueÃ±o y agregÃ¡s tus mascotas.</li>
              <li>GuardÃ¡s historial, vacunas y documentos.</li>
              <li>RecibÃ­s recordatorios y accedÃ©s a prestadores cercanos.</li>
            </ol>
          </div>
          <div class="glass rounded-2xl p-4">
            <h3 class="font-semibold mb-2">Si sos prestador/a</h3>
            <ol class="list-decimal list-inside space-y-1 text-white/80 text-sm">
              <li>CreÃ¡s tu ficha y cargÃ¡s tus servicios.</li>
              <li>RecibÃ­s consultas y generÃ¡s recetas/turnos.</li>
              <li>Ves mÃ©tricas bÃ¡sicas y mejoras visibilidad.</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
  </main>
</body>
</html>

