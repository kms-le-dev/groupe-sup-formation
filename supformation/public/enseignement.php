<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

// Récupérer l'ID du domaine "Enseignement Supérieur"
$stmt = $pdo->prepare("SELECT id FROM domains WHERE title = ?");
$stmt->execute(['Enseignement Supérieur']);
$domain = $stmt->fetch(PDO::FETCH_ASSOC);
$domain_id = $domain['id'] ?? 0;

// Récupérer les publications associées à ce domaine
$stmt = $pdo->prepare("SELECT p.*, m.filename AS media_file 
                       FROM publications p
                       LEFT JOIN media m ON p.media_id = m.id
                       WHERE p.domain_id = ? AND p.status = 'published'
                       ORDER BY p.published_at DESC");
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

<link rel="stylesheet" href="assets/css/style.css">

<h1>Enseignement Supérieur</h1>

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
<div class="info-bloc">
    <h2>Nos Formations BTS / DUT / Licences / Master</h2>
    <p>
        Finance, Gestion, Marketing, Informatique, RH, Logistique, Qualité, Agriculture, Electrotechnique, etc...
    </p>
</div>


<!-- Zone publications -->
<div class="publications">
    <h3>Actualités & Publications</h3>
    <?php if ($publications): ?>
        <?php foreach ($publications as $pub): ?>
            <div class="publication">
                <h4><?= e($pub['title']) ?></h4>
                <?php if(!empty($pub['excerpt'])): ?>
                    <p><?= e($pub['excerpt']) ?></p>
                <?php endif; ?>
                <?php if(!empty($pub['content'])): ?>
                    <p><?= e($pub['content']) ?></p>
                <?php endif; ?>

                <?php if (!empty($pub['media_file'])): ?>
                    <?php $ext = pathinfo($pub['media_file'], PATHINFO_EXTENSION); ?>
                    <?php if (in_array(strtolower($ext), ['jpg','jpeg','png','gif'])): ?>
                        <img src="uploads/<?= e($pub['media_file']) ?>" alt="">
                    <?php elseif (strtolower($ext) === 'mp4'): ?>
                        <video src="uploads/<?= e($pub['media_file']) ?>" controls></video>
                    <?php elseif (strtolower($ext) === 'pdf'): ?>
                        <a href="uploads/<?= e($pub['media_file']) ?>" target="_blank">Voir PDF</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune publication pour le moment.</p>
    <?php endif; ?>
</div>


<!-- Bouton inscription / paiement -->
<div class="inscription">
    <a href="paydunya_checkout.php?domaine=enseignement" class="btn">S'inscrire & Payer</a>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="assets/js/main.js" defer></script>
</body>
</html>