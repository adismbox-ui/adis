@echo off
echo ========================================
echo   TEST DE CONNECTIVITE RESEAU
echo ========================================
echo.

echo Votre PC actuel: %COMPUTERNAME%
echo Adresse IP: 192.168.1.12
echo.

echo Test de connectivite locale...
ping -n 1 127.0.0.1 >nul
if %errorlevel% equ 0 (
    echo [OK] Connectivite locale: OK
) else (
    echo [ERREUR] Connectivite locale: ECHEC
)

echo.
echo Test de connectivite reseau...
ping -n 1 192.168.1.1 >nul
if %errorlevel% equ 0 (
    echo [OK] Routeur accessible: OK
) else (
    echo [ERREUR] Routeur inaccessible
)

echo.
echo Test de connectivite Internet...
ping -n 1 8.8.8.8 >nul
if %errorlevel% equ 0 (
    echo [OK] Internet accessible: OK
) else (
    echo [ERREUR] Internet inaccessible
)

echo.
echo ========================================
echo   INFORMATIONS RESEAU
echo ========================================
echo.
echo Adresses IP actives:
ipconfig | findstr "IPv4"
echo.
echo Connexions actives:
netstat -an | findstr ":8000"
echo.
echo ========================================
echo.
echo Pour connecter l'autre PC:
echo 1. Assurez-vous qu'il est sur le meme reseau
echo 2. Sur l'autre PC, tapez: http://192.168.1.12:8000
echo 3. Ou utilisez le partage: \\192.168.1.12\adis
echo.
pause
