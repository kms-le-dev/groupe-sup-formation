<?php
// send_form.php
// Reçoit POST multipart/form-data contenant :
// - pdf_base64 (base64 string)
// - pdf_filename
// - photo_file (optionnel)
// - autres champs du formulaire

// Configuration
$admin_email = "groupesupformation@gmail.com"; // Remplace par ton email admin
$save_dir = __DIR__ . "/uploads";
if (!file_exists($save_dir)) mkdir($save_dir, 0755, true);

// Helper sanitize
function s($v){ return htmlspecialchars(trim($v ?? ''), ENT_QUOTES, 'UTF-8'); }

// Collect POST fields (safety)
$fields = [];
$expected = [
  'nom_prenom','date_naissance','lieu_naissance','telephone','email',
  'niveau_actuel','lieu_residence','formation_demandee','niveau_demandee',
  'filiere_demandee','type_cours_autre','contact_urgence_type','contact_urgence_autre',
  'contact_nom_prenom','contact_lieu','contact_telephone','mode_paiement','num_paiement'
];
foreach($expected as $k) {
  $fields[$k] = s($_POST[$k] ?? '');
}
// type_cours[] may have multiple values
$type_cours = [];
if(isset($_POST['type_cours[]'])) {
  $type_cours = $_POST['type_cours[]']; // array
} elseif(isset($_POST['type_cours'])) {
  // sometimes keys vary
  if(is_array($_POST['type_cours'])) $type_cours = $_POST['type_cours'];
  else $type_cours = [s($_POST['type_cours'])];
}

// pdf data
$pdf_base64 = $_POST['pdf_base64'] ?? null;
$pdf_filename = $_POST['pdf_filename'] ?? 'fiche_inscription.pdf';

$result = ['success'=>false];

try {
  if(!$pdf_base64) throw new Exception('PDF non fourni.');

  // Decode & save PDF
  $pdf_data = base64_decode($pdf_base64);
  if($pdf_data === false) throw new Exception('Erreur décodage PDF.');

  $unique = date('Ymd_His') . '_' . bin2hex(random_bytes(4));
  $pdf_path = $save_dir . '/' . pathinfo($pdf_filename, PATHINFO_FILENAME) . "_{$unique}.pdf";
  file_put_contents($pdf_path, $pdf_data);

  // Save photo if uploaded
  $photo_path = null;
  if(isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['photo_file']['tmp_name'];
    $orig = basename($_FILES['photo_file']['name']);
    $ext = pathinfo($orig, PATHINFO_EXTENSION);
    $photo_path = $save_dir . '/photo_' . $unique . '.' . $ext;
    move_uploaded_file($tmp, $photo_path);
  }

  // Build email
  $to = $admin_email;
  $subject = "Nouvelle inscription : " . ($fields['nom_prenom'] ?: '—');
  $boundary = md5(time());
  $messageText = "Une nouvelle fiche d'inscription a été soumise.\n\nDétails :\n";
  foreach($fields as $k=>$v) {
    $messageText .= "$k : $v\n";
  }
  $messageText .= "Type cours: ".implode(', ', $type_cours)."\n";
  $messageText .= "PDF enregistré: $pdf_path\n";
  if($photo_path) $messageText .= "Photo enregistrée: $photo_path\n";

  // Headers
  $headers = "From: no-reply@" . $_SERVER['SERVER_NAME'] . "\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n\r\n";

  // Body
  $body = "--{$boundary}\r\n";
  $body .= "Content-Type: text/plain; charset=utf-8\r\n\r\n";
  $body .= $messageText . "\r\n\r\n";

  // Attach PDF
  $pdf_contents = file_get_contents($pdf_path);
  $pdf_b64 = chunk_split(base64_encode($pdf_contents));
  $body .= "--{$boundary}\r\n";
  $body .= "Content-Type: application/pdf; name=\"" . basename($pdf_path) . "\"\r\n";
  $body .= "Content-Transfer-Encoding: base64\r\n";
  $body .= "Content-Disposition: attachment; filename=\"" . basename($pdf_path) . "\"\r\n\r\n";
  $body .= $pdf_b64 . "\r\n\r\n";

  // Attach photo if any
  if($photo_path && file_exists($photo_path)){
    $photo_contents = file_get_contents($photo_path);
    $photo_b64 = chunk_split(base64_encode($photo_contents));
    $mime = mime_content_type($photo_path);
    $body .= "--{$boundary}\r\n";
    $body .= "Content-Type: {$mime}; name=\"" . basename($photo_path) . "\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"" . basename($photo_path) . "\"\r\n\r\n";
    $body .= $photo_b64 . "\r\n\r\n";
  }

  $body .= "--{$boundary}--";

  // Send email
  $mailSent = mail($to, $subject, $body, $headers);

  if(!$mailSent){
    // If mail() fails, still return success=true (file saved), but indicate mail failed
    $result['success'] = true;
    $result['message'] = 'PDF enregistré, mais envoi email échoué (mail function non configurée). Fichiers sauvegardés.';
    $result['pdf_path'] = $pdf_path;
    if($photo_path) $result['photo_path'] = $photo_path;
  } else {
    $result['success'] = true;
    $result['message'] = 'PDF enregistré et email envoyé.';
    $result['pdf_path'] = $pdf_path;
    if($photo_path) $result['photo_path'] = $photo_path;
  }

} catch(Exception $e) {
  $result['success'] = false;
  $result['message'] = 'Erreur: ' . $e->getMessage();
}

// Return JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
exit;
