<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

// Récupérer l'ID du domaine "FDFP"
$stmt = $pdo->prepare("SELECT id FROM domains WHERE slug = ?");
$stmt->execute(['fdfp']); // ou 'cabinet-fdfp' selon ton slug
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

<h1>Cabinet de Formation FDFP</h1>

<div class="carousel" id="mainCarousel">
  <img src="assets/fdfp.jpg" loading="lazy" alt="slide1">
  <img src="assets/fdfp0.jpg" loading="lazy" alt="slide2">
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
<!-- Section Formation FDFP -->
<section class="fdfp-section">
  <div class="fdfp-container">

    <div class="fdfp-illustration">
      <img src="assets/fdfp.jpg" alt="Formation FDFP" loading="lazy">
      <div class="fdfp-glow"></div>
    </div>

    <div class="fdfp-text">
      <h2>🎓 Formation Professionnelle FDFP</h2>
      <p>
        Nous proposons des <strong>programmes de formation professionnelle</strong>
        adaptés aux besoins des <strong>entreprises</strong> et des <strong>institutions publiques</strong>.
        Notre objectif : renforcer les compétences, améliorer la productivité
        et favoriser le développement durable des organisations.
      </p>
      <ul>
        <li>🏢 <strong>Formation en entreprise</strong> – Sessions sur mesure selon vos besoins opérationnels</li>
        <li>👩‍💼 <strong>Renforcement de capacités</strong> – Pour cadres, agents et responsables de service</li>
        <li>📊 <strong>Programmes certifiés FDFP</strong> – Respectant les standards de qualité du fonds</li>
      </ul>
      <a href="#" class="btn-fdfp">Demander une formation</a>
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
    <a href="paydunya_checkout.php?domaine=fdfp" class="btn">S'inscrire & Payer</a>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="assets/js/main.js" defer></script>
</body>
</html>