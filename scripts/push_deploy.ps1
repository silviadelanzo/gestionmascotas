$ErrorActionPreference = 'Stop'

param(
  [string]$Message = ""
)

if ($Message -eq "") {
  $Message = "deploy: " + (Get-Date -Format "yyyy-MM-dd HH:mm:ss")
}

Write-Host "== git status =="
git status

Write-Host "== git add -A =="
git add -A

Write-Host "== git commit =="
try {
  git commit -m $Message
} catch {
  Write-Host "No hay cambios para commitear (o falló el commit)."
}

Write-Host "== git push origin main =="
git push origin main

Write-Host "Listo. Ahora revisá GitHub Actions (Deploy via FTP)."

