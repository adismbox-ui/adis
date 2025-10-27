# Surveillance Automatique des Questionnaires
Write-Host "========================================" -ForegroundColor Green
Write-Host "SURVEILLANCE AUTOMATIQUE DES QUESTIONNAIRES" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Le systeme verifie toutes les minutes si des questionnaires" -ForegroundColor Yellow
Write-Host "doivent etre envoyes automatiquement." -ForegroundColor Yellow
Write-Host ""
Write-Host "Pour arreter : Appuyez sur Ctrl+C" -ForegroundColor Red
Write-Host ""

# Aller dans le répertoire du projet
Set-Location $PSScriptRoot
Write-Host "Repertoire de travail : $(Get-Location)" -ForegroundColor Cyan
Write-Host ""

# Boucle de surveillance
while ($true) {
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Write-Host "[$timestamp] Verification en cours..." -ForegroundColor Green
    
    try {
        # Exécuter le script PHP
        php surveillance_automatique.php
        Write-Host ""
        Write-Host "Attente de 60 secondes..." -ForegroundColor Yellow
        Start-Sleep -Seconds 60
        Write-Host ""
    }
    catch {
        Write-Host "Erreur lors de l'execution : $_" -ForegroundColor Red
        Start-Sleep -Seconds 10
    }
} 