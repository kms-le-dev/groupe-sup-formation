<?php

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription - Sup'Formation</title>
<link rel="stylesheet" href="assets/css/styles.css">

<style>
/* --- Formulaire moderne --- */
body {
    font-family: Arial, sans-serif;
    background: #f0f4f8;
    margin: 0;
    padding: 0;
}

.registration-form {
    max-width: 500px;
    margin: 3rem auto;
    padding: 2rem;
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    position: relative;
}

.registration-form h2 {
    text-align: center;
    margin-bottom: 2rem;
    color: #ec1414ff;
}

.form-group {
    margin-bottom: 1.2rem;
    position: relative;
}

.form-group label {
    display: block;
    margin-bottom: 0.3rem;
    font-weight: 600;
    color: #14af42ff;
}

.form-group input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s;
}

.form-group input:focus {
    outline: none;
    border-color: #fc0f0fff;
    box-shadow: 0 0 8px rgba(30,64,175,0.3);
}

.error-message, .success-message {
    text-align: center;
    padding: 0.75rem;
    margin-bottom: 1rem;
    border-radius: 8px;
    opacity: 0;
    transform: translateY(-10px);
    transition: all 0.5s;
}

.error-message.show {
    background: #fee2e2;
    color: #b91c1c;
    opacity: 1;
    transform: translateY(0);
}

.success-message.show {
    background: #d1fae5;
    color: #065f46;
    opacity: 1;
    transform: translateY(0);
}

button.btn {
    width: 100%;
    padding: 0.9rem;
    font-size: 1rem;
    text-transform: uppercase;
    background: #14af42ff;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

button.btn:hover {
    background: #095e33ff;
}

.password-toggle {
    position: absolute;
    top: 50%;
    right: 1rem;
    transform: translateY(-50%);
    cursor: pointer;
    color: #14af42ff;
    font-size: 0.9rem;
    user-select: none;
}

#password-strength {
    height: 6px;
    border-radius: 4px;
    margin-top: 0.3rem;
    transition: width 0.3s;
}

/* Note sous le formulaire pour inviter à se connecter */
.bottom-note {
    text-align: center;
    margin-top: 1rem;
    color: #6b7280;
    font-size: 0.95rem;
}
.bottom-note a {
    color: #14af42ff;
    font-weight: 600;
    text-decoration: none;
}
.bottom-note a:hover {
    text-decoration: underline;
}

/* Bouton Accueil */
.home-btn {
    display: inline-block;
    margin-top: 0.6rem;
    padding: 0.5rem 1rem;
    background: #2563eb;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
}
.home-btn:hover { background: #1e40af; }

@media (max-width: 600px) {
    .registration-form {
        margin: 2rem 1rem;
        padding: 1.5rem;
    }
}
</style>
</head>
<body>

<div class="registration-form">
    <h2>Créer un compte</h2>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="error-message show"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="success-message show"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form action="register_submit.php" method="POST" id="registerForm">
        <div class="form-group">
            <label for="first_name">Prénom</label>
            <input type="text" id="first_name" name="first_name" required>
        </div>

        <div class="form-group">
            <label for="last_name">Nom</label>
            <input type="text" id="last_name" name="last_name" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group" style="position: relative;">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
            <span class="password-toggle" id="togglePassword">Voir</span>
            <div id="password-strength" style="width:0%; background:#dc2626;"></div>
        </div>

        <div class="form-group" style="position: relative;">
            <label for="confirm_password">Confirmer le mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <span class="password-toggle" id="toggleConfirm">Voir</span>
        </div>

        <button type="submit" class="btn">S'inscrire</button>
    </form>

        <div class="bottom-note">
            Vous avez un compte ? <a href="../public/login.php">Connectez-vous</a>
        </div>

        <div style="text-align:center;">
              <a class="home-btn" href="../public/index.php">Accueil</a>
        </div>
</div>

<script>
// --- Voir / cacher mot de passe ---
const togglePassword = document.getElementById('togglePassword');
const toggleConfirm = document.getElementById('toggleConfirm');
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('confirm_password');

togglePassword.addEventListener('click', () => {
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
    togglePassword.textContent = type === 'password' ? 'Voir' : 'Cacher';
});

toggleConfirm.addEventListener('click', () => {
    const type = confirmInput.type === 'password' ? 'text' : 'password';
    confirmInput.type = type;
    toggleConfirm.textContent = type === 'password' ? 'Voir' : 'Cacher';
});

// --- Validation instantanée ---
const form = document.getElementById('registerForm');
const strengthBar = document.getElementById('password-strength');

passwordInput.addEventListener('input', () => {
    let strength = 0;
    const val = passwordInput.value;
    if (val.length >= 6) strength += 25;
    if (/[A-Z]/.test(val)) strength += 25;
    if (/[0-9]/.test(val)) strength += 25;
    if (/[^A-Za-z0-9]/.test(val)) strength += 25;
    strengthBar.style.width = strength + '%';
    if (strength < 50) strengthBar.style.background = '#dc2626';
    else if (strength < 75) strengthBar.style.background = '#f59e0b';
    else strengthBar.style.background = '#16a34a';
});

form.addEventListener('submit', function(e){
    if(passwordInput.value !== confirmInput.value){
        e.preventDefault();
        alert("Les mots de passe ne correspondent pas.");
        confirmInput.focus();
    }
});
</script>

</body>
</html>
