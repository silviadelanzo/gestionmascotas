<?php
require __DIR__ . '/includes/bootstrap.php';

$baseUrl = app_base_url();
$isLogged = !empty($_SESSION['uid']);
$role = $_SESSION['rol'] ?? 'dueno';
$launchUrl = $role === 'prestador'
  ? $baseUrl . '/launchpad_prestador.php'
  : $baseUrl . '/launchpad_dueno.php';
$profileUrl = $launchUrl; // placeholder a perfil/launchpad actual
$registroDueno = $baseUrl . '/registro.php?role=dueno';
$registroPrestador = $baseUrl . '/registro.php?role=prestador';
$loginUrl = $baseUrl . '/login.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mascotas y Mimos · Agenda digital</title>
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
      filter: brightness(0.65);
    }
    .overlay {
      position: fixed;
      inset: 0;
      background: radial-gradient(circle at 20% 20%, rgba(169, 113, 85, 0.25), transparent 35%),
                  radial-gradient(circle at 80% 0%, rgba(122, 158, 126, 0.2), transparent 30%),
                  linear-gradient(120deg, rgba(15, 12, 12, 0.75), rgba(15, 12, 12, 0.6));
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
      padding: 0.55rem 0.95rem;
      border-radius: 999px;
      font-weight: 600;
      font-size: 0.95rem;
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
    .section {
      padding: clamp(2rem, 4vw, 3.5rem) 1rem;
    }
    .badge {
      display: inline-block;
      padding: 0.35rem 0.75rem;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.12);
      font-size: 0.85rem;
      letter-spacing: 0.02em;
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
        <a href="#duenos" class="hover:text-white">Dueños</a>
        <a href="#prestadores" class="hover:text-white">Prestadores</a>
        <a href="#como-funciona" class="hover:text-white">Cómo funciona</a>
        <a href="#accesos" class="hover:text-white">Accesos rápidos</a>
      </nav>
      <div class="flex items-center gap-2">
        <?php if ($isLogged): ?>
          <a class="pill btn-secondary" href="<?= htmlspecialchars($profileUrl, ENT_QUOTES, 'UTF-8') ?>">Mi cuenta</a>
          <a class="pill btn-primary" href="<?= htmlspecialchars($launchUrl, ENT_QUOTES, 'UTF-8') ?>">Ir al launchpad</a>
        <?php else: ?>
          <a class="pill btn-secondary" href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Iniciar sesión</a>
          <a class="pill btn-primary" href="<?= htmlspecialchars($registroDueno, ENT_QUOTES, 'UTF-8') ?>">Crear cuenta</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <main class="text-white">
    <!-- Hero -->
    <section class="section pt-10 md:pt-16">
      <div class="max-w-6xl mx-auto grid gap-8 md:grid-cols-[1.1fr,0.9fr] items-center">
        <div class="glass rounded-3xl p-6 md:p-8 shadow-2xl">
          <div class="badge mb-4">Video en vivo · Fondo natural</div>
          <h1 class="text-3xl md:text-4xl font-bold leading-tight mb-3">
            Organiza la salud de tus mascotas y potencia tu visibilidad como prestador.
          </h1>
          <p class="text-white/80 text-base md:text-lg mb-6">
            Recordatorios, documentos y contactos en un solo lugar. Gratis para dueños, planes escalables para veterinarias y prestadores.
          </p>
          <div class="flex flex-wrap gap-3">
            <?php if ($isLogged): ?>
              <a class="pill btn-primary" href="<?= htmlspecialchars($launchUrl, ENT_QUOTES, 'UTF-8') ?>">Ir al launchpad</a>
              <a class="pill btn-secondary" href="<?= htmlspecialchars($profileUrl, ENT_QUOTES, 'UTF-8') ?>">Ver mi perfil</a>
            <?php else: ?>
              <a class="pill btn-primary" href="<?= htmlspecialchars($registroDueno, ENT_QUOTES, 'UTF-8') ?>">Soy dueño/a</a>
              <a class="pill btn-secondary" href="<?= htmlspecialchars($registroPrestador, ENT_QUOTES, 'UTF-8') ?>">Soy prestador/a</a>
              <a class="pill btn-secondary" href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Ya tengo cuenta</a>
            <?php endif; ?>
          </div>
        </div>
        <div class="grid gap-3">
          <div class="glass rounded-3xl p-4 md:p-5">
            <div class="flex items-center justify-between mb-3">
              <span class="text-white font-semibold">Accesos rápidos</span>
              <span class="text-xs text-white/60">Según tu rol</span>
            </div>
            <div id="accesos" class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
              <a class="glass rounded-2xl p-3 hover:bg-white/10 transition" href="<?= htmlspecialchars($launchUrl, ENT_QUOTES, 'UTF-8') ?>">
                <div class="font-semibold">Launchpad</div>
                <div class="text-white/70">Inicio rápido</div>
              </a>
              <a class="glass rounded-2xl p-3 hover:bg-white/10 transition" href="<?= htmlspecialchars($baseUrl . '/registro.php', ENT_QUOTES, 'UTF-8') ?>">
                <div class="font-semibold">Registrarme</div>
                <div class="text-white/70">Crear mi cuenta</div>
              </a>
              <a class="glass rounded-2xl p-3 hover:bg-white/10 transition" href="<?= htmlspecialchars($baseUrl . '/mapa_prestadores.php', ENT_QUOTES, 'UTF-8') ?>">
                <div class="font-semibold">Mapa</div>
                <div class="text-white/70">Prestadores cercanos</div>
              </a>
              <a class="glass rounded-2xl p-3 hover:bg-white/10 transition" href="<?= htmlspecialchars($baseUrl . '/registro.php?role=dueno', ENT_QUOTES, 'UTF-8') ?>">
                <div class="font-semibold">Soy dueño</div>
                <div class="text-white/70">Agenda, vacunas</div>
              </a>
              <a class="glass rounded-2xl p-3 hover:bg-white/10 transition" href="<?= htmlspecialchars($baseUrl . '/registro.php?role=prestador', ENT_QUOTES, 'UTF-8') ?>">
                <div class="font-semibold">Soy prestador</div>
                <div class="text-white/70">Ficha y servicios</div>
              </a>
              <a class="glass rounded-2xl p-3 hover:bg-white/10 transition" href="<?= htmlspecialchars($baseUrl . '/login.php', ENT_QUOTES, 'UTF-8') ?>">
                <div class="font-semibold">Ingresar</div>
                <div class="text-white/70">Con mi cuenta</div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Dueños -->
    <section id="duenos" class="section">
      <div class="max-w-6xl mx-auto glass rounded-3xl p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
          <div>
            <p class="badge mb-2">Para dueños</p>
            <h2 class="text-2xl md:text-3xl font-bold">Agenda y tranquilidad para tus mascotas</h2>
            <p class="text-white/75 mt-2">Recordatorios, documentos y contactos en un solo lugar.</p>
          </div>
          <a class="pill btn-primary" href="<?= htmlspecialchars($registroDueno, ENT_QUOTES, 'UTF-8') ?>">Crear cuenta de dueño</a>
        </div>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          <?php
          $duenoFeatures = [
            ['title' => 'Recordatorios automáticos', 'desc' => 'Vacunas, turnos y tratamientos con alertas por email.'],
            ['title' => 'Historial por mascota', 'desc' => 'Vacunas, cirugías, alergias y notas siempre accesibles.'],
            ['title' => 'Documentos en la nube', 'desc' => 'Carnets, estudios y recetas en PDF sin perderlos.'],
            ['title' => 'Contactos de confianza', 'desc' => 'Veterinaria, paseador, guardería y emergencias a un clic.'],
          ];
          foreach ($duenoFeatures as $f): ?>
            <article class="glass rounded-2xl p-4">
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
          <div>
            <p class="badge mb-2">Para prestadores</p>
            <h2 class="text-2xl md:text-3xl font-bold">Visibilidad y organización en el mismo lugar</h2>
            <p class="text-white/75 mt-2">Tu ficha, servicios y reservas listos para dueños que ya usan la agenda.</p>
          </div>
          <a class="pill btn-secondary" href="<?= htmlspecialchars($registroPrestador, ENT_QUOTES, 'UTF-8') ?>">Crear cuenta de prestador</a>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
          <?php
          $prestador = [
            ['title' => 'Ficha visible en mapas/listados', 'desc' => 'Tus datos y servicios frente a dueños organizados.'],
            ['title' => 'Recetas y reservas', 'desc' => 'Recetas PDF y próximos turnos en un mismo flujo.'],
            ['title' => 'Estadísticas básicas', 'desc' => 'Vistas y clics a WhatsApp para medir resultados.'],
          ];
          foreach ($prestador as $f): ?>
            <article class="glass rounded-2xl p-4">
              <h3 class="font-semibold mb-1"><?= htmlspecialchars($f['title'], ENT_QUOTES, 'UTF-8') ?></h3>
              <p class="text-white/75 text-sm"><?= htmlspecialchars($f['desc'], ENT_QUOTES, 'UTF-8') ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Cómo funciona -->
    <section id="como-funciona" class="section pb-16">
      <div class="max-w-6xl mx-auto glass rounded-3xl p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
          <div>
            <p class="badge mb-2">Flujo simple</p>
            <h2 class="text-2xl md:text-3xl font-bold">Cómo funciona para cada rol</h2>
          </div>
          <a class="pill btn-secondary" href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Ingresar</a>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <div class="glass rounded-2xl p-4">
            <h3 class="font-semibold mb-2">Si sos dueño/a</h3>
            <ol class="list-decimal list-inside space-y-1 text-white/80 text-sm">
              <li>Creamos tu cuenta como dueño y agregás tus mascotas.</li>
              <li>Guardás historial, vacunas y documentos.</li>
              <li>Recibís recordatorios y accedés a prestadores cercanos.</li>
            </ol>
          </div>
          <div class="glass rounded-2xl p-4">
            <h3 class="font-semibold mb-2">Si sos prestador/a</h3>
            <ol class="list-decimal list-inside space-y-1 text-white/80 text-sm">
              <li>Creás tu ficha y cargás tus servicios.</li>
              <li>Recibís consultas y generás recetas/turnos.</li>
              <li>Ves métricas básicas y mejoras visibilidad.</li>
            </ol>
          </div>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
