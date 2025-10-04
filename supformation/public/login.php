<?php
session_start();
require_once "../includes/config.php"; // connexion DB

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, nom, prenom, email, password, role_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user["password"])) {
                // Connexion réussie
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["role_id"] = $user["role_id"];
                $_SESSION["nom"] = $user["nom"];

                if ($user["role_id"] == 1) {
                    header("Location: ../admin/dashboard.php"); // admin
                } else {
                    header("Location: index.php"); // utilisateur
                }
                exit;
            } else {
                $message = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $message = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion - Sup'Formation</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<div class="login-container">
    <h2>Connexion</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">Email *</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Mot de passe *</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore inscrit ? <a href="register.php">Créer un compte</a></p>
</div>

</body>
</html>
