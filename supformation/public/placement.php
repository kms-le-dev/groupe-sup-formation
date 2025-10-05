<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

// Récupérer l'ID du domaine "Placement de Personnel"
$stmt = $pdo->prepare("SELECT id FROM domains WHERE slug = ?");
$stmt->execute(['placement-personnel']);
$domain = $stmt->fetch(PDO::FETCH_ASSOC);
$domain_id = $domain['id'] ?? 0;

// Récupérer les publications associées à ce domaine
$stmt = $pdo->prepare("
    SELECT p.*, m.filename AS media_file 
    FROM publications p
    LEFT JOIN media m ON p.media_id = m.id
    WHERE p.domain_id = ? AND p.status = 'published'
    ORDER BY p.published_at DESC
");
$stmt->execute([$domain_id]);
$publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Groupe Sup'Formation</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
 
<h1>Placement de Personnel</h1>

<!-- Carousel -->
<!-- HTML amélioré avec indicateurs et coins -->
<div class="carousel" id="mainCarousel">
  <img src="assets/carousels/slide1.jpg" loading="lazy" alt="slide1">
  <img src="assets/carousels/slide2.jpg" loading="lazy" alt="slide2">
  <img src="assets/carousels/slide3.jpg" loading="lazy" alt="slide3">
  
  <!-- Coins animés -->
  <div class="carousel-corner top-left"></div>
  <div class="carousel-corner top-right"></div>
  <div class="carousel-corner bottom-left"></div>
  <div class="carousel-corner bottom-right"></div>
  
  <!-- Indicateurs de progression -->
  <div class="carousel-indicators">
    <div class="carousel-indicator"></div>
    <div class="carousel-indicator"></div>
    <div class="carousel-indicator"></div>
  </div>
</div>

<!-- Bloc infos -->
<!-- Section Placement et Insertion -->
<section class="placement-section">
  <div class="placement-container">
    
    <div class="placement-illustration">
      <img src="assets/pla.jpg" alt="Placement de personnel" loading="lazy">
      <div class="pulse-glow"></div>
    </div>

    <div class="placement-text">
      <h2>🤝 Placement et Insertion de Personnel</h2>
      <p>
        Nous accompagnons les diplômés et les professionnels dans leur
        <strong>insertion sur le marché de l’emploi</strong> grâce à un réseau
        d’entreprises partenaires et une approche personnalisée.
      </p>
      <ul>
        <li>🧭 <strong>Orientation professionnelle</strong> – Identification des opportunités adaptées à votre profil</li>
        <li>💼 <strong>Placement en entreprise</strong> – Accès direct à des postes qualifiés</li>
        <li>📈 <strong>Suivi de carrière</strong> – Coaching et accompagnement après le placement</li>
      </ul>
      <a href="#" class="btn-placement">Je veux être accompagné</a>
    </div>
  </div>
</section>


<!-- Zone publications -->
<div class="publications">
    <h3>Actualités & Publications</h3>
    <?php if ($publications): ?>
        <?php foreach ($publications as $pub): ?>
            <div class="publication">
                <h4><?= e($pub['titre']) ?></h4>
                <p><?= e($pub['contenu']) ?></p>
                <?php if ($pub['media']): ?>
                    <?php $ext = pathinfo($pub['media'], PATHINFO_EXTENSION); ?>
                    <?php if (in_array($ext, ['jpg','png','gif'])): ?>
                        <img src="uploads/<?= e($pub['media']) ?>" alt="">
                    <?php elseif ($ext === 'mp4'): ?>
                        <video src="uploads/<?= e($pub['media']) ?>" controls></video>
                    <?php elseif ($ext === 'pdf'): ?>
                        <a href="uploads/<?= e($pub['media']) ?>" target="_blank">Voir PDF</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune publication pour le moment.</p>
    <?php endif; ?>
</div>

<div class="inscription">
    <a href="paydunya_checkout.php?domaine=placement" class="btn">S'inscrire & Payer</a>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
