@echo off
title Surveillance Automatique Questionnaires
echo ========================================
echo SURVEILLANCE AUTOMATIQUE CONTINUE
echo ========================================
echo.
echo Ce script va surveiller et envoyer automatiquement
echo les questionnaires quand leur heure arrive.
echo.
echo Appuyez sur Ctrl+C pour arrêter la surveillance.
echo.
pause

:loop
echo.
echo [%date% %time%] Lancement de la surveillance...
php surveillance_automatique_continue.php

echo.
echo La surveillance s'est arrêtée. Redémarrage dans 5 secondes...
timeout /t 5 /nobreak >nul
goto loop 