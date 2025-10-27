@echo off
echo ========================================
echo   TEST D'ACCES LARAVEL EN LOCAL
echo ========================================
echo.
echo Votre PC serveur: %COMPUTERNAME%
echo Adresse IP: 192.168.1.12
echo Port Apache: 80
echo.
echo ========================================
echo   INFORMATIONS IMPORTANTES
echo ========================================
echo.
echo Pour acceder depuis l'autre PC:
echo.
echo 1. Assurez-vous que l'autre PC est sur le meme reseau
echo 2. Sur l'autre PC, ouvrez le navigateur
echo 3. Tapez: http://192.168.1.12
echo.
echo Si vous avez un virtual host Laravel:
echo http://192.168.1.12/votre_projet_laravel/public
echo.
echo ========================================
echo   TEST DE CONNECTIVITE
echo ========================================
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
echo ========================================
echo   SERVICES ACTIFS
echo ========================================
echo.
echo Ports en ecoute:
netstat -an | findstr ":80"
echo.
echo Services Apache:
sc query | findstr "Apache"
echo.
echo ========================================
echo   CONFIGURATION LARAVEL
echo ========================================
echo.
echo Verifiez que votre projet Laravel est dans:
echo C:\xampp\htdocs\votre_projet_laravel\
echo.
echo Et que le DocumentRoot pointe vers:
echo C:\xampp\htdocs\votre_projet_laravel\public\
echo.
echo ========================================
echo.
echo Appuyez sur une touche pour continuer...
pause >nul

echo.
echo Test d'acces local...
curl -s -o nul -w "HTTP Status: %%{http_code}\n" http://localhost >nul 2>&1
if %errorlevel% equ 0 (
    echo [OK] Serveur local accessible
) else (
    echo [ERREUR] Serveur local inaccessible
)

echo.
echo ========================================
echo   RESUME
echo ========================================
echo.
echo Si tous les tests sont OK:
echo 1. Votre PC serveur est pret
echo 2. L'autre PC peut acceder via: http://192.168.1.12
echo 3. Votre projet Laravel sera accessible
echo.
echo En cas de probleme:
echo - Verifiez que XAMPP est demarre
echo - Verifiez la configuration Apache
echo - Verifiez le pare-feu Windows
echo.
pause
