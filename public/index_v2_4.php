<?php
require __DIR__ . '/includes/bootstrap.php';

$baseUrl = app_base_url();
$isLogged = !empty($_SESSION['uid']);
$role = $_SESSION['rol'] ?? null;
$launchUrl = $role === 'prestador'
  ? $baseUrl . '/launchpad_prestador.php'
  : $baseUrl . '/launchpad_dueno.php';
$profileUrl = $launchUrl;
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
      filter: brightness(0.85);
    }
    .overlay {
      position: fixed;
      inset: 0;
      background: radial-gradient(circle at 20% 20%, rgba(169, 113, 85, 0.15), transparent 40%),
                  radial-gradient(circle at 80% 0%, rgba(122, 158, 126, 0.12), transparent 35%),
                  linear-gradient(120deg, rgba(15, 12, 12, 0.3), rgba(15, 12, 12, 0.25));
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
    .badge {
      display: inline-block;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--brand);
      background: rgba(169, 113, 85, 0.15);
      padding: 0.25rem 0.75rem;
      border-radius: 999px;
      font-weight: 600;
    }
    .feature-img {
      width: 100%;
      height: 8rem;
      object-fit: cover;
      border-radius: 0.75rem;
      margin-bottom: 0.75rem;
    }
    /* Menú de navegación más grande y visible */
    .nav-link {
      font-size: 1rem;
      font-weight: 500;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      transition: all 0.2s ease;
    }
    .nav-link:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff !important;
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
      <nav class="hidden md:flex items-center gap-2 text-white/85">
        <a href="#duenos" class="nav-link">Dueños</a>
        <a href="#prestadores" class="nav-link">Prestadores</a>
        <a href="#como-funciona" class="nav-link">Cómo funciona</a>
      </nav>
      <div class="flex items-center gap-2">
        <?php if ($isLogged): ?>
          <a class="pill btn-secondary" href="<?= htmlspecialchars($launchUrl, ENT_QUOTES, 'UTF-8') ?>">Launchpad</a>
        <?php endif; ?>
        <a class="pill btn-primary" href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>">Ingresar a mi cuenta</a>
      </div>
    </div>
  </header>

  <main class="text-white">
    <!-- Hero -->
    <section class="section pt-10 md:pt-16">
      <div class="max-w-6xl mx-auto">
        <div class="glass rounded-3xl p-6 md:p-8 shadow-2xl text-center">
          <h1 class="text-3xl md:text-4xl font-bold leading-tight mb-3">
            Un solo lugar para dueños y prestadores de mascotas.
          </h1>
          <p class="text-white/80 text-base md:text-lg mb-6">
            Gratis para dueños (agenda y recordatorios). Planes escalables para veterinarias y prestadores que quieren visibilidad y organización.
          </p>
          <div class="flex flex-wrap gap-3 justify-center">
            <a class="pill btn-brown" href="<?= htmlspecialchars($registroDueno, ENT_QUOTES, 'UTF-8') ?>">Crear cuenta dueño/a</a>
            <a class="pill btn-brown" href="<?= htmlspecialchars($registroPrestador, ENT_QUOTES, 'UTF-8') ?>">Crear cuenta prestador/a</a>
            <?php if ($isLogged): ?>
              <a class="pill btn-secondary" href="<?= htmlspecialchars($launchUrl, ENT_QUOTES, 'UTF-8') ?>">Ir al launchpad</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>

    <!-- Dueños -->
    <section id="duenos" class="section">
      <div class="max-w-6xl mx-auto glass rounded-3xl p-6 md:p-8">
        <div class="text-center mb-6">
          <p class="badge mb-2">Para dueños</p>
          <h2 class="text-2xl md:text-3xl font-bold">Agenda y tranquilidad para tus mascotas</h2>
          <p class="text-white/75 mt-2">Recordatorios, documentos y contactos en un solo lugar.</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          <article class="glass rounded-2xl p-4 text-center">
            <img src="assets/img/recordatorio_mail..webp" onerror="this.src='assets/img/recordatorio_mail..png'" alt="Recordatorios automáticos" class="feature-img" />
            <h3 class="font-semibold mb-1">Recordatorios automáticos</h3>
            <p class="text-white/75 text-sm">Vacunas, turnos y tratamientos con alertas por email.</p>
          </article>
          <article class="glass rounded-2xl p-4 text-center">
            <img src="assets/img/agenda_vacunas.webp" onerror="this.src='assets/img/agenda_vacunas.jpg'" alt="Historial por mascota" class="feature-img" />
            <h3 class="font-semibold mb-1">Historial por mascota</h3>
            <p class="text-white/75 text-sm">Vacunas, cirugías, alergias y notas siempre accesibles.</p>
          </article>
          <article class="glass rounded-2xl p-4 text-center">
            <img src="assets/img/veterinario_consultorio.webp" onerror="this.src='assets/img/veterinario_consultorio.png'" alt="Documentos en la nube" class="feature-img" />
            <h3 class="font-semibold mb-1">Documentos en la nube</h3>
            <p class="text-white/75 text-sm">Carnets, estudios y recetas en PDF sin perderlos.</p>
          </article>
          <article class="glass rounded-2xl p-4 text-center">
            <img src="assets/img/mapa_prestadores.webp" onerror="this.src='assets/img/mapa_prestadores.png'" alt="Contactos de confianza" class="feature-img" />
            <h3 class="font-semibold mb-1">Contactos de confianza</h3>
            <p class="text-white/75 text-sm">Veterinaria, paseador, guardería y emergencias a un clic.</p>
          </article>
        </div>
      </div>
    </section>

    <!-- Prestadores -->
    <section id="prestadores" class="section">
      <div class="max-w-6xl mx-auto glass rounded-3xl p-6 md:p-8">
        <div class="text-center mb-6">
          <p class="badge mb-2">Para prestadores</p>
          <h2 class="text-2xl md:text-3xl font-bold">Visibilidad y organización en el mismo lugar</h2>
          <p class="text-white/75 mt-2">Tu ficha, servicios y reservas listos para dueños que ya usan la agenda.</p>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
          <article class="glass rounded-2xl p-4 text-center">
            <img src="assets/img/veterinario.webp" onerror="this.src='assets/img/veterinario.png'" alt="Ficha visible en mapas" class="feature-img" />
            <h3 class="font-semibold mb-1">Ficha visible en mapas/listados</h3>
            <p class="text-white/75 text-sm">Tus datos y servicios frente a dueños organizados.</p>
          </article>
          <article class="glass rounded-2xl p-4 text-center">
            <img src="assets/img/hero.webp" onerror="this.src='assets/img/hero.png'" alt="Recetas y reservas" class="feature-img" />
            <h3 class="font-semibold mb-1">Recetas y reservas</h3>
            <p class="text-white/75 text-sm">Recetas PDF y próximos turnos en un mismo flujo.</p>
          </article>
          <article class="glass rounded-2xl p-4 text-center">
            <img src="assets/img/beneficio1.webp" onerror="this.src='assets/img/beneficio1.png'" alt="Estadísticas básicas" class="feature-img" />
            <h3 class="font-semibold mb-1">Estadísticas básicas</h3>
            <p class="text-white/75 text-sm">Vistas y clics a WhatsApp para medir resultados.</p>
          </article>
        </div>
      </div>
    </section>

    <!-- Cómo funciona -->
    <section id="como-funciona" class="section pb-16">
      <div class="max-w-6xl mx-auto glass rounded-3xl p-6 md:p-8">
        <div class="text-center mb-6">
          <p class="badge mb-2">Flujo simple</p>
          <h2 class="text-2xl md:text-3xl font-bold">Cómo funciona para cada rol</h2>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <div class="glass rounded-2xl p-4 text-center">
            <h3 class="font-semibold mb-2">Si sos dueño/a</h3>
            <ol class="list-decimal list-inside space-y-1 text-white/80 text-sm text-left">
              <li>Creamos tu cuenta como dueño y agregás tus mascotas.</li>
              <li>Guardás historial, vacunas y documentos.</li>
              <li>Recibís recordatorios y accedés a prestadores cercanos.</li>
            </ol>
          </div>
          <div class="glass rounded-2xl p-4 text-center">
            <h3 class="font-semibold mb-2">Si sos prestador/a</h3>
            <ol class="list-decimal list-inside space-y-1 text-white/80 text-sm text-left">
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
