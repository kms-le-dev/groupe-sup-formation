<?php
// public/register.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // show form
    $csrf = generate_csrf_token();
    include __DIR__ . '/../templates/register_form.php';
    exit;
}

// POST handling
if (!verify_csrf_token($_POST['csrf'] ?? '')) {
    http_response_code(400); echo "Invalid CSRF token"; exit;
}

$first = trim($_POST['first_name'] ?? '');
$last = trim($_POST['last_name'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$phone = trim($_POST['phone'] ?? '');
$pass = $_POST['password'] ?? '';
$pass2 = $_POST['password_confirm'] ?? '';

if (!$email) { echo "Email invalide"; exit;}
if ($pass !== $pass2 || strlen($pass) < 8) { echo "Mots de passe incorrects"; exit; }

// check email existence (simple pattern + MX record check)
$domain = explode('@',$email)[1] ?? '';
if ($domain && !checkdnsrr($domain,'MX')) {
    echo "Domaine email invalide"; exit;
}

$passwordHash = password_hash($pass, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));

$stmt = $pdo->prepare("INSERT INTO users (role_id, first_name, last_name, email, email_verified, email_verification_token, phone, password_hash) VALUES (1, :fn, :ln, :email, 0, :token, :phone, :hash)");
try {
    $stmt->execute([':fn'=>$first,':ln'=>$last,':email'=>$email,':token'=>$token,':phone'=>$phone,':hash'=>$passwordHash]);
    $userId = $pdo->lastInsertId();

    $verifyUrl = BASE_URL . "/verify_email.php?token=$token";
    $html = "<p>Bonjour $first,</p><p>Merci de vous inscrire. Cliquez ici pour vérifier votre email : <a href=\"$verifyUrl\">Vérifier l'email</a></p>";
    send_mail($email, "Vérification email Sup'Formation", $html);

    echo "Inscription réussie. Vérifiez votre email pour activer votre compte.";
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        echo "Email déjà utilisé.";
    } else {
        echo "Erreur serveur.";
    }
}
