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
        // Prépare les données à envoyer à EmailJS
        $data = [
            'service_id' => 'default_service', // ou ton service ID exact si différent
            'template_id' => 'template_o4eyaqn', // ton template ID
            'user_id' => 'tKqFOfM8cHTyTPeG7', // ta clé publique EmailJS
            'template_params' => [
                'title' => "Contact Us",        // sujet ou titre
                'name' => $nom,                 // nom envoyé au template
                'email' => $email,              // email envoyé au template
                // Si tu veux passer le téléphone et le message, il faut aussi modifier le template pour accepter ces variables !
                'telephone' => $telephone,      
                'message' => $msg
            ]
        ];

        // Envoi de la requête HTTP POST vers EmailJS API
        $ch = curl_init('https://api.emailjs.com/api/v1.0/email/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $message = "Erreur lors de l'envoi : $error";
        } else {
            $message = "Message envoyé avec succès !";
        }
    }
}
?>







<!doctype html>
<html lang="fr">
<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Groupe Sup'Formation</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>

<style>
/* Style centré et mis en valeur pour la localisation */
.map-location {
  text-align: center;
  max-width: 900px;
  margin: 18px auto 0 auto;
  font-size: 1.15rem;
  font-weight: 700;
  color: #b30000;                    /* rouge GSF */
  background: linear-gradient(90deg, #fff6f6, #fff1f1);
  padding: 12px 16px;
  border-radius: 12px;
  box-shadow: 0 8px 30px rgba(179,0,0,0.06);
  border: 1px solid rgba(179,0,0,0.08);
  line-height: 1.4;
  letter-spacing: 0.3px;
}

/* Petite icône locale à gauche (optionnel) */
.map-location::before {
  content: '📍';
  display: inline-block;
  margin-right: 8px;
  transform: translateY(1px);
}

/* Responsive : réduire la taille sur petits écrans */
@media (max-width: 480px) {
  .map-location {
    font-size: 1rem;
    padding: 10px 12px;
    border-radius: 10px;
  }
}
</style>
<body>

<!-- Bandeau contacts en haut -->
<div class="top-contacts" role="region" aria-label="Nos contacts principaux">
  <h1 class="contact-title">Nous Contacter</h1>
  <div class="top-contacts-inner">
    <a href="tel:+2252735999501" class="top-phone">📞 27 35 99 95 01</a>
    <a href="tel:+2250574939737" class="top-phone">📞 05 74 93 97 37</a>
    <a href="tel:+2250706591243" class="top-phone">📞 07 06 59 12 43</a>
  </div>
</div>


<h1 class="contact-title">Soumettre une demande</h1>

<?php if ($message): ?>
  <div class="alert" role="status"><?= e($message) ?></div>
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
    <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3972.137147803987!2d-4.005823425016036!3d5.396064294582986!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zNcKwMjMnNDUuOCJOIDTCsDAwJzExLjciVw!5e0!3m2!1sfr!2sci!4v1759691213289!5m2!1sfr!2sci" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      <h2 class="map-location">Nous sommes situé au Plateau Dokui, non loin de l'allocodrome</h2>
</div>

<!-- Section Réseaux Sociaux -->
<div class="social-container">
  <h3>Suivez-nous</h3>
  <div class="social-icons">
    <a href="https://vm.tiktok.com/ZMH7dwMGmWB51-jTY0D/" target="_blank" class="social-icon tiktok">
      <i class="fab fa-tiktok"></i>
    </a>
    <a href="mailto:groupesupformation@gmail.com" target="_blank" class="social-icon email">
      <i class="fas fa-envelope"></i>
    </a>
    <a href="https://www.facebook.com/groupesupformation" target="_blank" class="social-icon facebook">
      <i class="fab fa-facebook-f"></i>
    </a>
    <a href="https://chat.whatsapp.com/DF8ox4lLxwIJlTtg1QHXcZ?mode=wwc" target="_blank" class="social-icon whatsapp">
      <i class="fab fa-whatsapp"></i>
    </a>
  </div>
</div>

<!-- FontAwesome pour les icônes -->
<script src="https://kit.fontawesome.com/a2b3c4d5e6.js" crossorigin="anonymous"></script>

<style>
    /* css pour les social-media  */

    /* Conteneur principal */
.social-container {
  text-align: center;
  background: linear-gradient(120deg, #f0f9ff, #e0f2fe);
  padding: 60px 20px;
  border-radius: 20px;
  max-width: 600px;
  margin: 60px auto;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  animation: fadeInUp 1.2s ease-out forwards;
}

/* Titre */
.social-container h3 {
  font-size: 1.8rem;
  color: #0f172a;
  margin-bottom: 25px;
  position: relative;
  letter-spacing: 1px;
}
.social-container h3::after {
  content: "";
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 3px;
  background: #0284c7;
  border-radius: 2px;
}

/* Icônes */
.social-icons {
  display: flex;
  justify-content: center;
  gap: 35px;
}

.social-icon {
  font-size: 1.8rem;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  color: white;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
  position: relative;
  overflow: hidden;
}

/* Couleurs spécifiques */
.social-icon.tiktok { background: #010101; }
.social-icon.email { background: #f97316; }
.social-icon.facebook { background: #1877f2; }
.social-icon.whatsapp { background: #25D366; }

/* Animation de survol */
.social-icon:hover {
  transform: scale(1.15);
  box-shadow: 0 0 20px rgba(0,0,0,0.25);
}

.social-icon::before {
  content: "";
  position: absolute;
  width: 120%;
  height: 120%;
  top: 100%;
  left: -10%;
  background: rgba(255,255,255,0.2);
  transform: rotate(45deg);
  transition: 0.6s ease;
}

.social-icon:hover::before {
  top: -20%;
}

/* Animation d’entrée */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 600px) {
  .social-container {
    padding: 40px 10px;
  }
  .social-icons {
    gap: 25px;
  }
  .social-icon {
    width: 50px;
    height: 50px;
    font-size: 1.5rem;
  }
}



    /* Variables CSS pour une personnalisation facile */
:root {
  --primary: #0a8d36ff;
  --primary-hover: #2edb1eff;
  --secondary: #91e20eff;
  --success: #10b981;
  --error: #ef4444;
  --dark: #1e293b;
  --gray: #64748b;
  --light: #f1f5f9;
  --white: #ffffff;
  --border: #cbd5e1;
  --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
  --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.12);
  --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.15);
  --radius: 12px;
  --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Formulaire - Container principal */
form {
  max-width: 650px;
  margin: 0 auto 3rem;
  background: var(--white);
  padding: 3rem;
  border-radius: var(--radius);
  box-shadow: var(--shadow-lg);
  position: relative;
  overflow: hidden;
  animation: slideUp 0.6s ease;
}

/* Barre décorative en haut du formulaire */
form::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(90deg, var(--primary), var(--secondary), var(--primary));
  background-size: 200% 100%;
  animation: gradientMove 4s linear infinite;
}

/* Label des champs */
form label {
  display: block;
  margin-bottom: 0.5rem;
  color: var(--dark);
  font-weight: 600;
  font-size: 0.95rem;
  letter-spacing: 0.3px;
  transition: var(--transition);
  cursor: pointer;
}

/* Animation du label au focus */
form input:focus + label,
form textarea:focus + label {
  color: var(--primary);
}

/* Champs de saisie (input et textarea) */
form input[type="text"],
form input[type="email"],
form textarea {
  width: 100%;
  padding: 1rem 1.25rem;
  margin-bottom: 1.75rem;
  border: 2px solid var(--border);
  border-radius: 10px;
  font-size: 1rem;
  font-family: inherit;
  color: var(--dark);
  background: var(--light);
  transition: var(--transition);
  outline: none;
}

/* Effet au survol des champs */
form input:hover,
form textarea:hover {
  border-color: var(--gray);
  background: var(--white);
  transform: translateY(-1px);
}

/* Effet au focus des champs */
form input:focus,
form textarea:focus {
  border-color: var(--primary);
  background: var(--white);
  box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
  transform: translateY(-2px);
}

/* Animation de remplissage - champ valide */
form input:valid:not(:placeholder-shown),
form textarea:valid:not(:placeholder-shown) {
  border-color: var(--success);
  background: rgba(16, 185, 129, 0.05);
}

/* Animation de remplissage - champ invalide */
form input:invalid:not(:placeholder-shown),
form textarea:invalid:not(:placeholder-shown) {
  border-color: var(--error);
  background: rgba(239, 68, 68, 0.05);
  animation: shake 0.4s ease;
}

/* Zone de texte spécifique */
form textarea {
  min-height: 160px;
  resize: vertical;
  line-height: 1.6;
}

/* Bouton d'envoi */
form button[type="submit"] {
  width: 100%;
  padding: 1.1rem 2rem;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  color: var(--white);
  border: none;
  border-radius: 10px;
  font-size: 1.1rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: var(--transition);
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
}

/* Effet de brillance sur le bouton */
form button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.5s ease;
}

form button:hover::before {
  left: 100%;
}

/* Effet ripple au clic */
form button::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.4);
  transform: translate(-50%, -50%);
  transition: width 0.6s ease, height 0.6s ease;
}

form button:active::after {
  width: 400px;
  height: 400px;
}

/* Survol du bouton */
form button:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5);
}

/* Clic sur le bouton */
form button:active {
  transform: translateY(-1px);
}

/* État désactivé du bouton */
form button:disabled {
  background: var(--gray);
  cursor: not-allowed;
  opacity: 0.6;
  transform: none;
}

/* Section carte Google Maps */
.map {
  max-width: 900px;
  margin: 0 auto;
  background: var(--white);
  padding: 2.5rem;
  border-radius: var(--radius);
  box-shadow: var(--shadow-lg);
  animation: slideUp 0.8s ease 0.2s backwards;
}

/* Titre de la carte */
.map h3 {
  color: var(--dark);
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  text-align: center;
  position: relative;
  padding-bottom: 1rem;
}

/* Ligne décorative sous le titre */
.map h3::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 4px;
  background: linear-gradient(90deg, var(--primary), var(--secondary));
  border-radius: 2px;
  animation: expand 1s ease;
}

/* iframe Google Maps */
.map iframe {
  width: 100%;
  height: 350px;
  border-radius: 10px;
  box-shadow: var(--shadow-md);
  transition: var(--transition);
  border: 3px solid var(--light);
}

/* Effet au survol de la carte */
.map iframe:hover {
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
  transform: scale(1.02);
  border-color: var(--primary);
}

/* === ANIMATIONS === */

/* Animation d'entrée - montée */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(40px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Animation gradient */
@keyframes gradientMove {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

/* Animation de secousse (erreur) */
@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-10px); }
  75% { transform: translateX(10px); }
}

/* Animation d'expansion */
@keyframes expand {
  from { width: 0; }
  to { width: 80px; }
}

/* Animation de pulsation */
@keyframes pulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.05); }
}

/* === RESPONSIVE DESIGN === */

@media (max-width: 768px) {
  form {
    padding: 2rem 1.5rem;
    margin-bottom: 2rem;
  }
  
  .map {
    padding: 1.5rem;
  }
  
  .map iframe {
    height: 300px;
  }
  
  form button[type="submit"] {
    font-size: 1rem;
    padding: 1rem;
  }
}

@media (max-width: 480px) {
  form {
    padding: 1.5rem 1rem;
  }
  
  form label {
    font-size: 0.9rem;
  }
  
  form input,
  form textarea {
    padding: 0.875rem 1rem;
    font-size: 0.95rem;
  }
  
  .map h3 {
    font-size: 1.4rem;
  }
  
  .map iframe {
    height: 250px;
  }
}

/* Effet de focus visible pour l'accessibilité */
form input:focus-visible,
form textarea:focus-visible,
form button:focus-visible {
  outline: 3px solid var(--primary);
  outline-offset: 2px;
}
/* --- Titre Contact dynamique --- */
.contact-title {
  text-align: center;
  font-size: 3rem;
  font-weight: 900;
  margin: 2.5rem auto 2rem auto;
  background: linear-gradient(90deg, #0a8d36ff, #2edb1eff, #1877f2, #0a8d36ff);
  background-size: 300% 100%;
  color: transparent;
  -webkit-background-clip: text;
  background-clip: text;
  filter: drop-shadow(0 6px 18px rgba(16,185,129,0.18));
  letter-spacing: 2px;
  animation: gradientMove 5s linear infinite, fadeInUp 1.1s cubic-bezier(0.4,0,0.2,1);
  transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), filter 0.3s;
  cursor: pointer;
}
.contact-title:hover {
  transform: scale(1.04) translateY(-2px);
  filter: drop-shadow(0 10px 32px rgba(16,185,129,0.25));
}
/* Bandeau contacts en haut */
.top-contacts {
  width: 100%;
  background: linear-gradient(90deg,#fff4f4, #fff9f9);
  border-bottom: 2px solid rgba(224,60,60,0.06);
  padding: 8px 0;
  box-shadow: 0 6px 18px rgba(224,60,60,0.04);
}
.top-contacts-inner{
  max-width:1100px;
  margin:0 auto;
  display:flex;
  gap:16px;
  justify-content:center;
  align-items:center;
  padding:6px 12px;
}
.top-phone{
  display:inline-flex;
  align-items:center;
  gap:8px;
  color:#b30000;
  background:#fff;
  padding:8px 14px;
  border-radius:20px;
  text-decoration:none;
  font-weight:700;
  box-shadow:0 6px 18px rgba(179,0,0,0.06);
  transition:transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
}
.top-phone:active{ transform: scale(0.98); }
.top-phone:hover{ background:#ffeaea; transform: translateY(-4px); box-shadow:0 12px 30px rgba(224,60,60,0.12); }

@media(max-width:640px){
  .top-contacts-inner{ gap:8px; padding:6px; }
  .top-phone{ padding:8px 10px; font-size:0.95rem; }
}
/* Message PHP centré et stylisé */
.alert {
  display: block;
  margin: 1.5rem auto 2.2rem auto;
  max-width: 600px;
  text-align: center;
  font-size: 1.15rem;
  font-weight: 600;
  padding: 1.1rem 1.5rem;
  border-radius: 12px;
  background: linear-gradient(90deg, #f0fdf4 60%, #f1f5f9 100%);
  color: #15803d;
  border: 1.5px solid #bbf7d0;
  box-shadow: 0 2px 12px rgba(16,185,129,0.08);
  letter-spacing: 0.5px;
  animation: fadeInUp 0.7s cubic-bezier(0.4,0,0.2,1);
}
.alert:empty { display: none; }
@media (max-width: 600px) {
  .alert { font-size: 1rem; padding: 0.8rem 0.7rem; }
}
</style>
<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="assets/js/main.js" defer></script>
</body>
</html>