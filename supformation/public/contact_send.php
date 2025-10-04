<?php
// public/contact_send.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
if (!verify_csrf_token($_POST['csrf'] ?? '')) { http_response_code(400); exit; }

$name = trim($_POST['name'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$phone = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? 'Contact site');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) { echo "Remplir tous les champs requis"; exit; }

// save in DB
$stmt = $pdo->prepare("INSERT INTO contacts (name,email,phone,subject,message) VALUES (:n,:e,:p,:s,:m)");
$stmt->execute([':n'=>$name,':e'=>$email,':p'=>$phone,':s'=>$subject,':m'=>$message]);

// send mail to admin
$adminEmail = 'admin@supformation.example'; // remplace
$html = "<p>Message reçu depuis le site :</p>
<p><strong>Nom:</strong> ".htmlspecialchars($name)."</p>
<p><strong>Email:</strong> ".htmlspecialchars($email)."</p>
<p><strong>Message:</strong><br>".nl2br(htmlspecialchars($message))."</p>";

send_mail($adminEmail, "Contact site - $subject", $html);

echo "Message envoyé. Nous vous contacterons bientôt.";
