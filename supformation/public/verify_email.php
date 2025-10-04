<?php
// public/verify_email.php
require_once __DIR__ . '/../includes/config.php';
$token = $_GET['token'] ?? '';
if (!$token) { echo "Token manquant"; exit; }

$stmt = $pdo->prepare("UPDATE users SET email_verified=1, email_verification_token=NULL WHERE email_verification_token = :t LIMIT 1");
$stmt->execute([':t'=>$token]);
if ($stmt->rowCount()){
    echo "Email vérifié. Vous pouvez vous connecter.";
} else {
    echo "Token invalide ou déjà utilisé.";
}
