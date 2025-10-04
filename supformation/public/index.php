<?php
// public/index.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
$csrf = generate_csrf_token();
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
<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="container">
  <section id="hero">
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
    <div class="hero-intro">
      <h1>Groupe Sup'Formation</h1>
      <p>Enseignement supérieur, placement de personnel, cabinet de formation FDFP.</p>
      <div class="cta-row">
        <a href="enseignement.php" class="btn">Enseignement Supérieur</a>
        <a href="placement.php" class="btn">Placement de Personnel</a>
        <a href="cabinet.php" class="btn">Cabinet FDFP</a>
      </div>
    </div>
  </section>

  <section id="publications">
    <h2>Dernières publications</h2>
    <div class="posts-grid">
      <?php
      $stmt = $pdo->prepare("SELECT p.*, m.filename FROM publications p LEFT JOIN media m ON p.media_id = m.id WHERE p.status='published' ORDER BY published_at DESC LIMIT 6");
      $stmt->execute();
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)):
      ?>
        <article class="post">
          <?php if ($row['filename']): ?>
            <img src="uploads/<?=htmlspecialchars($row['filename'])?>" loading="lazy" alt="">
          <?php endif; ?>
          <h3><?=htmlspecialchars($row['title'])?></h3>
          <p><?=nl2br(htmlspecialchars(substr($row['excerpt'] ?? strip_tags($row['content']),0,200)))?></p>
        </article>
      <?php endwhile; ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="assets/js/main.js" defer></script>
</body>
</html>
