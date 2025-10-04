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

// Mail (PHPMailer SMTP)
define('SMTP_HOST','smtp.example.com');
define('SMTP_PORT',587);
define('SMTP_USER','no-reply@ton-domaine.com');
define('SMTP_PASS','smtp_password');
define('MAIL_FROM','no-reply@ton-domaine.com');
define('MAIL_FROM_NAME','SupFormation');

// Stripe (exemple)
define('STRIPE_SECRET_KEY','sk_test_xxx');
define('STRIPE_PUBLISHABLE_KEY','pk_test_xxx');

// PayDunya etc. ajoute clés si tu en uses

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
