<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

// Récupérer l'ID du domaine "FDFP" (tentative) et récupérer les publications.
$domain_id = 0;
$publications = [];
try {
  // Essayer plusieurs slugs possibles
  $slugs = ['fdfp','cabinet-fdfp','cabinet fdfp'];
  $domain = null;
  foreach ($slugs as $s) {
    $stmt = $pdo->prepare("SELECT id FROM domains WHERE slug = ?");
    $stmt->execute([$s]);
    $domain = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($domain) break;
  }
  // Si pas trouvé, essayer une recherche plus large (LIKE) ou par title
  if (!$domain) {
    $stmt = $pdo->prepare("SELECT id FROM domains WHERE slug LIKE ? OR title LIKE ? LIMIT 1");
    $stmt->execute(['%fdfp%','%FDFP%']);
    $domain = $stmt->fetch(PDO::FETCH_ASSOC);
  }
  $domain_id = $domain['id'] ?? 0;
} catch (PDOException $e) {
  // table domains peut ne pas exister — on ignore, on utilisera un fallback
  $domain_id = 0;
}

// Première tentative : publications référencées par domain_id (nouveau schéma)
if ($domain_id) {
  try {
    $stmt = $pdo->prepare(
      "SELECT p.*, m.filename AS media_file 
       FROM publications p
       LEFT JOIN media m ON p.media_id = m.id
       WHERE p.domain_id = ? AND p.status = 'published'
       ORDER BY p.published_at DESC"
    );
    $stmt->execute([$domain_id]);
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    // si la structure est différente, on passera au fallback
    $publications = [];
  }
}

// Fallback : anciene structure où publications.domaine est une colonne ENUM ('fdfp')
if (empty($publications)) {
  try {
    $stmt = $pdo->prepare(
      "SELECT p.*, m.filename AS media_file 
       FROM publications p
       LEFT JOIN media m ON p.media_id = m.id
       WHERE p.domaine = ? 
       ORDER BY p.created_at DESC"
    );
    $stmt->execute(['fdfp']);
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    // colonne 'domaine' peut ne pas exister — en dernier recours, essayer par title dans domains
    try {
      $stmt = $pdo->prepare(
        "SELECT p.*, m.filename AS media_file 
         FROM publications p
         LEFT JOIN media m ON p.media_id = m.id
         JOIN domains d ON p.domain_id = d.id
         WHERE d.slug = ? AND p.status = 'published'
         ORDER BY p.published_at DESC"
      );
      $stmt->execute(['fdfp']);
      $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      $publications = [];
    }
  }
}
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
        <?php foreach ($publications as $pub): 
          $title = $pub['titre'] ?? $pub['title'] ?? '';
          if (!empty($pub['contenu'])) {
            $content = $pub['contenu'];
          } elseif (!empty($pub['content'])) {
            $content = $pub['content'];
          } elseif (!empty($pub['excerpt'])) {
            $content = $pub['excerpt'];
          } else {
            $content = '';
          }
          $media = $pub['media'] ?? $pub['media_file'] ?? $pub['filename'] ?? '';
        ?>
          <div class="publication">
            <h4><?= e($title) ?></h4>
            <?php if (!empty($content)): ?>
              <p><?= e($content) ?></p>
            <?php endif; ?>
            <?php if (!empty($media)): 
              $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
              if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
                <img src="uploads/<?= e($media) ?>" alt="">
              <?php elseif ($ext === 'mp4'): ?>
                <video src="uploads/<?= e($media) ?>" controls></video>
              <?php elseif ($ext === 'pdf'): ?>
                <a href="uploads/<?= e($media) ?>" target="_blank">Voir PDF</a>
              <?php endif; 
            endif; ?>
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