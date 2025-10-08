<?php
// includes/config.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DB
define('DB_HOST','127.0.0.1');
define('DB_NAME','supformation_db');
define('DB_USER','root');
define('DB_PASS',''); // mets un mot de passe fort

// Site
define('BASE_URL','http://localhost/supformation/public'); // adapte
define('ASSETS','../public/assets');


// SMTP placeholders - REMPLACE CES VALEURS AVANT MISE EN PRODUCTION
define('SMTP_HOST',''); // ex: 'smtp-relay.sendinblue.com'
define('SMTP_PORT',587);
define('SMTP_USER',''); // ex: 'ton@domaine.com'
define('SMTP_PASS',''); // ex: 'mot_de_passe_smtp'
// Adresse par défaut utilisée comme expéditeur
define('MAIL_FROM','kanigui43@gmail.com');
define('MAIL_FROM_NAME','SupFormation');

// Active le debug SMTP pour PHPMailer (0 = off, 2 = client+server). Mettre à 2 pour développement local.
define('SMTP_DEBUG', 2);



// URL de checkout PayDunya (à remplacer par ton checkout réel)
define('PAYDUNYA_CHECKOUT_URL', 'https://paydunya.com/pay/TON_CHECKOUT_ID');

// WhatsApp Cloud API placeholders (si tu veux envoyer les PDFs directement sur WhatsApp)
// Tu dois créer une application Meta/WhatsApp Cloud et récupérer un token et un phone_number_id
define('WHATSAPP_TOKEN', ''); // ex: 'EAA...'
define('WHATSAPP_PHONE_NUMBER_ID', ''); // ex: '10987654321'
// Numéro destinataire admin en E.164 (ex: +2250505051570)
define('WHATSAPP_ADMIN_NUMBER', '+2250505051570');

// Stripe (exemple)
define('STRIPE_SECRET_KEY','sk_test_xxx');
define('STRIPE_PUBLISHABLE_KEY','pk_test_xxx');


// PDO connexion
try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    http_response_code(500);
    echo "DB Connexion error: " . htmlspecialchars($e->getMessage());
    exit;
}
