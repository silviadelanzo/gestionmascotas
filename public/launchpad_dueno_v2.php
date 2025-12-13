<?php
session_start();
require_once __DIR__ . '/includes/helpers.php';

// Verificar que el usuario esté logueado y sea dueño
if (!isset($_SESSION['uid']) || $_SESSION['rol'] !== 'dueno') {
    header('Location: ' . home_url());
    exit;
}

$nombre = $_SESSION['nombre'] ?? 'Usuario';
$plan = $_SESSION['plan'] ?? 'gratis'; // 'gratis' o 'pro'

// Simular contador de consultas IA (en producción vendría de BD)
$consultasIA = $_SESSION['consultas_ia_mes'] ?? 0;
$limiteConsultasIA = ($plan === 'pro') ? 999 : 3;

// Simular cantidad de mascotas (en producción vendría de BD)
$cantidadMascotas = $_SESSION['cantidad_mascotas'] ?? 0;
$limiteMascotas = ($plan === 'pro') ? 999 : 2;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Launchpad - Mascotas y Mimos</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      overflow: hidden;
    }

    /* Fondo con imagen de mascotas */
    .launchpad-overlay {
      position: fixed;
      inset: 0;
      background-image: url('<?= app_base_url() ?>/assets/img/launchpad_bg.png');
      background-size: cover;
      background-position: center;
      z-index: 9999;
      display: flex;
      align-items: center;
      justify-content: center;
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    /* Overlay con blur y gradiente */
    .launchpad-overlay::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(
        135deg,
        rgba(165, 115, 85, 0.75),
        rgba(210, 180, 140, 0.65)
      );
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
    }

    /* Contenedor principal */
    .launchpad-container {
      position: relative;
      width: 95%;
      max-width: 1200px;
      max-height: 90vh;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      border-radius: 24px;
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    /* Header */
    .launchpad-header {
      padding: 1.5rem 2rem;
      background: rgba(255, 255, 255, 0.2);
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .launchpad-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: white;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .home-btn {
      padding: 0.65rem 1.25rem;
      border-radius: 20px;
      font-size: 0.95rem;
      font-weight: 600;
      background: rgba(255, 255, 255, 0.9);
      color: #2b1d18;
      border: 1px solid rgba(255, 255, 255, 0.4);
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .home-btn:hover {
      background: white;
      transform: translateY(-1px);
      box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
    }

    /* Búsqueda */
    .search-container {
      padding: 1.5rem 2rem;
    }

    .search-input {
      width: 100%;
      padding: 0.875rem 1.25rem 0.875rem 3rem;
      border-radius: 12px;
      border: 1px solid rgba(255, 255, 255, 0.3);
      background: rgba(255, 255, 255, 0.9);
      font-size: 1rem;
      outline: none;
      transition: all 0.2s ease;
    }

    .search-input:focus {
      background: white;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .search-icon {
      position: absolute;
      left: 3.25rem;
      top: 2.375rem;
      font-size: 1.25rem;
      color: #666;
    }

    /* Contenido scrollable */
    .launchpad-content {
      flex: 1;
      overflow-y: auto;
      padding: 1.5rem 2rem 2rem;
    }

    .launchpad-content::-webkit-scrollbar {
      width: 8px;
    }

    .launchpad-content::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 4px;
    }

    .launchpad-content::-webkit-scrollbar-thumb {
      background: rgba(255, 255, 255, 0.3);
      border-radius: 4px;
    }

    /* Categorías */
    .category {
      margin-bottom: 2.5rem;
    }

    .category-title {
      font-size: 0.875rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: white;
      margin-bottom: 1rem;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    /* Grid de botones */
    .buttons-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1rem;
    }

    /* Botón base */
    .launch-button {
      position: relative;
      padding: 1.5rem;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      color: inherit;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5rem;
    }

    .launch-button:hover {
      background: rgba(255, 255, 255, 0.95);
      transform: translateY(-4px);
      box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
    }

    /* Botón PRO */
    .launch-button.pro {
      background: linear-gradient(
        135deg,
        rgba(255, 215, 0, 0.25),
        rgba(218, 165, 32, 0.2)
      );
      border: 2px solid rgba(255, 215, 0, 0.5);
    }

    .launch-button.pro:hover {
      background: linear-gradient(
        135deg,
        rgba(255, 215, 0, 0.35),
        rgba(218, 165, 32, 0.3)
      );
      border-color: rgba(255, 215, 0, 0.7);
    }

    /* Botón destacado (Mejorar a PRO) */
    .launch-button.featured {
      background: linear-gradient(135deg, #FFD700, #FFA500);
      border: none;
      color: #000;
      font-weight: 700;
      box-shadow: 0 12px 32px rgba(255, 215, 0, 0.5);
      grid-column: span 2;
    }

    .launch-button.featured:hover {
      background: linear-gradient(135deg, #FFC700, #FF9500);
      transform: translateY(-6px);
      box-shadow: 0 16px 40px rgba(255, 215, 0, 0.6);
    }

    /* Icono del botón */
    .button-icon {
      font-size: 2rem;
      line-height: 1;
    }

    /* Título del botón */
    .button-title {
      font-size: 0.95rem;
      font-weight: 600;
      color: #333;
      line-height: 1.3;
    }

    .launch-button.featured .button-title {
      color: #000;
      font-size: 1.1rem;
    }

    /* Descripción del botón */
    .button-desc {
      font-size: 0.8rem;
      color: #666;
      line-height: 1.4;
    }

    .launch-button.featured .button-desc {
      color: rgba(0, 0, 0, 0.7);
    }

    /* Badge PRO (corona) */
    .pro-badge {
      position: absolute;
      top: -8px;
      right: -8px;
      font-size: 1.75rem;
      filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.3));
      animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-4px); }
    }

    /* Contador (para consultas IA) */
    .button-counter {
      position: absolute;
      top: 0.75rem;
      right: 0.75rem;
      padding: 0.25rem 0.5rem;
      border-radius: 12px;
      background: rgba(0, 0, 0, 0.7);
      color: white;
      font-size: 0.75rem;
      font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .launchpad-container {
        width: 100%;
        max-height: 100vh;
        border-radius: 0;
      }

      .buttons-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.75rem;
      }

      .launch-button {
        padding: 1.25rem;
      }

      .launch-button.featured {
        grid-column: span 1;
      }

      .button-icon {
        font-size: 1.75rem;
      }

      .button-title {
        font-size: 0.875rem;
      }

      .button-desc {
        font-size: 0.75rem;
      }
    }

    /* Ocultar elementos cuando se busca */
    .hidden {
      display: none;
    }
  </style>
</head>
<body>
  <div class="launchpad-overlay">
    <div class="launchpad-container">
      <!-- Header -->
      <div class="launchpad-header">
        <h1 class="launchpad-title">🐾 Hola, <?= htmlspecialchars($nombre) ?></h1>
        <a href="<?= home_url() ?>" class="home-btn">Volver al home</a>
      </div>

      

      <!-- Búsqueda -->
      <div class="search-container" style="position: relative;">
        <span class="search-icon">🔍</span>
        <input 
          type="text" 
          class="search-input" 
          id="searchInput"
          placeholder="Buscar funcionalidad..."
          autocomplete="off"
        >
      </div>

      <!-- Contenido -->
      <div class="launchpad-content">
        
        <!-- CATEGORÍA: MIS MASCOTAS -->
        <div class="category" data-category="mascotas">
          <h2 class="category-title">Mis Mascotas</h2>
          <div class="buttons-grid">
            
            <a href="<?= app_base_url() ?>/mascotas/mis_mascotas.php" class="launch-button" data-search="ver mis mascotas lista">
              <span class="button-icon">🐕</span>
              <span class="button-title">Ver mis mascotas</span>
              <span class="button-desc">Lista completa de tus mascotas</span>
            </a>

            <?php if ($cantidadMascotas >= $limiteMascotas && $plan === 'gratis'): ?>
              <a href="<?= app_base_url() ?>/planes.php?feature=mascotas_ilimitadas" class="launch-button pro" data-search="agregar mascota nueva">
                <span class="pro-badge">👑</span>
                <span class="button-icon">➕</span>
                <span class="button-title">Agregar mascota</span>
                <span class="button-desc">Límite alcanzado (<?= $cantidadMascotas ?>/<?= $limiteMascotas ?>)</span>
              </a>
            <?php else: ?>
              <a href="<?= app_base_url() ?>/mascotas/agregar.php" class="launch-button" data-search="agregar mascota nueva">
                <span class="button-icon">➕</span>
                <span class="button-title">Agregar mascota</span>
                <span class="button-desc">Registra una nueva mascota</span>
              </a>
            <?php endif; ?>

            <a href="<?= app_base_url() ?>/planes.php?feature=analisis_salud" class="launch-button pro" data-search="analisis salud graficos">
              <span class="pro-badge">👑</span>
              <span class="button-icon">📊</span>
              <span class="button-title">Análisis de salud</span>
              <span class="button-desc">Gráficos y tendencias</span>
            </a>

          </div>
        </div>

        <!-- CATEGORÍA: SALUD Y RECORDATORIOS -->
        <div class="category" data-category="salud">
          <h2 class="category-title">Salud y Recordatorios</h2>
          <div class="buttons-grid">
            
            <a href="<?= app_base_url() ?>/recordatorios/vacunas.php" class="launch-button" data-search="calendario vacunas proximas">
              <span class="button-icon">💉</span>
              <span class="button-title">Calendario de vacunas</span>
              <span class="button-desc">Próximas vacunas y desparasitaciones</span>
            </a>

            <a href="<?= app_base_url() ?>/recordatorios/index.php" class="launch-button" data-search="recordatorios alertas notificaciones">
              <span class="button-icon">🔔</span>
              <span class="button-title">Mis recordatorios</span>
              <span class="button-desc">Gestiona alertas y notificaciones</span>
            </a>

            <a href="<?= app_base_url() ?>/planes.php?feature=whatsapp_ilimitado" class="launch-button pro" data-search="whatsapp recordatorios ilimitados">
              <span class="pro-badge">👑</span>
              <span class="button-icon">📱</span>
              <span class="button-title">WhatsApp ilimitado</span>
              <span class="button-desc">Alertas sin límites por WhatsApp</span>
            </a>

            <?php if ($consultasIA >= $limiteConsultasIA && $plan === 'gratis'): ?>
              <a href="<?= app_base_url() ?>/planes.php?feature=ia_ilimitado" class="launch-button pro" data-search="consultar ia chatbot sintomas">
                <span class="pro-badge">👑</span>
                <span class="button-counter"><?= $consultasIA ?>/<?= $limiteConsultasIA ?></span>
                <span class="button-icon">🤖</span>
                <span class="button-title">Consultar IA</span>
                <span class="button-desc">Límite alcanzado este mes</span>
              </a>
            <?php else: ?>
              <a href="<?= app_base_url() ?>/ia/chatbot.php" class="launch-button" data-search="consultar ia chatbot sintomas">
                <?php if ($plan === 'gratis'): ?>
                  <span class="button-counter"><?= $consultasIA ?>/<?= $limiteConsultasIA ?></span>
                <?php endif; ?>
                <span class="button-icon">🤖</span>
                <span class="button-title">Consultar IA</span>
                <span class="button-desc">Pregunta sobre síntomas y cuidados</span>
              </a>
            <?php endif; ?>

          </div>
        </div>

        <!-- CATEGORÍA: DOCUMENTOS Y HISTORIAL -->
        <div class="category" data-category="documentos">
          <h2 class="category-title">Documentos y Historial</h2>
          <div class="buttons-grid">
            
            <a href="<?= app_base_url() ?>/documentos/index.php" class="launch-button" data-search="documentos carnets recetas">
              <span class="button-icon">📄</span>
              <span class="button-title">Mis documentos</span>
              <span class="button-desc">Carnets, recetas y estudios</span>
            </a>

            <a href="<?= app_base_url() ?>/historial/index.php" class="launch-button" data-search="historial medico vacunas">
              <span class="button-icon">📋</span>
              <span class="button-title">Historial médico</span>
              <span class="button-desc">Registro de vacunas y tratamientos</span>
            </a>

            <a href="<?= app_base_url() ?>/planes.php?feature=historial_completo" class="launch-button pro" data-search="historial completo medicaciones cirugias">
              <span class="pro-badge">👑</span>
              <span class="button-icon">📊</span>
              <span class="button-title">Historial completo</span>
              <span class="button-desc">Medicaciones, cirugías y más</span>
            </a>

            <a href="<?= app_base_url() ?>/planes.php?feature=exportar_pdf" class="launch-button pro" data-search="exportar pdf descargar">
              <span class="pro-badge">👑</span>
              <span class="button-icon">📥</span>
              <span class="button-title">Exportar PDF</span>
              <span class="button-desc">Descarga historial completo</span>
            </a>

          </div>
        </div>

        <!-- CATEGORÍA: PRESTADORES -->
        <div class="category" data-category="prestadores">
          <h2 class="category-title">Prestadores</h2>
          <div class="buttons-grid">
            
            <a href="<?= app_base_url() ?>/mapa_prestadores.php" class="launch-button" data-search="buscar veterinarias mapa cerca">
              <span class="button-icon">🗺️</span>
              <span class="button-title">Buscar veterinarias</span>
              <span class="button-desc">Encuentra prestadores cerca tuyo</span>
            </a>

            <a href="<?= app_base_url() ?>/prestadores/favoritos.php" class="launch-button" data-search="favoritos guardados">
              <span class="button-icon">⭐</span>
              <span class="button-title">Mis favoritos</span>
              <span class="button-desc">Prestadores guardados</span>
            </a>

            <a href="<?= app_base_url() ?>/reservas/nueva.php" class="launch-button" data-search="reservar turno agendar">
              <span class="button-icon">📅</span>
              <span class="button-title">Reservar turno</span>
              <span class="button-desc">Agenda citas con prestadores</span>
            </a>

          </div>
        </div>

        <!-- CATEGORÍA: CUENTA -->
        <div class="category" data-category="cuenta">
          <h2 class="category-title">Cuenta</h2>
          <div class="buttons-grid">
            
            <a href="<?= app_base_url() ?>/perfil/editar.php" class="launch-button" data-search="perfil datos personales editar">
              <span class="button-icon">👤</span>
              <span class="button-title">Mi perfil</span>
              <span class="button-desc">Edita tus datos personales</span>
            </a>

            <a href="<?= app_base_url() ?>/planes.php?feature=compartir_acceso" class="launch-button pro" data-search="compartir acceso familia">
              <span class="pro-badge">👑</span>
              <span class="button-icon">👨‍👩‍👧</span>
              <span class="button-title">Compartir acceso</span>
              <span class="button-desc">Familia y cuidadores</span>
            </a>

            <?php if ($plan === 'gratis'): ?>
              <a href="<?= app_base_url() ?>/planes.php" class="launch-button featured" data-search="mejorar pro premium suscripcion">
                <span class="button-icon">💎</span>
                <span class="button-title">Mejorar a PRO</span>
                <span class="button-desc">Desbloquea todas las funcionalidades por $3.99/mes</span>
              </a>
            <?php endif; ?>

          </div>
        </div>

      </div>
    </div>
  </div>

  <script>
    // Búsqueda en tiempo real
    const searchInput = document.getElementById('searchInput');
    const categories = document.querySelectorAll('.category');
    const buttons = document.querySelectorAll('.launch-button');

    searchInput.addEventListener('input', function() {
      const query = this.value.toLowerCase().trim();

      if (query === '') {
        // Mostrar todo
        categories.forEach(cat => cat.classList.remove('hidden'));
        buttons.forEach(btn => btn.classList.remove('hidden'));
        return;
      }

      // Filtrar botones
      let hasVisibleButtons = {};
      
      buttons.forEach(btn => {
        const searchText = btn.getAttribute('data-search') || '';
        const titleText = btn.querySelector('.button-title')?.textContent.toLowerCase() || '';
        const descText = btn.querySelector('.button-desc')?.textContent.toLowerCase() || '';
        
        const matches = searchText.includes(query) || 
                       titleText.includes(query) || 
                       descText.includes(query);

        if (matches) {
          btn.classList.remove('hidden');
          const category = btn.closest('.category');
          if (category) {
            hasVisibleButtons[category.dataset.category] = true;
          }
        } else {
          btn.classList.add('hidden');
        }
      });

      // Ocultar categorías sin botones visibles
      categories.forEach(cat => {
        if (hasVisibleButtons[cat.dataset.category]) {
          cat.classList.remove('hidden');
        } else {
          cat.classList.add('hidden');
        }
      });
    });

    // Focus automático en búsqueda
    searchInput.focus();

    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        window.location.href = '<?= home_url() ?>';
      }
    });
  </script>
</body>
</html>
