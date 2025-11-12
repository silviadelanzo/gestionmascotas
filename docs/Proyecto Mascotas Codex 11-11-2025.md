# Proyecto Mascotas Codex — 11/11/2025

## Descripción general
Plataforma SaaS “Mascotas y Mimos” para conectar dueños de mascotas con prestadores de servicios (veterinarias, cuidadores, paseadores, etc.). Permite registro dual (usuario / prestador), gestión de mascotas, agenda de turnos, historial de servicios y visualización geolocalizada de prestadores.

## Objetivos
- Operar en modo SaaS, escalable por dominio o cuenta.
- Registro con validación por email y login social (Google, Yahoo, Outlook).
- Interfaz adaptable tipo app móvil (Bootstrap / Tailwind).
- Comunicación entre usuarios y prestadores; reservas y gestión de agenda.
- Mapas (Google Maps API o Leaflet) para ubicación de prestadores.

---

## Estado actual del desarrollo
1) Entorno local y servidor sincronizados
- Base de datos: `petcare_saas`.
- Tablas principales: `users`, `email_verifications`, `mascotas`, `prestadores`, `reservas`, etc.
- Envío de correos funcional (PHPMailer operativo en local y servidor).

2) Registro y verificación
- `public/registro.php` y `public/verificar.php` implementados.
- Alta de usuario con validación por token vía correo.
- Tabla `email_verifications` con clave foránea correcta.

3) Mailer
- SMTP Host: `mail.mascotasymimos.com`
- Puerto: 465 (SSL) o 587 (TLS)
- Usuario: `no-responder@mascotasymimos.com`
- Contraseña: gestionada por configuración/secret (no versionar en texto plano).
- Test funcional en local y server (`public/test_email.php` OK).

---

## Estructura de proyecto
```
gestionmascotas/
├── app/
│   ├── Controllers/
│   ├── Models/
│   └── Views/
├── config/
│   ├── mail.php
│   └── env.example
├── lib/
│   └── PHPMailer/
├── public/
│   ├── index.php
│   ├── registro.php
│   ├── verificar.php
│   ├── test_email.php
│   └── assets/
├── docs/
└── Proyecto_Mascotas_Codex11_11_2025.md
```

---

## Conexión con GitHub
Repositorio remoto: https://github.com/silviadelanzo/gestionmascotas

### GitHub Actions: Secrets
- `FTP_HOST` → servidor (ej: `ftp.mascotasymimos.com`).
- `FTP_USER` → usuario FTP cPanel.
- `FTP_PASS` → contraseña FTP.
- `FTP_PATH` → ruta destino, ej: `/public_html/gestionmascotas/`.

### Workflow sugerido: `.github/workflows/deploy.yml`
```yaml
name: Deploy to Server

on:
  push:
    branches:
      - main

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v3

      - name: Deploy via FTP
        uses: SamKirkland/FTP-Deploy-Action@v4.3.0
        with:
          server: ${{ secrets.FTP_HOST }}
          username: ${{ secrets.FTP_USER }}
          password: ${{ secrets.FTP_PASS }}
          server-dir: ${{ secrets.FTP_PATH }}
```

### Reglas de despliegue
- Commits locales se suben al repo GitHub (rama `main`).
- GitHub Actions despliega al servidor sin eliminar archivos existentes.
- Nuevos módulos (Home, Login, Registro, Mapa, Dashboard) se integran gradualmente.

---

## Próximos pasos
- Implementar landing page comercial (discreta, estilo pastel).
- Finalizar flujo de registro dual (usuario / prestador).
- Agregar gestión de múltiples mascotas por usuario.
- Crear mapa geolocalizado de prestadores (Google Maps o Leaflet).
- Diseñar dashboard responsive con accesos rápidos.
- Implementar login social (Google / Yahoo / Outlook).
- Añadir administración básica (panel admin).

---

## Notas técnicas
- Centralizar credenciales de correo en `config/mail.php` y variables de entorno; evitar commit de secretos.
- PHPMailer está bajo `lib/PHPMailer/` y se usa en `public/test_email.php` y flujo de verificación.
- Base de datos `petcare_saas` con FK definida para `email_verifications` → `users`.
- UI: Bootstrap o Tailwind; priorizar componentes responsive y UX mobile-first.

---

## Estado actual
- Proyecto operativo en local y servidor.
- PHPMailer configurado y probado correctamente.

---

## Checklist de seguimiento
- [ ] Landing page implementada y enlazada desde `public/index.php`.
- [ ] Registro dual completo y verificado.
- [ ] Gestión de múltiples mascotas por usuario.
- [ ] Mapa de prestadores con filtros y geolocalización.
- [ ] Dashboard responsive con accesos rápidos.
- [ ] Login social integrado (Google, Yahoo, Outlook).
- [ ] Panel de administración básico.
- [ ] Pipeline de deploy verificada con rollback simple.

---

## Observaciones de seguridad
- No almacenar contraseñas ni tokens en repositorio: usar `.env` y secrets de GitHub.
- Forzar `SMTPSecure` según puerto (TLS para 587, SSL para 465) y verificar certificados.
- Sanitizar entradas en formularios de registro y verificación; usar declaraciones preparadas.

---

## Anexos
- Script de prueba SMTP: `public/test_email.php`.
- Rutas de registro/verificación: `public/registro.php`, `public/verificar.php`.

***

> Este documento es la base para coordinar tareas, despliegues y próximos incrementos del proyecto Mascotas y Mimos en modalidad SaaS.
