# Script para subir archivos al servidor
# IMPORTANTE: Necesitas tener WinSCP instalado o usar FTP manualmente

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "ARCHIVOS PARA SUBIR AL SERVIDOR" -ForegroundColor Yellow
Write-Host "========================================`n" -ForegroundColor Cyan

$archivos = @(
    @{
        Local  = "d:\xampp\htdocs\gestionmascotas\public\config\db.php"
        Remoto = "/gestionmascotas/public/config/db.php"
    },
    @{
        Local  = "d:\xampp\htdocs\gestionmascotas\public\includes\auth.php"
        Remoto = "/gestionmascotas/public/includes/auth.php"
    },
    @{
        Local  = "d:\xampp\htdocs\gestionmascotas\public\includes\bootstrap.php"
        Remoto = "/gestionmascotas/public/includes/bootstrap.php"
    }
)

Write-Host "Archivos a subir:" -ForegroundColor Green
foreach ($archivo in $archivos) {
    if (Test-Path $archivo.Local) {
        Write-Host "  OK $($archivo.Local)" -ForegroundColor Green
        Write-Host "    -> $($archivo.Remoto)" -ForegroundColor Gray
    }
    else {
        Write-Host "  ERROR: NO ENCONTRADO: $($archivo.Local)" -ForegroundColor Red
    }
}

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "OPCIONES PARA SUBIR:" -ForegroundColor Yellow
Write-Host "========================================`n" -ForegroundColor Cyan

Write-Host "1. Usar FileZilla (Manual):" -ForegroundColor Cyan
Write-Host "   - Abri FileZilla" -ForegroundColor Gray
Write-Host "   - Conectate a mascotasymimos.com" -ForegroundColor Gray
Write-Host "   - Arrastra los archivos de arriba a las rutas remotas" -ForegroundColor Gray

Write-Host "`n2. Copiar a carpeta temporal:" -ForegroundColor Cyan
$tempFolder = "$env:USERPROFILE\Desktop\subir_al_servidor"
if (!(Test-Path $tempFolder)) {
    New-Item -ItemType Directory -Path $tempFolder -Force | Out-Null
}

foreach ($archivo in $archivos) {
    if (Test-Path $archivo.Local) {
        $destino = Join-Path $tempFolder (Split-Path $archivo.Local -Leaf)
        Copy-Item $archivo.Local -Destination $destino -Force
    }
}

Write-Host "   OK Archivos copiados a: $tempFolder" -ForegroundColor Green
Write-Host "   Subi esos archivos manualmente al servidor" -ForegroundColor Gray

Write-Host "`n========================================" -ForegroundColor Cyan
Write-Host "Presiona cualquier tecla para abrir la carpeta..." -ForegroundColor Yellow
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
Start-Process $tempFolder
