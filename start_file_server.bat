@echo off
echo ========================================
echo   SERVEUR DE FICHIERS LOCAL
echo ========================================
echo.
echo Votre PC actuel: %COMPUTERNAME%
echo Adresse IP: 192.168.1.12
echo Port: 8000
echo.
echo Pour acceder depuis l'autre PC:
echo http://192.168.1.12:8000
echo.
echo Appuyez sur Ctrl+C pour arreter le serveur
echo ========================================
echo.

cd /d "%~dp0"
python -m http.server 8000

pause
