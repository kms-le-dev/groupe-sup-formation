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
 
<h1>Enseignement Supérieur</h1>

<!-- Carousel -->
<!-- HTML amélioré avec indicateurs et coins -->
<div class="carousel" id="mainCarousel">
  <img src="assets/ens0.jpg" loading="lazy" alt="slide1">
  <img src="assets/ens1.jpg" loading="lazy" alt="slide2">
  <img src="assets/ens2.jpg" loading="lazy" alt="slide3">
  
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
<!-- Section Enseignement Supérieur -->
<section class="enseignement-section">
  <div class="enseignement-container">
    <div class="enseignement-text">
      <h2>🎓 Enseignement Supérieur</h2>
      <p>
        Découvrez nos formations de qualité conçues pour vous accompagner vers la réussite.
        <br><br>
        Nous proposons des parcours complets :
      </p>
      <ul>
        <li><strong>BTS / DUT</strong> – Formations pratiques et professionnelles</li>
        <li><strong>Licence</strong> – Développez vos compétences académiques et techniques</li>
        <li><strong>Master</strong> – Devenez un expert dans votre domaine</li>
        <li><strong>VAE</strong> – Valorisez votre expérience par un diplôme reconnu</li>
      </ul>
      <a href="#" class="btn-inscription">S’inscrire maintenant</a>
    </div>

    <div class="enseignement-illustration">
      <img src="assets/ens.jpg" alt="Étudiants en formation" loading="lazy">
      <div class="light-glow"></div>
    </div>
  </div>
</section>



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