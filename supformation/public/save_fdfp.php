<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
header('Content-Type: application/json');
try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid method');
  $uploadDir = __DIR__ . '/uploads/fdfp';
  if (!is_dir($uploadDir)) mkdir($uploadDir,0755,true);
  $pdo->exec("CREATE TABLE IF NOT EXISTS fdfp_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    meta JSON NULL,
    filename VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
  $meta = $_POST['meta'] ?? null;
  // normalize meta: if client sent an array/object via FormData, ensure we store a JSON string
  if (is_array($meta)) {
    $meta = json_encode($meta, JSON_UNESCAPED_UNICODE);
  }
  $target = null;
  if (!isset($_FILES['pdf']) || empty($_FILES['pdf']['tmp_name'])) {
    // fallback create simple PDF
    $text = $meta ?: 'FDFP submission';
    $pdfContent = generate_simple_pdf(is_string($text)?$text:json_encode($text));
    $basename = 'fdfp_' . time() . '.pdf';
    $target = $uploadDir . '/' . $basename;
    if (file_put_contents($target, $pdfContent) === false) throw new Exception('Impossible de créer le fichier');
  } else {
    $file = $_FILES['pdf'];
    if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Upload error');
    $basename = preg_replace('/[^a-zA-Z0-9_\-\.]/','_', basename($file['name']));
    $target = $uploadDir . '/' . time() . '_' . $basename;
    if (!move_uploaded_file($file['tmp_name'], $target)) throw new Exception('Move failed');
  }
  // store a predictable path so admin can reliably find it
  $storedFilename = 'uploads/fdfp/' . basename($target);
  $stmt = $pdo->prepare('INSERT INTO fdfp_submissions (meta, filename) VALUES (?, ?)');
  $stmt->execute([$meta, $storedFilename]);
  $id = $pdo->lastInsertId();
  // no debug logging in production
  // return an absolute URL to make client navigation reliable
  $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] : '') . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
  $resUrl = $base . '/uploads/fdfp/' . basename($target);
  echo json_encode(['success'=>true,'id'=>$id,'url'=>$resUrl,'filename'=>basename($target),'stored'=>$storedFilename]);
  exit;
} catch (Exception $e) {
  // no debug logging in production
  http_response_code(400);
  echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
  exit;
}

function generate_simple_pdf(string $text): string {
  // reuse placement simple PDF generator style (minimal)
  $lines = preg_split('/\r?\n/', wordwrap($text, 80, "\n"));
  $stream = "BT /F1 12 Tf 50 750 Td\n";
  foreach ($lines as $i => $ln) {
    $safe = addcslashes($ln, "\\()\n\r");
    $stream .= "(" . $safe . ") Tj\n";
    if ($i < count($lines)-1) $stream .= "0 -14 Td\n";
  }
  $stream .= "ET\n";
  $objects = [];
  $objects[1] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
  $objects[2] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
  $objects[3] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /MediaBox [0 0 612 792] /Contents 5 0 R >>\nendobj\n";
  $objects[4] = "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";
  $objects[5] = "5 0 obj\n<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "endstream\nendobj\n";
  $pdf = "%PDF-1.1\n";
  $offsets = [];
  foreach ($objects as $i => $obj) { $offsets[$i] = strlen($pdf); $pdf .= $obj; }
  $xrefPos = strlen($pdf);
  $count = count($objects) + 1;
  $xref = "xref\n0 $count\n";
  $xref .= sprintf("%010d 65535 f\n", 0);
  for ($i=1;$i<=count($objects);$i++) $xref .= sprintf("%010d 00000 n\n", $offsets[$i]);
  $trailer = "trailer\n<< /Size $count /Root 1 0 R >>\nstartxref\n" . $xrefPos . "\n%%EOF\n";
  $pdf .= $xref . $trailer;
  return $pdf;
}
