<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

// Récupérer les publications pour le domaine Enseignement (fallback pour anciens schémas)
$publications = [];
$domain_id = 0;
try {
  $stmt = $pdo->prepare("SELECT id FROM domains WHERE slug = ?");
  $stmt->execute(['enseignement']);
  $domain = $stmt->fetch(PDO::FETCH_ASSOC);
  $domain_id = $domain['id'] ?? 0;
} catch (PDOException $e) {
  $domain_id = 0;
}

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
    $publications = [];
  }
}

if (empty($publications)) {
  try {
    $stmt = $pdo->prepare(
      "SELECT p.*, m.filename AS media_file 
       FROM publications p
       LEFT JOIN media m ON p.media_id = m.id
       WHERE p.domaine = ?
       ORDER BY p.created_at DESC"
    );
    $stmt->execute(['enseignement']);
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    try {
      $stmt = $pdo->prepare(
        "SELECT p.*, m.filename AS media_file 
         FROM publications p
         LEFT JOIN media m ON p.media_id = m.id
         JOIN domains d ON p.domain_id = d.id
         WHERE d.title = ? AND p.status = 'published'
         ORDER BY p.published_at DESC"
      );
      $stmt->execute(['Enseignement Supérieur']);
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

<h1>Enseignement Supérieur</h1>

<!-- Carousel -->
<!-- HTML amélioré avec indicateurs et coins -->
<div class="carousel" id="mainCarousel">
  <img src="assets/ens0.jpg" loading="lazy" alt="slide1" class="scroll-fade">
  <img src="assets/ens1.jpg" loading="lazy" alt="slide2" class="scroll-fade">
  <img src="assets/ens2.jpg" loading="lazy" alt="slide3" class="scroll-fade">
  
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
  <div class="enseignement-text scroll-fade">
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
      <a href="./formulaire.php" class="btn-inscription">S’inscrire maintenant</a>
    </div>

  <div class="enseignement-illustration scroll-fade">
      <img src="assets/ens.jpg" alt="Étudiants en formation" loading="lazy">
      <div class="light-glow"></div>
    </div>
  </div>
</section>



<!-- Zone publications -->
<div class="publications">
  <h3 class="publications-title">Actualités & Publications</h3>
    <?php if ($publications): ?>
    <?php foreach ($publications as $pub): ?>
      <div class="publication scroll-fade">
                <h4><?= e($pub['title']) ?></h4>
        <?php if(!empty($pub['content'])): ?>
          <p><?= e($pub['content']) ?></p>
        <?php elseif(!empty($pub['excerpt'])): ?>
          <p><?= e($pub['excerpt']) ?></p>
        <?php endif; ?>

                <?php if (!empty($pub['media_file'])): ?>
                    <?php $ext = pathinfo($pub['media_file'], PATHINFO_EXTENSION); ?>
          <?php if (in_array(strtolower($ext), ['jpg','jpeg','png','gif'])): ?>
            <img src="uploads/<?= e($pub['media_file']) ?>" alt="">
            <div class="download-link"><a href="uploads/<?= e($pub['media_file']) ?>" download="<?= e($pub['media_file']) ?>">Télécharger</a></div>
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


<style>
/* --- Titre Actualités & Publications dynamique --- */
.publications-title {
  text-align: center;
  font-size: 2.3rem;
  font-weight: 900;
  margin: 2.5rem auto 2.2rem auto;
  background: linear-gradient(90deg, #1877f2, #0a8d36ff, #2edb1eff, #1877f2);
  background-size: 300% 100%;
  color: transparent;
  -webkit-background-clip: text;
  background-clip: text;
  filter: drop-shadow(0 6px 18px rgba(24,119,242,0.13));
  letter-spacing: 1.5px;
  animation: gradientMove 5s linear infinite, fadeInUp 1.1s cubic-bezier(0.4,0,0.2,1);
  transition: transform 0.3s cubic-bezier(0.4,0,0.2,1), filter 0.3s;
  cursor: pointer;
}
.publications-title:hover {
  transform: scale(1.04) translateY(-2px);
  filter: drop-shadow(0 10px 32px rgba(24,119,242,0.18));
}
</style>

<style>
/* Animation d'apparition magique au scroll */
.scroll-fade {
  opacity: 0;
  transform: translateY(40px) scale(0.98);
  transition: opacity 0.8s cubic-bezier(.4,0,.2,1), transform 0.8s cubic-bezier(.4,0,.2,1);
  will-change: opacity, transform;
}
.scroll-fade.visible {
  opacity: 1;
  transform: translateY(0) scale(1);
  filter: drop-shadow(0 6px 18px rgba(24,119,242,0.10));
}
</style>
<script>
// Apparition magique au scroll (IntersectionObserver)
document.addEventListener('DOMContentLoaded', function() {
  var els = document.querySelectorAll('.scroll-fade');
  if ('IntersectionObserver' in window) {
    var obs = new IntersectionObserver(function(entries, observer) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.18 });
    els.forEach(function(el) { obs.observe(el); });
  } else {
    // Fallback: tout afficher
    els.forEach(function(el) { el.classList.add('visible'); });
  }
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="assets/js/main.js" defer></script>
</body>
</html>