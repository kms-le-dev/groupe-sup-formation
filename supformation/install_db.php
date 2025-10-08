<?php
/**
 * Script d'installation de la base de données Sup'Formation
 * Tech Stack : PHP + MySQL
 */

$host = "localhost";   // ton serveur MySQL
$user = "root";        // ton utilisateur MySQL
$pass = "";            // ton mot de passe MySQL
$dbname = "supformation_db";

try {
    // Connexion MySQL
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connexion MySQL réussie ✅<br>";

    // Créer la base si elle n'existe pas
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    echo "Base de données '$dbname' créée ou déjà existante.<br>";

    // Connexion à la base nouvellement créée
    $pdo->exec("USE `$dbname`");

    // --- TABLE roles ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role_name VARCHAR(50) UNIQUE NOT NULL
    ) ENGINE=InnoDB;");

    // --- TABLE users ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        prenom VARCHAR(100) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        telephone VARCHAR(50),
        password VARCHAR(255) NOT NULL,
        role_id INT DEFAULT 2, -- par défaut utilisateur
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // --- TABLE publications ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS publications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        contenu TEXT,
        media_path VARCHAR(255),
        domaine ENUM('enseignement', 'placement', 'fdfp') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        user_id INT,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB;");

    // --- TABLE formations ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS formations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        description TEXT,
        conditions TEXT,
        calendrier DATE,
        prix DECIMAL(10,2) DEFAULT 0.00
    ) ENGINE=InnoDB;");

    // --- TABLE paiements ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS paiements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        formation_id INT NOT NULL,
        montant DECIMAL(10,2) NOT NULL,
        reference VARCHAR(100) UNIQUE,
        statut ENUM('pending','success','failed') DEFAULT 'pending',
        date_paiement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (formation_id) REFERENCES formations(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // --- TABLE newsletter ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS newsletter (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(150) UNIQUE NOT NULL,
        subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    // --- TABLE cvs ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS cvs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        fichier_path VARCHAR(255) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;");

    // --- TABLE inscriptions (nouveau pour les fiches uploadées) ---
    $pdo->exec("CREATE TABLE IF NOT EXISTS inscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cycle VARCHAR(50) NOT NULL,
        affecte ENUM('oui','non') NOT NULL,
        mode_paiement VARCHAR(50),
        num_paiement VARCHAR(100),
        pdf_filename VARCHAR(255) NOT NULL,
        montant INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    echo "Toutes les tables ont été créées ✅<br>";

    // --- INSERT ROLES ---
    $pdo->exec("INSERT IGNORE INTO roles (id, role_name) VALUES (1,'admin'), (2,'utilisateur')");

    echo "Rôles ajoutés ✅<br>";

    // --- CREER UN ADMIN PAR DEFAUT ---
    $adminEmail = "admin@supformation.com";
    $check = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $check->execute([$adminEmail]);

    if ($check->rowCount() === 0) {
        $hash = password_hash("admin123", PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, telephone, password, role_id) 
                               VALUES (?,?,?,?,?,1)");
        $stmt->execute(["Super", "Admin", $adminEmail, "0100000000", $hash]);
        echo "Compte admin créé (login : $adminEmail / mdp : admin123) ✅<br>";
    } else {
        echo "Admin déjà existant.<br>";
    }

    echo "<br>Installation terminée 🎉";

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
