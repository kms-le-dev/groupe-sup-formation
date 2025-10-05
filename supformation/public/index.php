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

<div class="GSF">
  <h1>Groupe Sup'Formation</h1>
</div>

<div class="media-section">
  <div class="side-images left-images">
    <img src="assets/0img.jpg" alt="Image gauche 1">
    <img src="assets/1img.jpg" alt="Image gauche 2">
  </div>

  <div class="video-container">
    <video autoplay muted loop playsinline>
      <source src="assets/video.mp4" type="video/mp4">
      Votre navigateur ne supporte pas la vidéo.
    </video>
  </div>

  <div class="side-images right-images">
    <img src="assets/2img.jpg" alt="Image droite 1">
    <img src="assets/3img.jpg" alt="Image droite 2">
  </div>
</div>

    <div class="GSF">
      <h1>Groupe Sup'Formation</h1>
      <p>Enseignement supérieur, placement de personnel, cabinet de formation FDFP.</p>
    </div>
  </section>
  <section class="services-section">
  <h2 class="section-title">Nos Pôles de Formation</h2>
  <p class="section-subtitle">
    Découvrez nos domaines d’excellence et rejoignez le <strong>Groupe Sup’Formation</strong> pour booster votre avenir académique et professionnel.
  </p>

  <div class="services-grid">
    <div class="service-card">
      <div class="icon">🎓</div>
      <h3>Enseignement Supérieur</h3>
      <p>BTS, DUT, Licence, Master et Validation des Acquis de l’Expérience (VAE). Des parcours diplômants reconnus et adaptés au monde professionnel.</p>
    </div>

    <div class="service-card">
      <div class="icon">💼</div>
      <h3>Formation Qualifiante</h3>
      <p>Des formations pratiques et courtes pour acquérir rapidement des compétences recherchées sur le marché du travail.</p>
    </div>

    <div class="service-card">
      <div class="icon">🤝</div>
      <h3>Placement & Insertion</h3>
      <p>Accompagnement professionnel pour faciliter l’accès à l’emploi grâce à un vaste réseau d’entreprises partenaires.</p>
    </div>

    <div class="service-card">
      <div class="icon">🏢</div>
      <h3>Formation Professionnelle FDFP</h3>
      <p>Formation continue pour les entreprises et institutions publiques, soutenue par le <strong>FDFP</strong>.</p>
    </div>
  </div>

  <div class="cta-container">
    <a href="register.php" class="btn-register">S'inscrire dès maintenant</a>
  </div>
</section>


  
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="assets/js/main.js" defer></script>
</body>
</html>
