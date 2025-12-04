<?php
require __DIR__ . '/includes/bootstrap.php';
$baseUrl = app_base_url();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesion - Mascotas y Mimos</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --brand: #A97155;
      --brand-dark: #8d5f47;
      --text: #3b2c26;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      min-height: 100vh;
      font-family: 'Poppins', system-ui, -apple-system, 'Segoe UI', sans-serif;
      color: var(--text);
      background: radial-gradient(circle at 20% 20%, rgba(255,214,165,0.6), transparent 35%),
                  radial-gradient(circle at 80% 0%, rgba(250,224,195,0.6), transparent 30%),
                  linear-gradient(135deg, #fff4ec, #f9e4d5);
      position: relative;
      overflow: hidden;
    }
    body::before {
      content: '';
      position: absolute;
      inset: 0;
      background: url('/assets/img/hero.webp') center/cover no-repeat;
      filter: blur(18px) brightness(0.9);
      opacity: 0.35;
      z-index: 0;
    }
    main {
      position: relative;
      z-index: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 2rem 1rem;
    }
    .auth-card {
      width: 100%;
      max-width: 420px;
      background: rgba(255,255,255,0.92);
      backdrop-filter: blur(6px);
      border-radius: 28px;
      padding: 2.5rem 2rem;
      box-shadow: 0 25px 70px rgba(80, 50, 35, 0.18);
    }
    .auth-card h1 {
      margin: 0 0 0.35rem;
      font-size: 1.9rem;
      text-align: center;
    }
    .auth-card p {
      margin: 0 0 1.6rem;
      text-align: center;
      color: #6b4e43;
    }
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.35rem;
      margin-bottom: 1rem;
      font-size: 0.95rem;
    }
    .form-group label {
      font-weight: 600;
    }
    .form-control {
      width: 100%;
      padding: 0.85rem 1rem;
      border: 1px solid #e6d8cf;
      border-radius: 16px;
      background: #fffaf7;
      font-size: 1rem;
      transition: border 0.2s ease, box-shadow 0.2s ease;
    }
    .form-control:focus {
      outline: none;
      border-color: var(--brand);
      box-shadow: 0 0 0 3px rgba(169, 113, 85, 0.18);
      background: #ffffff;
    }
    .password-group { position: relative; }
    .password-toggle {
      position: absolute;
      top: 50%;
      right: 0.65rem;
      transform: translateY(-50%);
      border: none;
      background: transparent;
      color: #6b4e43;
      font-size: 1rem;
      cursor: pointer;
    }
    .cta {
      width: 100%;
      padding: 1rem;
      border: none;
      border-radius: 20px;
      background: linear-gradient(135deg, var(--brand), var(--brand-dark));
      color: #fff;
      font-weight: 700;
      font-size: 1.05rem;
      cursor: pointer;
      transition: transform 0.15s ease, box-shadow 0.2s ease;
    }
    .cta:hover {
      transform: translateY(-1px);
      box-shadow: 0 15px 28px rgba(169, 113, 85, 0.25);
    }
    .links {
      margin-top: 1.2rem;
      text-align: center;
      font-size: 0.95rem;
    }
    .links a {
      color: var(--brand);
      text-decoration: none;
      font-weight: 600;
    }
    .links a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <main>
    <section class="auth-card" aria-labelledby="auth-title">
      <h1 id="auth-title">Iniciar sesion</h1>
      <p>Accede a tu agenda de mascotas o a tu panel de prestador.</p>
      <form method="post" action="api/login.php" novalidate>
        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" class="form-control" required autocomplete="email">
        </div>
        <div class="form-group">
          <label for="password">Contrasena</label>
          <div class="password-group">
            <input id="password" name="password" type="password" class="form-control" required autocomplete="current-password">
            <button type="button" class="password-toggle" aria-label="Mostrar contrasena" data-target="password">👁</button>
          </div>
        </div>
        <button type="submit" class="cta">Entrar</button>
      </form>
      <div class="links">
        <p><a href="olvide_password.php">¿Olvidaste tu contrasena?</a></p>
        <p><a href="registro.php">Crear cuenta</a></p>
      </div>
    </section>
  </main>
  <script>
    (function () {
      const toggle = document.querySelector('.password-toggle');
      if (!toggle) return;
      toggle.addEventListener('click', () => {
        const targetId = toggle.getAttribute('data-target');
        const field = document.getElementById(targetId);
        if (!field) return;
        const isPassword = field.type === 'password';
        field.type = isPassword ? 'text' : 'password';
        toggle.setAttribute('aria-label', isPassword ? 'Ocultar contrasena' : 'Mostrar contrasena');
      });
    })();
  </script>
</body>
</html>
