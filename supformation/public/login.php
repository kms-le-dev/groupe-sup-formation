<?php
session_start();
require_once __DIR__ . '/../includes/config.php'; // connexion DB

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    if (empty($email) || empty($password)) {
        $message = "Veuillez remplir tous les champs.";
    } else {
        try {
            // Select all and map fields in PHP to avoid SQL errors when columns differ
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $pwd = $row['password'] ?? $row['password_hash'] ?? null;
                if ($pwd && password_verify($password, $pwd)) {
                    $user = [
                        'id' => $row['id'],
                        'role_id' => $row['role_id'] ?? null,
                        'first_name' => $row['prenom'] ?? $row['first_name'] ?? '',
                        'last_name' => $row['nom'] ?? $row['last_name'] ?? '',
                        'email' => $row['email'] ?? '',
                    ];
                    // Connexion réussie
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["role_id"] = $user["role_id"] ?? null;
                    $_SESSION["first_name"] = $user["first_name"] ?? '';
                    $_SESSION["last_name"] = $user["last_name"] ?? '';

                    if (!empty($_SESSION["role_id"]) && (int)$_SESSION["role_id"] === 2) {
                        header("Location: ../admin/dashboard.php"); // admin (role_id === 2)
                    } else {
                        header("Location: index.php"); // utilisateur
                    }
                    exit;
                }
            }
            // Si on arrive ici, connexion incorrecte
            $message = "Email ou mot de passe incorrect.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        /* Formulaire simple et responsive */
        .login-container {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            border-radius: 12px;
            background: #f9f9f9;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #22c55e;
        }
        .login-container label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .login-container input {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .login-container input:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 5px rgba(34, 197, 94, 0.5);
        }
        .login-container button {
            width: 100%;
            padding: 0.8rem;
            background-color: #22c55e;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
        }
        .login-container .alert {
            color: red;
            margin-bottom: 1rem;
            font-weight: bold;
        }
        .show-password {
            margin-bottom: 1rem;
        }
        .login-container a {
            color: #22c55e;
            text-decoration: none;
            font-weight: 600;
        }
        .login-container a:hover {
            text-decoration: underline;
        }
        .flash-success {
            background: #d1fae5;
            color: #064e3b;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 700;
            animation: flashIn 0.5s ease-out;
        }

        @keyframes flashIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Connexion</h2>

    <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'registered'): ?>
        <div class="flash-success">Inscription réussie. Vous pouvez maintenant vous connecter.</div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="email">Email *</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Mot de passe *</label>
        <input type="password" name="password" id="password" required>

        <div class="show-password">
            <input type="checkbox" id="show_pass"> Voir le mot de passe
        </div>

        <button type="submit">Se connecter</button>
    </form>

    <p style="text-align:center; margin-top:1rem;">
        Pas encore inscrit ? <a href="register.php">Créer un compte</a>
    </p>
    <div style="text-align:center; margin-top:0.5rem;">
        <a href="../public/index.php" style="display:inline-block; padding:0.45rem 0.9rem; background:#2563eb; color:#fff; border-radius:8px; text-decoration:none; font-weight:600;">Accueil</a>
    </div>
</div>

<script>
    // Voir / cacher mot de passe
    const showPassCheckbox = document.getElementById('show_pass');
    const passwordInput = document.getElementById('password');

    showPassCheckbox.addEventListener('change', function() {
        passwordInput.type = this.checked ? 'text' : 'password';
    });
</script>

</body>
</html>
