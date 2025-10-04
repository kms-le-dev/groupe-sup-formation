<?php
// public/register.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Générer le CSRF
    $csrf = generate_csrf_token();
    include __DIR__ . '/../templates/register_form.php';
    exit;
}

// POST handling
if (!verify_csrf_token($_POST['csrf'] ?? '')) {
    $_SESSION['error'] = "Jeton CSRF invalide";
    header("Location: register.php");
    exit;
}

// Récupération et validation
$first = trim($_POST['first_name'] ?? '');
$last = trim($_POST['last_name'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$phone = trim($_POST['phone'] ?? '');
$pass = $_POST['password'] ?? '';
$pass2 = $_POST['password_confirm'] ?? '';

if (!$first || !$last || !$email || !$pass || !$pass2) {
    $_SESSION['error'] = "Veuillez remplir tous les champs.";
    header("Location: register.php");
    exit;
}

if ($pass !== $pass2) {
    $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
    header("Location: register.php");
    exit;
}

if (strlen($pass) < 8) {
    $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères.";
    header("Location: register.php");
    exit;
}

// Vérification du domaine email
$domain = explode('@',$email)[1] ?? '';
if ($domain && !checkdnsrr($domain,'MX')) {
    $_SESSION['error'] = "Domaine email invalide.";
    header("Location: register.php");
    exit;
}

// Insertion
$passwordHash = password_hash($pass, PASSWORD_DEFAULT);
$token = bin2hex(random_bytes(32));

$stmt = $pdo->prepare("
    INSERT INTO users 
    (role_id, first_name, last_name, email, email_verified, email_verification_token, phone, password_hash) 
    VALUES (1, :fn, :ln, :email, 0, :token, :phone, :hash)
");

try {
    $stmt->execute([
        ':fn' => $first,
        ':ln' => $last,
        ':email' => $email,
        ':token' => $token,
        ':phone' => $phone,
        ':hash' => $passwordHash
    ]);

    $verifyUrl = BASE_URL . "/verify_email.php?token=$token";
    $html = "<p>Bonjour $first,</p><p>Merci de vous inscrire. Cliquez ici pour vérifier votre email : <a href=\"$verifyUrl\">Vérifier l'email</a></p>";
    send_mail($email, "Vérification email Sup'Formation", $html);

    $_SESSION['success'] = "Inscription réussie. Vérifiez votre email pour activer votre compte.";
    header("Location: register.php");
    exit;

} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        $_SESSION['error'] = "Email déjà utilisé.";
    } else {
        $_SESSION['error'] = "Erreur serveur.";
    }
    header("Location: register.php");
    exit;
}
