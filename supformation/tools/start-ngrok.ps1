# start-ngrok.ps1
# Lance ngrok (windows binary fourni dans public/ngrok-v3-stable-windows-amd64)
# Capture l'URL publique HTTP et HTTPS via l'API locale d'ngrok et écrit dans ngrok-url.txt
# Usage: .\start-ngrok.ps1 -Port 80
param(
    [int]$Port = 80,
    [string]$NgrokPath = "$PSScriptRoot\..\public\ngrok-v3-stable-windows-amd64\ngrok.exe",
    [int]$WaitSeconds = 1
)

# Resolve absolute path
$NgrokPath = (Resolve-Path $NgrokPath).Path
if (!(Test-Path $NgrokPath)) {
    Write-Error "ngrok introuvable à $NgrokPath. Vérifiez le chemin ou placez ngrok.exe dans public/ngrok-v3-stable-windows-amd64/"
    exit 2
}

# Start ngrok in background
$ngrokArgs = "http $Port --log=stdout --log-format=json"
Write-Host "Démarrage de ngrok: $NgrokPath $ngrokArgs"
# Start-Process will create a new window by default; use -NoNewWindow for current console
$startInfo = New-Object System.Diagnostics.ProcessStartInfo
$startInfo.FileName = $NgrokPath
$startInfo.Arguments = $ngrokArgs
$startInfo.RedirectStandardOutput = $true
$startInfo.UseShellExecute = $false
$startInfo.CreateNoWindow = $true
$process = [System.Diagnostics.Process]::Start($startInfo)
Start-Sleep -Seconds $WaitSeconds

# Try to query ngrok's API at http://127.0.0.1:4040/api/tunnels
$apiUrl = 'http://127.0.0.1:4040/api/tunnels'
$attempts = 0
$maxAttempts = 12
$tunnels = $null
while ($attempts -lt $maxAttempts) {
    try {
        $resp = Invoke-RestMethod -Uri $apiUrl -Method Get -ErrorAction Stop -TimeoutSec 2
        $tunnels = $resp.tunnels
        if ($tunnels) { break }
    } catch {
        Start-Sleep -Milliseconds 500
    }
    $attempts++
}

if (-not $tunnels) {
    Write-Error "Impossible de récupérer les tunnels ngrok via l'API locale après $maxAttempts tentatives. Vérifiez que ngrok est lancé et que le port 4040 est disponible."

    # Diagnostics supplémentaires pour aider au debugging
    Write-Host "--- Diagnostics ngrok ---"
    try {
        $proc = Get-Process -Name ngrok -ErrorAction SilentlyContinue
        if ($proc) {
            Write-Host "Process ngrok trouvé : PID=$($proc.Id) (StartTime=$($proc.StartTime))"
        } else {
            Write-Host "Aucun process ngrok trouvé. ngrok n'a peut-être pas démarré correctement."
        }
    } catch { Write-Host "Impossible d'interroger les processus (permissions) : $_" }

    Write-Host "Vérification des sockets écoutant sur le port 4040 (netstat) :"
    try {
        netstat -aon | Select-String ":4040" | ForEach-Object { Write-Host $_ }
    } catch { Write-Host "netstat indisponible ou erreur : $_" }

    Write-Host "Si vous voulez voir pourquoi ngrok n'arrive pas à exposer le tunnel, lancez manuellement la commande suivante dans cette console pour voir la sortie/erreurs :"
    Write-Host "& '$NgrokPath' http $Port --log=stdout --log-format=json"
    Write-Host "Ou exécutez : & '$NgrokPath' http $Port"
    Write-Host "Si ngrok affiche une erreur (ex: problème d'auth token, port déjà utilisé, permission), corrigez-la puis relancez ce script."

    exit 3
}

# Get first HTTP/HTTPS public url
$http = $tunnels | Where-Object { $_.proto -eq 'http' } | Select-Object -First 1
if (-not $http) { $http = $tunnels[0] }
$publicUrl = $http.public_url

# Save to file at repo root
$repoRoot = (Resolve-Path "$PSScriptRoot\..")
$outFile = Join-Path $repoRoot 'ngrok-url.txt'
Set-Content -Path $outFile -Value $publicUrl -Encoding UTF8
Write-Host "URL ngrok écrite dans $outFile : $publicUrl"

# Also print it
Write-Output $publicUrl

# Keep process running in background (it is started already). We return the PID for user convenience.
Write-Host "ngrok PID: $($process.Id)"

# Exit with success
exit 0
