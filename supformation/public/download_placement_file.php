<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Vérifier que l'utilisateur est admin
if (!is_admin()) {
    http_response_code(403);
    die('Accès refusé');
}

// Récupérer l'ID du placement et le type de fichier
$id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? null; // 'form', 'cv' ou 'cover'

if (!$id || !$type || !in_array($type, ['form', 'cv', 'cover'])) {
    http_response_code(400);
    die('Paramètres invalides');
}

try {
    // Récupérer les informations du fichier
    $stmt = $pdo->prepare('SELECT filename, cv_file, cover_file FROM placements WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        die('Fichier non trouvé');
    }

    // Déterminer le chemin du fichier selon le type
    $filename = null;
    switch ($type) {
        case 'form':
            $filename = $row['filename'];
            break;
        case 'cv':
            $filename = $row['cv_file'];
            break;
        case 'cover':
            $filename = $row['cover_file'];
            break;
    }

    if (!$filename) {
        http_response_code(404);
        die('Fichier non disponible');
    }

    // Construire le chemin complet et vérifier l'existence
    $publicRoot = __DIR__;
    $candidates = [
        $publicRoot . '/' . $filename,
        $publicRoot . '/uploads/' . ltrim($filename, '/'),
        $publicRoot . '/uploads/placements/' . basename($filename),
        $publicRoot . '/' . basename($filename)
    ];

    $filePath = null;
    foreach ($candidates as $candidate) {
        if (file_exists($candidate)) {
            $filePath = $candidate;
            break;
        }
    }

    if (!$filePath || !is_readable($filePath)) {
        http_response_code(404);
        die('Fichier non trouvé sur le serveur');
    }

    // Forcer le téléchargement
    $basename = basename($filePath);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $basename . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');

    readfile($filePath);
    exit;

} catch (Exception $e) {
    error_log('Erreur téléchargement placement: ' . $e->getMessage());
    http_response_code(500);
    die('Erreur serveur');
}