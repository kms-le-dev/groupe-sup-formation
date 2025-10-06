<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$email || !$phone || !$password || !$password_confirm) {
        $message = 'Veuillez remplir tous les champs.';
    } elseif ($password !== $password_confirm) {
        $message = 'Les mots de passe ne correspondent pas.';
    } else {
        // Vérifier que l'email existe et que le numéro correspond
        $stmt = $pdo->prepare('SELECT id, phone FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $message = 'Email incorrect.';
        } elseif (trim($user['phone'] ?? '') !== $phone) {
            $message = 'Numéro invalide pour cet email.';
        } else {
            // Mettre à jour le mot de passe
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $upd->execute([$hash, $user['id']]);
            $_SESSION['success'] = 'Mot de passe mis à jour. Vous pouvez vous connecter.';
            header('Location: login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .reset-container {
            max-width: 480px;
            margin: 3rem auto;
            padding: 1.5rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }
        .reset-container h2 { text-align:center; color:#0f172a; }
        .reset-container label { display:block; margin-bottom:0.4rem; font-weight:600; }
        .reset-container input { width:100%; padding:0.7rem; margin-bottom:0.9rem; border-radius:8px; border:1px solid #e2e8f0 }
    .reset-container button { width:100%; padding:0.8rem; border-radius:8px; background:#10b981; color:#fff; border:none; cursor:pointer }
        .alert { color:#b91c1c; font-weight:700; margin-bottom:0.8rem }
    </style>
</head>
<body>
<div class="reset-container">
    <h2>Mot de passe oublié</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>

        <label for="phone">Numéro de téléphone</label>
        <input type="text" name="phone" id="phone" placeholder="Ex: +221770000000" required>

        <label for="password">Nouveau mot de passe</label>
        <input type="password" name="password" id="password" required>

        <label for="password_confirm">Confirmer le mot de passe</label>
        <input type="password" name="password_confirm" id="password_confirm" required>

        <button type="submit">Mettre à jour le mot de passe</button>
    </form>

    <p style="text-align:center; margin-top:0.8rem;"><a href="login.php">Retour à la connexion</a></p>
</div>
</body>
</html>
