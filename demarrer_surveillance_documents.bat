@echo off
title Démarrage Surveillance Documents
color 0C

echo.
echo ========================================
echo    SURVEILLANCE AUTOMATIQUE DES
echo         DOCUMENTS
echo ========================================
echo.
echo Ce script lance la surveillance continue
echo des documents pour l'envoi automatique.
echo.
echo Options :
echo 1. Surveillance continue (recommandé)
echo 2. Test du système
echo 3. Vérification manuelle
echo 4. Quitter
echo.
echo ========================================
echo.

:menu
set /p choix="Choisissez une option (1-4) : "

if "%choix%"=="1" goto surveillance
if "%choix%"=="2" goto test
if "%choix%"=="3" goto verification
if "%choix%"=="4" goto quit
echo Option invalide. Veuillez choisir 1, 2, 3 ou 4.
goto menu

:surveillance
echo.
echo 🚀 Lancement de la surveillance continue...
echo ⏰ Vérification toutes les 30 secondes...
echo 🛑 Pour arrêter : Ctrl+C
echo.
php surveillance_documents_automatique.php
goto menu

:test
echo.
echo 🧪 Lancement du test du système...
echo.
php test_envoi_documents_automatique.php
echo.
pause
goto menu

:verification
echo.
echo 🔍 Vérification manuelle des documents...
echo.
php artisan content:send-scheduled
echo.
pause
goto menu

:quit
echo.
echo 👋 Au revoir !
echo.
pause 