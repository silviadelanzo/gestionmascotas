$content = Get-Content 'public\index_v2_6.php' -Raw -Encoding UTF8

# 1. Cambiar botón login por avatar
$content = $content -replace '(\s+)<\?php else: \?>\s+<a class="pill btn-primary" data-href="<\?= htmlspecialchars\(\$loginUrl, ENT_QUOTES, ''UTF-8''\) \?>">Ingresar a mi cuenta</a>\s+<\?php endif; \?>', @'
$1<?php else: ?>
$1  <!-- Avatar para no logueados -->
$1  <div class="user-menu">
$1    <div class="user-avatar" id="login-avatar">
$1      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
$1        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
$1      </svg>
$1    </div>
$1    <div class="dropdown" id="login-dropdown">
$1      <a data-href="<?= htmlspecialchars($loginUrl, ENT_QUOTES, 'UTF-8') ?>" class="dropdown-item">
$1        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
$1          <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
$1        </svg>
$1        Ingresar a mi cuenta
$1      </a>
$1    </div>
$1  </div>
$1<?php endif; ?>
'@

# 2. Agregar CSS toggle active
$content = $content -replace '(\.user-menu:hover \.dropdown \{\s+opacity: 1;\s+visibility: visible;\s+transform: translateY\(0\);\s+\})', @'
$1
    /* Toggle manual para mobile */
    .dropdown.active {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
'@

# 3. Agregar JavaScript para toggle
$content = $content -replace '(document\.querySelectorAll\("a\[data-scroll\]"\)\.forEach.*?\}\);\s+\}\);)', @'
$1

    // Toggle dropdown para login en mobile (click/tap)
    const loginAvatar = document.getElementById("login-avatar");
    const loginDropdown = document.getElementById("login-dropdown");
    
    if (loginAvatar && loginDropdown) {
      loginAvatar.addEventListener("click", function(e) {
        e.stopPropagation();
        loginDropdown.classList.toggle("active");
      });
      
      // Cerrar al hacer click fuera
      document.addEventListener("click", function(e) {
        if (!loginAvatar.contains(e.target) && !loginDropdown.contains(e.target)) {
          loginDropdown.classList.remove("active");
        }
      });
    }
'@

Set-Content 'public\index_v2_6.php' -Value $content -NoNewline -Encoding UTF8
Write-Host "index_v2_6.php actualizado"
