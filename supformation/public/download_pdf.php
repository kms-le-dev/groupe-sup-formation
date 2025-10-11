<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Vérification de sécurité
session_start();
if (!current_user()) {
    header('HTTP/1.1 403 Forbidden');
    exit('Accès refusé');
}

// Récupération et validation du nom de fichier
$filename = isset($_GET['file']) ? basename($_GET['file']) : null;
if (!$filename) {
    header('HTTP/1.1 400 Bad Request');
    exit('Fichier non spécifié');
}

// Chemin complet du fichier
$filepath = __DIR__ . '/uploads/fdfp/' . $filename;

// Debug
error_log('Tentative d\'accès au fichier: ' . $filepath);

// Vérification de l'existence et de la sécurité du chemin
if (!file_exists($filepath)) {
    error_log('Fichier non trouvé: ' . $filepath);
    header('HTTP/1.1 404 Not Found');
    exit('Fichier non trouvé');
}

if (!is_file($filepath)) {
    error_log('N\'est pas un fichier: ' . $filepath);
    header('HTTP/1.1 400 Bad Request');
    exit('Type de fichier invalide');
}

// Vérification supplémentaire de la validité du PDF
$f = @fopen($filepath, 'rb');
if ($f) {
    $header = fread($f, 4);
    fclose($f);
    if ($header !== '%PDF') {
        error_log('Fichier non PDF: ' . $filepath);
        header('HTTP/1.1 400 Bad Request');
        exit('Format de fichier invalide');
    }
}
    header('HTTP/1.1 404 Not Found');
    exit('Fichier non trouvé');


// Forcer le téléchargement avec le bon type MIME
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: private, no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Envoi du fichier
readfile($filepath);
exit;