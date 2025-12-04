<?php
require __DIR__ . '/includes/bootstrap.php';

$rol = $_SESSION['rol'] ?? '';
if ($rol !== 'prestador' && $rol !== 'admin') {
  header('Location: login.php');
  exit;
}
$nombre = $_SESSION['nombre'] ?? 'tu cuenta';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Launchpad prestador - Mascotas y Mimos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root { --brand:#A97155; --brand-dark:#8d5f47; --text:#3b2c26; }
    * { box-sizing:border-box; }
    body {
      margin:0; min-height:100vh; font-family:'Poppins',system-ui,-apple-system,'Segoe UI',sans-serif;
      color:var(--text);
      background: radial-gradient(circle at 20% 20%, rgba(255,214,165,0.6), transparent 35%),
                  radial-gradient(circle at 80% 0%, rgba(250,224,195,0.6), transparent 30%),
                  linear-gradient(135deg,#fff4ec,#f9e4d5);
      position:relative; overflow:hidden;
    }
    body::before {
      content:''; position:absolute; inset:0; background:url('/assets/img/hero.webp') center/cover no-repeat;
      filter:blur(20px) brightness(0.9); opacity:0.28; z-index:0;
    }
    main { position:relative; z-index:1; max-width:960px; margin:0 auto; padding:3rem 1.25rem 4rem; }
    .card {
      background:rgba(255,255,255,0.94); backdrop-filter:blur(8px); border-radius:28px;
      padding:2.2rem; box-shadow:0 24px 60px rgba(80,50,35,0.16);
    }
    h1 { margin:0 0 0.4rem; font-size:2rem; }
    p.lead { margin:0 0 1.4rem; color:#6b4e43; }
    .cta {
      display:inline-flex; align-items:center; gap:0.5rem; padding:0.95rem 1.3rem;
      border-radius:16px; border:1px solid #e6d8cf; background:#fffaf7; color:var(--text);
      cursor:pointer; font-weight:700; transition:transform 0.15s ease, box-shadow 0.2s ease;
    }
    .cta:hover { transform:translateY(-1px); box-shadow:0 14px 24px rgba(169,113,85,0.2); }
    .pill {
      display:inline-flex; align-items:center; padding:0.45rem 0.9rem; border-radius:999px;
      background:rgba(169,113,85,0.12); color:var(--brand-dark); font-size:0.9rem; font-weight:600;
    }
    .modal {
      position:fixed; inset:0; display:none; align-items:center; justify-content:center; padding:1.5rem;
      background:rgba(0,0,0,0.55); backdrop-filter:blur(6px); z-index:10;
    }
    .modal.active { display:flex; }
    .modal-card {
      width:100%; max-width:620px; background:#fff; border-radius:24px; padding:1.8rem 1.6rem;
      box-shadow:0 24px 60px rgba(0,0,0,0.2);
    }
    .actions { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:0.8rem; margin-top:1rem; }
    .action {
      padding:1rem 1rem; border-radius:16px; border:1px solid #e6d8cf; background:#fffaf7;
      font-weight:600; color:var(--text); text-decoration:none; display:block;
    }
    .action small { display:block; margin-top:0.2rem; color:#6b4e43; font-weight:400; }
    .close { float:right; border:none; background:transparent; font-size:1.2rem; cursor:pointer; color:#6b4e43; }
  </style>
</head>
<body>
  <main>
    <div class="card">
      <div class="pill">Launchpad prestador</div>
      <h1>Hola, <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?></h1>
      <p class="lead">Accede rapido a tu ficha, servicios y reservas.</p>
      <button class="cta" id="open-modal">Abrir acciones</button>
    </div>
  </main>

  <div class="modal" id="actions-modal" aria-hidden="true">
    <div class="modal-card">
      <button class="close" id="close-modal" aria-label="Cerrar">×</button>
      <h2>Acciones rapidas</h2>
      <div class="actions">
        <a class="action" href="/prestadores/mi_ficha.php">Editar mi ficha<small>Datos de contacto, ubicacion y plan.</small></a>
        <a class="action" href="/prestadores/fotos.php">Subir fotos<small>Galeria y portada de tu ficha.</small></a>
        <a class="action" href="/servicios/publicar.php">Publicar servicios<small>Tipos, precios y disponibilidad.</small></a>
        <a class="action" href="/reservas/listado.php">Reservas y consultas<small>Ver pendientes, confirmar o reprogramar.</small></a>
        <a class="action" href="/prestadores/recetas.php">Recetas/indicaciones PDF<small>Enviar a clientes con tu matricula.</small></a>
        <a class="action" href="/reportes/estadisticas.php">Estadisticas<small>Vistas de ficha y clics a WhatsApp.</small></a>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const modal = document.getElementById('actions-modal');
      const openBtn = document.getElementById('open-modal');
      const closeBtn = document.getElementById('close-modal');
      if (!modal || !openBtn || !closeBtn) return;

      const open = () => { modal.classList.add('active'); modal.setAttribute('aria-hidden','false'); };
      const close = () => { modal.classList.remove('active'); modal.setAttribute('aria-hidden','true'); };

      openBtn.addEventListener('click', open);
      closeBtn.addEventListener('click', close);
      modal.addEventListener('click', (e) => { if (e.target === modal) close(); });
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });

      open();
    })();
  </script>
</body>
</html>
