<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $msg = trim($_POST['message']);

    if (empty($nom) || empty($email) || empty($msg)) {
        $message = "Veuillez remplir tous les champs obligatoires.";
    } else {
        // Envoi email via PHPMailer 
        require __DIR__ . '/../vendor/autoload.php';

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ton_email@gmail.com';
            $mail->Password = 'ton_mot_de_passe_app';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($email, $nom);
            $mail->addAddress('admin@supformation.com', 'Admin SupFormation');

            $mail->isHTML(true);
            $mail->Subject = "Message contact - $nom";
            $mail->Body = "<p><b>Nom:</b> $nom</p>
                           <p><b>Email:</b> $email</p>
                           <p><b>Téléphone:</b> $telephone</p>
                           <p><b>Message:</b> $msg</p>";

            $mail->send();
            $message = "Votre message a été envoyé avec succès !";
        } catch (Exception $e) {
            $message = "Erreur lors de l'envoi du message: {$mail->ErrorInfo}";
        }
    }
}
?>






<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Groupe Sup'Formation</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<h1>Contact</h1>

<?php if ($message): ?>
    <div class="alert"><?= e($message) ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label>Nom *</label>
    <input type="text" name="nom" required>
    <label>Email *</label>
    <input type="email" name="email" required>
    <label>Téléphone</label>
    <input type="text" name="telephone">
    <label>Message *</label>
    <textarea name="message" required></textarea>
    <button type="submit">Envoyer</button>
</form>

<div class="map">
    <h3>Notre localisation</h3>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18..." width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="assets/js/main.js" defer></script>
</body>
</html>