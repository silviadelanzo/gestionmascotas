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
              <button type="button" data-register-role="dueno" class="min-h-[44px] inline-flex items-center justify-center rounded-lg bg-[#A97155] text-white px-5 py-3 hover:bg-[#8d5f47] w-full sm:w-auto">
                Soy dueño/a de mascotas
              </button>
              <button type="button" data-register-role="prestador" class="min-h-[44px] inline-flex items-center justify-center rounded-lg bg-white text-[#A97155] border border-[#A97155]/30 px-5 py-3 hover:bg-[#fff2ea] w-full sm:w-auto">
                Soy veterinario/a o prestador
              </button>
            </div>
          </div>
          <div>
            <img src="assets/img/hero.webp" onerror="this.src='assets/img/hero.png'" alt="Familia con mascotas usando una app" class="w-full aspect-[4/3] object-cover rounded-xl shadow" />
          </div>
        </div>
      </div>
    </section>

    <!-- Bloque dueños -->
    <section id="duenos" class="max-w-screen-xl mx-auto px-4 pb-8 md:pb-12">
      <h2 class="text-[clamp(1.4rem,4vw,2rem)] font-semibold mb-4 text-center underline underline-offset-4 decoration-2 decoration-[#A97155]">
        Pensado para familias con mascotas
      </h2>
      <p class="max-w-2xl mx-auto text-sm md:text-base leading-relaxed text-center mb-6">
        Una cuenta por familia, hasta 10 mascotas. Todo el historial, recordatorios y documentos en un solo lugar, para que no dependas de una sola veterinaria o ciudad.
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
        <button type="button" data-register-role="dueno" class="inline-flex min-h-[44px] items-center justify-center rounded-lg bg-[#A97155] text-white px-6 py-3 hover:bg-[#8d5f47]">
          Crear cuenta gratuita de dueño
        </button>
        <p class="mt-2 text-xs md:text-sm opacity-80">En la versión inicial, el registro se enfocará en dueños de Argentina.</p>
      </div>
    </section>

    <!-- Bloque prestadores -->
    <section id="prestadores" class="max-w-screen-xl mx-auto px-4 pb-8 md:pb-12">
      <h2 class="text-[clamp(1.4rem,4vw,2rem)] font-semibold mb-4 text-center underline underline-offset-4 decoration-2 decoration-[#A97155]">
        Más visibilidad donde están tus clientes
      </h2>
      <p class="max-w-2xl mx-auto text-sm md:text-base leading-relaxed text-center mb-6">
        Aparecé en los listados de tu zona y destacá tus servicios frente a dueños que ya organizan la salud de sus mascotas en Mascotas y Mimos.
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
        <button type="button" data-register-role="prestador" class="inline-flex min-h-[44px] items-center justify-center rounded-lg bg-[#A97155] text-white px-6 py-3 hover:bg-[#8d5f47]">
          Crear cuenta gratuita de prestador
        </button>
      </div>
    </section>

    <!-- Cómo funciona -->
    <section id="como-funciona" class="max-w-screen-xl mx-auto px-4 pb-10 md:pb-14">
      <h2 class="text-[clamp(1.4rem,4vw,2rem)] font-semibold mb-4 text-center underline underline-offset-4 decoration-2 decoration-[#A97155]">
        ¿Cómo funciona?
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
        <article class="bg-white/95 rounded-xl p-5 shadow">
          <h3 class="text-base md:text-lg font-semibold mb-1">1. Elegís tu rol</h3>
          <p class="text-sm md:text-sm leading-relaxed">Creás tu cuenta como dueño o como prestador, con tus datos básicos y ubicación en Argentina.</p>
        </article>
        <article class="bg-white/95 rounded-xl p-5 shadow">
          <h3 class="text-base md:text-lg font-semibold mb-1">2. Cargás tus datos</h3>
          <p class="text-sm md:text-sm leading-relaxed">Dueños: mascotas, historial y contactos. Prestadores: ficha profesional, servicios y plan Free/Pro/Premium.</p>
        </article>
        <article class="bg-white/95 rounded-xl p-5 shadow">
          <h3 class="text-base md:text-lg font-semibold mb-1">3. Usás la agenda y los listados</h3>
          <p class="text-sm md:text-sm leading-relaxed">Recibís recordatorios, guardás documentos y encontrás servicios en tu ciudad cuando los necesitás.</p>
        </article>
      </div>
      <p class="max-w-2xl mx-auto mt-4 text-xs md:text-sm text-center opacity-80">
        En próximas etapas se integrarán las pantallas de registro, tableros por rol y mapas de prestadores con geolocalización.
      </p>
    </section>
  </main>

  <div id="registro-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="flex min-h-screen items-center justify-center px-4 py-6">
      <div class="absolute inset-0 bg-black/50 backdrop-blur-sm z-0" data-modal-close></div>
      <div class="relative z-10 w-full max-w-xl rounded-3xl bg-white px-5 py-6 shadow-2xl md:px-8 md:py-8">
        <button type="button" data-modal-close class="absolute top-4 right-4 rounded-full bg-white/70 p-2 text-sm font-semibold text-[#5A3E36] transition hover:bg-white">
          <span aria-hidden="true">&times;</span>
          <span class="sr-only">Cerrar formulario</span>
        </button>
        <div class="space-y-2">
          <p class="text-xs uppercase tracking-[0.3em] text-[#A97155]">Mascotas y Mimos</p>
          <h3 class="text-[clamp(1.4rem,4vw,1.8rem)] font-semibold text-[#5A3E36]" data-modal-title>Crear cuenta gratuita</h3>
          <p class="text-sm md:text-base text-[#5A3E36]/80" data-modal-subtitle>
            Completa tus datos, confirmá el email y comenzá a cuidar tus mascotas o mostrar tus servicios.
          </p>
        </div>
        <form id="registro-modal-form" method="post" action="registro.php" class="mt-5 space-y-4">
          <input type="hidden" name="tipo_usuario" id="registro-modal-role" value="dueño" />
          <div class="space-y-1 text-sm">
            <label for="registro-nombre" class="font-semibold text-[#5A3E36]">Nombre completo</label>
            <input id="registro-nombre" name="nombre" type="text" required autocomplete="name" class="w-full rounded-2xl border border-[#5A3E36]/20 bg-[#fffaf5] px-4 py-3 text-sm text-[#5A3E36] focus:border-[#A97155] focus:ring-2 focus:ring-[#A97155]/30" placeholder="Como figura en tu DNI o cuenta" />
          </div>
          <div class="space-y-1 text-sm">
            <label for="registro-email" class="font-semibold text-[#5A3E36]">Email</label>
            <input id="registro-email" name="email" type="email" required autocomplete="email" class="w-full rounded-2xl border border-[#5A3E36]/20 bg-[#fffaf5] px-4 py-3 text-sm text-[#5A3E36] focus:border-[#A97155] focus:ring-2 focus:ring-[#A97155]/30" placeholder="nombre@dominio.com" />
            <p class="text-xs text-[#5A3E36]/70">Te enviaremos un link para confirmar la cuenta.</p>
          </div>
          <div class="space-y-1 text-sm">
            <label for="registro-password" class="font-semibold text-[#5A3E36]">Contraseña</label>
            <div class="password-group">
              <input id="registro-password" name="password" type="password" required autocomplete="new-password" minlength="6" class="w-full rounded-2xl border border-[#5A3E36]/20 bg-[#fffaf5] px-4 py-3 text-sm text-[#5A3E36] focus:border-[#A97155] focus:ring-2 focus:ring-[#A97155]/30" placeholder="Mínimo 6 caracteres" />
              <button type="button" class="password-toggle" data-target="registro-password" aria-label="Mostrar contraseña">
                <span aria-hidden="true">👁</span>
              </button>
            </div>
            <p class="text-xs text-[#5A3E36]/70">Usá al menos 6 caracteres y combinaciones seguras.</p>
          </div>
          <div class="space-y-1 text-sm">
            <label for="registro-password-confirm" class="font-semibold text-[#5A3E36]">Confirmar contraseña</label>
            <div class="password-group">
              <input id="registro-password-confirm" name="password_confirm" type="password" required autocomplete="new-password" class="w-full rounded-2xl border border-[#5A3E36]/20 bg-[#fffaf5] px-4 py-3 text-sm text-[#5A3E36] focus:border-[#A97155] focus:ring-2 focus:ring-[#A97155]/30" placeholder="Repite la contraseña" />
              <button type="button" class="password-toggle" data-target="registro-password-confirm" aria-label="Mostrar contraseña confirmada">
                <span aria-hidden="true">👁</span>
              </button>
            </div>
          </div>
          <div id="registro-modal-feedback" class="hidden rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert"></div>
          <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-[#A97155] to-[#F27D4F] px-4 py-3 text-base font-semibold uppercase tracking-[0.1em] text-white shadow-lg transition hover:from-[#8d5f47] hover:to-[#b55b32]">Crear cuenta</button>
          <p class="text-xs text-center text-[#5A3E36]/70">
            Al registrarte aceptás nuestras políticas y recibirás novedades por email. Podés darte de baja cuando quieras.
          </p>
        </form>
      </div>
    </div>
  </div>

  <style>
    #registro-modal .password-group {
      position: relative;
    }
    #registro-modal .password-group .password-toggle {
      position: absolute;
      top: 50%;
      right: 0.5rem;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      font-size: 1.1rem;
      cursor: pointer;
      color: #5A3E36;
      padding: 0;
    }
  </style>

  <script>
    (function () {
      const modal = document.getElementById('registro-modal');
      if (!modal) return;
      const form = document.getElementById('registro-modal-form');
      const roleInput = document.getElementById('registro-modal-role');
      const titleEl = modal.querySelector('[data-modal-title]');
      const subtitleEl = modal.querySelector('[data-modal-subtitle]');
      const feedback = document.getElementById('registro-modal-feedback');
      const password = document.getElementById('registro-password');
      const passwordConfirm = document.getElementById('registro-password-confirm');
      const triggers = document.querySelectorAll('[data-register-role]');
      const copy = {
        dueño: {
          title: 'Crear cuenta gratuita de dueño/a',
          subtitle: 'Registrate para guardar tus mascotas, historiales y recordatorios en una sola cuenta familiar.'
        },
        prestador: {
          title: 'Crear cuenta gratuita de prestador/a',
          subtitle: 'Armá tu ficha profesional, cargá servicios y empezá a recibir consultas de dueños que usan la agenda.'
        }
      };

      const togglePasswordVisibility = (button) => {
        const targetId = button.getAttribute('data-target');
        const field = document.getElementById(targetId);
        if (!field) return;
        const isPassword = field.type === 'password';
        field.type = isPassword ? 'text' : 'password';
        button.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
      };

      modal.querySelectorAll('.password-toggle').forEach((button) => {
        button.addEventListener('click', () => togglePasswordVisibility(button));
      });

      const showFeedback = (message) => {
        if (!feedback) return;
        feedback.textContent = message;
        feedback.classList.remove('hidden');
      };

      const hideFeedback = () => {
        if (!feedback) return;
        feedback.textContent = '';
        feedback.classList.add('hidden');
      };

      const openModal = (role = 'dueño') => {
        const copyData = copy[role] || copy['dueño'];
        if (titleEl) titleEl.textContent = copyData.title;
        if (subtitleEl) subtitleEl.textContent = copyData.subtitle;
        if (form) form.reset();
        if (roleInput) roleInput.value = role;
        hideFeedback();
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
      };

      const closeModal = () => {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
      };

      triggers.forEach((btn) => {
        btn.addEventListener('click', (event) => {
          event.preventDefault();
          const role = (btn.getAttribute('data-register-role') || 'dueño').toLowerCase();
          openModal(role === 'prestador' ? 'prestador' : 'dueño');
        });
      });

      modal.querySelectorAll('[data-modal-close]').forEach((btn) => {
        btn.addEventListener('click', closeModal);
      });

      modal.addEventListener('click', (event) => {
        if (event.target === modal) {
          closeModal();
        }
      });

      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
          closeModal();
        }
      });

      if (form) {
        form.addEventListener('submit', (event) => {
          hideFeedback();
          if (password && password.value.length < 6) {
            event.preventDefault();
            showFeedback('La contraseña debe tener al menos 6 caracteres.');
            password.focus();
            return;
          }
          if (password && passwordConfirm && password.value !== passwordConfirm.value) {
            event.preventDefault();
            showFeedback('Las contraseñas deben coincidir.');
            passwordConfirm.focus();
          }
        });
      }
    })();
  </script>

  <footer class="py-6 text-center text-xs md:text-sm opacity-80">
    © <?php echo date('Y'); ?> Mascotas y Mimos · Esta es una versión de prueba de la nueva home (index_v2.php) en entorno local.
  </footer>
</body>
</html>
