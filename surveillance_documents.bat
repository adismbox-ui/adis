@echo off
title Surveillance Automatique des Documents
color 0C

echo ========================================
echo   SURVEILLANCE AUTOMATIQUE DES
echo         DOCUMENTS
echo ========================================
echo.
echo Ce script surveille et envoie automatiquement
echo les documents quand leur heure arrive.
echo.
echo Pour arrêter : Ctrl+C
echo ========================================
echo.

cd /d "%~dp0"

:loop
php surveillance_documents_automatique.php
if %errorlevel% neq 0 (
    echo.
    echo ❌ Erreur dans la surveillance. Redémarrage dans 10 secondes...
    timeout /t 10 /nobreak >nul
    goto loop
)

echo.
echo ✅ Surveillance terminée normalement.
pause 