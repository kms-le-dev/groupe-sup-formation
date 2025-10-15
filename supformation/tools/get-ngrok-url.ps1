# get-ngrok-url.ps1
# Lit et affiche le contenu de ngrok-url.txt si présent
$repoRoot = (Resolve-Path "$PSScriptRoot\..")
$outFile = Join-Path $repoRoot 'ngrok-url.txt'
if (Test-Path $outFile) {
    $u = Get-Content $outFile -Raw
    Write-Host "ngrok public URL: $u"
    exit 0
} else {
    Write-Error "Fichier ngrok-url.txt introuvable. Lancez tools\start-ngrok.ps1 d'abord."
    exit 1
}
