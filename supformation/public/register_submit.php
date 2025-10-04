<?php
session_start();

// Detect AJAX requests (keep JSON response) or normal form POST (redirect)
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Récupérer les données POST
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Vérifications côté serveur
$errors = [];

if (!$first_name) $errors[] = "Le prénom est requis.";
if (!$last_name) $errors[] = "Le nom est requis.";
if (!$email) $errors[] = "L'email est requis.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "L'email n'est pas valide.";
if (!$password) $errors[] = "Le mot de passe est requis.";
if ($password !== $confirm_password) $errors[] = "Les mots de passe ne correspondent pas.";

// Vérifier si l'email existe déjà
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) $errors[] = "Cet email est déjà utilisé.";

if (!empty($errors)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'messages' => $errors]);
        exit;
    }
    // For normal form submit, store errors in session and redirect back to register
    $_SESSION['error'] = implode(' ', $errors);
    header('Location: register.php');
    exit;
}

// Hasher le mot de passe
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Insérer l'utilisateur
$stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, is_active, created_at) VALUES (?, ?, ?, ?, 1, NOW())");

try {
    $stmt->execute([$first_name, $last_name, $email, $password_hash]);
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'messages' => ['Inscription réussie !']]);
        exit;
    }
    // Redirect to login with success flag for normal form submit
    header('Location: login.php?msg=registered');
    exit;
} catch (PDOException $e) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'messages' => ["Erreur serveur : " . $e->getMessage()]]);
        exit;
    }
    $_SESSION['error'] = "Erreur serveur : " . $e->getMessage();
    header('Location: register.php');
    exit;
}
