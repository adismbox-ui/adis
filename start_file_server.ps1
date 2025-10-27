# Script PowerShell pour serveur de fichiers local
Write-Host "========================================" -ForegroundColor Green
Write-Host "   SERVEUR DE FICHIERS LOCAL" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Votre PC actuel: $env:COMPUTERNAME" -ForegroundColor Yellow
Write-Host "Adresse IP: 192.168.1.12" -ForegroundColor Yellow
Write-Host "Port: 8000" -ForegroundColor Yellow
Write-Host ""
Write-Host "Pour acceder depuis l'autre PC:" -ForegroundColor Cyan
Write-Host "http://192.168.1.12:8000" -ForegroundColor Cyan
Write-Host ""
Write-Host "Appuyez sur Ctrl+C pour arreter le serveur" -ForegroundColor Red
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

# Démarrer le serveur HTTP simple
$listener = New-Object System.Net.HttpListener
$listener.Prefixes.Add("http://+:8000/")
$listener.Start()

Write-Host "Serveur demarre sur le port 8000..." -ForegroundColor Green

try {
    while ($listener.IsListening) {
        $context = $listener.GetContext()
        $request = $context.Request
        $response = $context.Response
        
        $localPath = $request.Url.LocalPath
        $filePath = Join-Path (Get-Location) $localPath.TrimStart('/')
        
        if (Test-Path $filePath -PathType Leaf) {
            $content = Get-Content $filePath -Raw -Encoding UTF8
            $buffer = [System.Text.Encoding]::UTF8.GetBytes($content)
            $response.ContentLength64 = $buffer.Length
            $response.OutputStream.Write($buffer, 0, $buffer.Length)
        } else {
            $response.StatusCode = 404
            $content = "Fichier non trouve: $localPath"
            $buffer = [System.Text.Encoding]::UTF8.GetBytes($content)
            $response.ContentLength64 = $buffer.Length
            $response.OutputStream.Write($buffer, 0, $buffer.Length)
        }
        
        $response.Close()
    }
} finally {
    $listener.Stop()
}
