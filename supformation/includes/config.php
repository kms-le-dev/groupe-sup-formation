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
define('BASE_URL','http://localhost/groupe-sup-formation/supformation/public'); // adapte
define('ASSETS','/groupe-sup-formation/supformation/public/assets');


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



// === PAYDUNYA CONFIGURATION ===
// Mode test
define('PAYDUNYA_MODE', 'test');

// Clés de test
define('PAYDUNYA_MASTER_KEY', '3AAiZOuK-pY6y-tubH-hReu-SM9fSoihea1C');
define('PAYDUNYA_PUBLIC_KEY', 'test_public_glOr2qq8UzQm6hm0ZDv1AklrIi5');
define('PAYDUNYA_PRIVATE_KEY', 'test_private_RWeCW1OmiF32drWquMlMAJ0O3LU');
define('PAYDUNYA_TOKEN', 'KQ67pbN9R29ewZkvlXqP');

// Callback et success URLs
define('PAYDUNYA_CALLBACK_URL', BASE_URL . '/paydunya_callback.php');
define('PAYDUNYA_SUCCESS_URL', BASE_URL . '/paydunya_success.php');
define('PAYDUNYA_CANCEL_URL', BASE_URL . '/inscription.php'); 




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
