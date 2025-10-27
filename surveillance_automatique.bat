@echo off
title Surveillance Automatique des Questionnaires
color 0A
cls
echo ========================================
echo SURVEILLANCE AUTOMATIQUE DES QUESTIONNAIRES
echo ========================================
echo.
echo Le systeme verifie toutes les minutes si des questionnaires
echo doivent etre envoyes automatiquement.
echo.
echo Pour arreter : Appuyez sur Ctrl+C
echo.
cd /d "%~dp0"
echo Repertoire de travail : %CD%
echo.
:loop
echo [%date% %time%] Verification en cours...
php surveillance_automatique.php
echo.
echo Attente de 60 secondes...
timeout /t 60 /nobreak > nul
echo.
goto loop
