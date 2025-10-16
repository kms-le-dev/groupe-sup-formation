<?php
// Use an output buffer so any accidental output (whitespace, includes, debug echos)
// won't corrupt the JSON response expected by the client.
ob_start();

// Do not display PHP errors in the response. Log them instead.
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Remove any output that may have been produced by includes before sending JSON
if (ob_get_length()) ob_clean();

// endpoint to accept placement PDFs and metadata
header('Content-Type: application/json; charset=utf-8');

// Helper to send JSON after clearing any accidental output
function send_json($data, int $status = 200): void {
  // clear any output produced so far
  if (ob_get_length()) ob_clean();
  http_response_code($status);
  header('Content-Type: application/json; charset=utf-8');
  // use UNESCAPED_UNICODE for readability, suppress errors if any
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  // flush and end
  if (function_exists('fastcgi_finish_request')) {
    @fastcgi_finish_request();
  }
  exit;
}

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid method');

  // ensure uploads dir
  $uploadDir = __DIR__ . '/uploads/placements';
  if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

  // create table if not exists (ajout cv_file et cover_file)
  $pdo->exec("CREATE TABLE IF NOT EXISTS placements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(40) NOT NULL,
    meta JSON NULL,
    filename VARCHAR(255) NOT NULL,
    cv_file VARCHAR(255) NULL,
    cover_file VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

  $type = $_POST['type'] ?? 'unknown';
  $meta = $_POST['meta'] ?? null;
  // Gestion des fichiers PDF principaux et pièces jointes (CV et lettre de motivation)
  $target = null;
  $cvPath = null;
  $coverPath = null;

  // Sauvegarde du PDF principal (formulaire)
  if (!isset($_FILES['pdf']) || empty($_FILES['pdf']['tmp_name'])) {
    $text = '';
    if ($meta) {
      $text = is_string($meta) ? $meta : json_encode($meta);
    }
    $text = trim($text) ?: ("Soumission type: " . $type);
    $pdfContent = generate_simple_pdf($text);
    $basename = 'placement_' . time() . '.pdf';
    $target = $uploadDir . '/' . $basename;
    if (file_put_contents($target, $pdfContent) === false) throw new Exception('Impossible de créer le fichier PDF');
  } else {
    $file = $_FILES['pdf'];
    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Upload error');
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $basename = preg_replace('/[^a-zA-Z0-9_\-\.]/','_', basename($file['name']));
    $target = $uploadDir . '/' . time() . '_' . $basename;
    if (!move_uploaded_file($file['tmp_name'], $target)) throw new Exception('Move failed');
    // validate that the uploaded file is a real PDF (starts with %PDF)
    $validPdf = false;
    $fh = @fopen($target, 'rb');
    if ($fh) {
      $head = fread($fh, 4);
      fclose($fh);
      if ($head === "%PDF") $validPdf = true;
    }
    if (!$validPdf) {
      $uploadedText = @file_get_contents($target);
      $pdfContent = generate_simple_pdf($uploadedText ?: ($meta ?: $type));
      if (file_put_contents($target, $pdfContent) === false) {
        throw new Exception('Impossible d\'écrire le PDF de remplacement');
      }
      if (strtolower(pathinfo($target, PATHINFO_EXTENSION)) !== 'pdf') {
        $newTarget = preg_replace('/\.[^.]+$/', '', $target) . '.pdf';
        @rename($target, $newTarget);
        $target = $newTarget;
      }
    }
  }

  // Sauvegarde du CV
  if (isset($_FILES['cv']) && !empty($_FILES['cv']['tmp_name'])) {
    $cvFile = $_FILES['cv'];
    if ($cvFile['error'] === UPLOAD_ERR_OK) {
      $cvExt = pathinfo($cvFile['name'], PATHINFO_EXTENSION);
      $cvBase = 'cv_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/','_', basename($cvFile['name']));
      $cvPath = $uploadDir . '/' . $cvBase;
      if (move_uploaded_file($cvFile['tmp_name'], $cvPath)) {
        $cvPath = 'placements/' . basename($cvPath);
      } else {
        $cvPath = null;
      }
    }
  }

  // Sauvegarde de la lettre de motivation
  if (isset($_FILES['cover']) && !empty($_FILES['cover']['tmp_name'])) {
    $coverFile = $_FILES['cover'];
    if ($coverFile['error'] === UPLOAD_ERR_OK) {
      $coverExt = pathinfo($coverFile['name'], PATHINFO_EXTENSION);
      $coverBase = 'cover_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/','_', basename($coverFile['name']));
      $coverPath = $uploadDir . '/' . $coverBase;
      if (move_uploaded_file($coverFile['tmp_name'], $coverPath)) {
        $coverPath = 'placements/' . basename($coverPath);
      } else {
        $coverPath = null;
      }
    }
  }

  // save to DB (ajout cv_file et cover_file)
  $stmt = $pdo->prepare('INSERT INTO placements (type, meta, filename, cv_file, cover_file) VALUES (?, ?, ?, ?, ?)');
  $stmt->execute([$type, $meta, 'placements/' . basename($target), $cvPath, $coverPath]);
  $id = $pdo->lastInsertId();

  // Prepare WhatsApp link (client can open) - message includes a summary
  $phone = '+2250706591243';
  // Decode meta if it's JSON and normalize into an ordered array of fields
  $fields = [];
  if ($meta) {
    if (is_string($meta)) {
      $decoded = json_decode($meta, true);
      if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $metaArr = $decoded;
      } else {
        // if it's not valid JSON, keep as raw text under 'message'
        $metaArr = ['message' => $meta];
      }
    } elseif (is_array($meta)) {
      $metaArr = $meta;
    } else {
      $metaArr = ['message' => (string)$meta];
    }
  } else {
    $metaArr = [];
  }

  // Define preferred order and readable labels
  $order = [
    'Nom de l\'entreprise' => 'Entreprise',
    'Domaine d_activite' => 'Domaine d\'activité',
    'Adresse geographique' => 'Adresse',
    'contacts' => 'Contacts',
    'Email' => 'Email',
    'Niveau de qualification' => 'Niveau(x) de qualification',
    'Nombre d_employe souhaité' => 'Nombre d\'employés souhaités',
    'Numero DFE' => 'Numéro DFE',
    'RCCM' => 'RCCM'
  ];

  // helper to sanitize values
  $sanitize_text = function ($v) {
    $s = strip_tags((string)$v);
    // remove control chars
    $s = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $s);
    // collapse multiple spaces
    $s = preg_replace('/\s+/u', ' ', $s);
    // trim
    $s = trim($s);
    // remove weird long punctuation sequences but keep common punctuation
    $s = preg_replace('/[\*\~\`\^\|\[\]{}<>]/u', '', $s);
    return $s;
  };

  // Fill fields from ordered keys
  foreach ($order as $key => $label) {
    if ($key === 'type') {
      // use the $type param first
      $val = $type;
    } elseif (array_key_exists($key, $metaArr)) {
      $val = $metaArr[$key];
    } else {
      // try variants (e.g., firstname/lastname vs name)
      if ($key === 'name' && isset($metaArr['firstname']) && isset($metaArr['lastname'])) {
        $val = $metaArr['firstname'] . ' ' . $metaArr['lastname'];
      } else {
        $val = null;
      }
    }
    if ($val !== null && $val !== '') {
      if ($key === 'Niveau de qualification' && is_array($val)) {
        $fields[$label] = "\n▫️ " . implode("\n▫️ ", array_map($sanitize_text, $val));
      } else {
        $fields[$label] = $sanitize_text($val);
      }
    }
  }

  // For any remaining keys in metaArr not in order, append them at the end
  foreach ($metaArr as $k => $v) {
    if (!in_array($k, array_keys($order))) {
      $label = ucfirst(str_replace('_', ' ', $k));
      $fields[$label] = $sanitize_text($v);
    }
  }

  // Compose message lines
  $lines = [];
  $lines[] = "Nouvelle soumission placement";
  foreach ($fields as $label => $val) {
    $lines[] = "$label: $val";
  }
  $messageText = implode("\n", $lines);

  // short preview for logging or display
  $preview = mb_substr(preg_replace('/\s+/u', ' ', $messageText), 0, 400);
  $msg = urlencode($messageText);
  $waLink = "https://wa.me/{$phone}?text={$msg}";

  $resUrl = dirname($_SERVER['SCRIPT_NAME']) . '/uploads/placements/' . basename($target);
  $cvUrl = $cvPath ? (dirname($_SERVER['SCRIPT_NAME']) . '/uploads/placements/' . basename($cvPath)) : null;
  $coverUrl = $coverPath ? (dirname($_SERVER['SCRIPT_NAME']) . '/uploads/placements/' . basename($coverPath)) : null;

  send_json([
    'success'=>true,
    'id'=>$id,
    'url'=>$resUrl,
    'filename'=>basename($target),
    'cv_url'=>$cvUrl,
    'cover_url'=>$coverUrl,
    'whatsapp'=>$waLink
  ], 200);
} catch (Exception $e) {
  send_json([
    'success'=>false,
    'error'=>$e->getMessage(),
    'trace'=>$e->getTraceAsString()
  ], 400);
}

/**
 * Génère un PDF très minimaliste (1 page) contenant le texte fourni.
 * Ceci permet d'avoir un fichier PDF valide si le client envoie seulement du texte.
 * Note: méthode basique, ne remplace pas une vraie lib PDF.
 */
function generate_simple_pdf(string $text): string {
  // Build a minimal valid PDF by calculating object byte offsets and a proper xref.
  $lines = preg_split('/\r?\n/', wordwrap($text, 80, "\n"));
  // Build stream by explicit moves to avoid reliance on T* leading
  $stream = "BT /F1 12 Tf 50 750 Td\n";
  $count = count($lines);
  foreach ($lines as $i => $ln) {
    $safe = addcslashes($ln, "\\()\n\r");
    $stream .= "(" . $safe . ") Tj\n";
    if ($i < $count - 1) {
      // move down by 14 units
      $stream .= "0 -14 Td\n";
    }
  }
  $stream .= "ET\n";

  // Define PDF objects (1..5)
  $objects = [];
  $objects[1] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
  $objects[2] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
  $objects[3] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /MediaBox [0 0 612 792] /Contents 5 0 R >>\nendobj\n";
  $objects[4] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
  $objects[5] = "5 0 obj\n<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream\nendobj\n";

  // Build the PDF and collect offsets
  $header = "%PDF-1.1\n";
  $pdf = $header;
  $offsets = [];
  foreach ($objects as $i => $obj) {
    $offsets[$i] = strlen($pdf);
    $pdf .= $obj;
  }

  // xref
  $xrefPos = strlen($pdf);
  $count = count($objects) + 1; // include object 0
  $xref = "xref\n0 $count\n";
  $xref .= sprintf("%010d 65535 f\n", 0);
  for ($i = 1; $i <= count($objects); $i++) {
    $xref .= sprintf("%010d 00000 n\n", $offsets[$i]);
  }

  $trailer = "trailer\n<< /Size $count /Root 1 0 R >>\nstartxref\n" . $xrefPos . "\n%%EOF\n";
  $pdf .= $xref . $trailer;
  return $pdf;
}
