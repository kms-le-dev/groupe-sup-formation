<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

// Récupérer les publications pour le domaine Placement (avec fallback pour ancien schéma)
$publications = [];
$domain_id = 0;
try {
  $stmt = $pdo->prepare("SELECT id FROM domains WHERE slug = ?");
  $stmt->execute(['placement-personnel']);
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
    $stmt->execute(['placement']);
    $publications = $stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    try {
      $stmt = $pdo->prepare(
        "SELECT p.*, m.filename AS media_file 
         FROM publications p
         LEFT JOIN media m ON p.media_id = m.id
         JOIN domains d ON p.domain_id = d.id
         WHERE d.slug = ? AND p.status = 'published'
         ORDER BY p.published_at DESC"
      );
      $stmt->execute(['placement-personnel']);
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
    
    <div class="placement-illustration scroll-fade">
      <img src="assets/pla.jpg" alt="Placement de personnel" loading="lazy" class="scroll-fade">
      <div class="pulse-glow"></div>
    </div>

    <div class="placement-text scroll-fade">
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
      <a href="" class="btn-placement">Je veux être accompagné</a>
    </div>
  </div>
</section>


<!-- Zone publications -->
<div class="publications">
  <h3 class="publications-title">Actualités & Publications</h3>
    <?php if ($publications): ?>
        <?php foreach ($publications as $pub): 
          // Robust fallbacks: the DB and older code sometimes use french keys (titre/contenu) or english (title/content)
          $title = $pub['titre'] ?? $pub['title'] ?? '';
          // Prefer full content, otherwise excerpt
          if (!empty($pub['contenu'])) {
            $content = $pub['contenu'];
          } elseif (!empty($pub['content'])) {
            $content = $pub['content'];
          } elseif (!empty($pub['excerpt'])) {
            $content = $pub['excerpt'];
          } else {
            $content = '';
          }
          // Media may be aliased as media, media_file or filename depending on the query
          $media = $pub['media'] ?? $pub['media_file'] ?? $pub['filename'] ?? '';
        ?>
          <div class="publication scroll-fade">
            <h4><?= e($title) ?></h4>
            <?php if (!empty($content)): ?>
              <p><?= e($content) ?></p>
            <?php endif; ?>
            <?php if (!empty($media)): 
              $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
              if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
                <img src="uploads/<?= e($media) ?>" alt="" class="scroll-fade">
                <div class="download-link"><a href="uploads/<?= e($media) ?>" download="<?= e(basename($media)) ?>">Télécharger</a></div>
              <?php elseif ($ext === 'mp4'): ?>
                <video src="uploads/<?= e($media) ?>" controls class="scroll-fade"></video>
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



<!-- Choix modal: Entreprise vs Chercheur d'emploi -->
<style>
  /* Modal overlay and centered box */
  .modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,0.45);
    z-index: 9999;
    padding: 1rem;
  }
  .modal[aria-hidden="false"] { display:flex; }
  .modal-inner {
    background: #fff;
    padding: 1.25rem 1.5rem;
    border-radius: 10px;
    max-width: 720px;
    width: 100%;
    box-shadow: 0 18px 50px rgba(0,0,0,0.25);
    text-align: center;
  }
  body.modal-open { overflow: hidden; }
</style>

<div id="placementChoice" class="modal" aria-hidden="true">
  <div class="modal-inner" role="dialog" aria-modal="true">
    <h2>Êtes-vous</h2>
    <p>Choisissez le type de formulaire :</p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
      <button id="asCompany" class="btn">Entreprise à la recherche de personnel</button>
      <button id="asJobseeker" class="btn">En quête d'emploi</button>
    </div>
    <div style="text-align:center;margin-top:0.75rem;"><button id="closeChoice" class="btn small">Annuler</button></div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var btn = document.querySelector('.btn-placement');
  var modal = document.getElementById('placementChoice');
  var asCompany = document.getElementById('asCompany');
  var asJob = document.getElementById('asJobseeker');
  var closeBtn = document.getElementById('closeChoice');
  if (!btn || !modal) return;
  btn.addEventListener('click', function(e){
    e.preventDefault();
    modal.style.display='flex';
    modal.setAttribute('aria-hidden','false');
    document.body.classList.add('modal-open');
    // focus first action for accessibility
    setTimeout(function(){ asCompany.focus && asCompany.focus(); }, 40);
  });
  closeBtn.addEventListener('click', function(){
    modal.style.display='none';
    modal.setAttribute('aria-hidden','true');
    document.body.classList.remove('modal-open');
  });
  asCompany.addEventListener('click', function(){ window.location = 'form_placement1.php'; });
  asJob.addEventListener('click', function(){ window.location = 'form_placement2.php'; });

  // close on backdrop click
  modal.addEventListener('click', function(e){
    if (e.target === modal) {
      modal.style.display='none';
      modal.setAttribute('aria-hidden','true');
      document.body.classList.remove('modal-open');
    }
  });

  // close on Escape
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' || e.key === 'Esc') {
      if (modal.getAttribute('aria-hidden') === 'false') {
        modal.style.display='none';
        modal.setAttribute('aria-hidden','true');
        document.body.classList.remove('modal-open');
      }
    }
  });
});
</script>

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

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!-- Scroll reveal (fade + translate) -->
<style>
.scroll-fade { opacity: 0; transform: translateY(36px) scale(0.985); transition: opacity 0.75s cubic-bezier(.4,0,.2,1), transform 0.75s cubic-bezier(.4,0,.2,1); will-change: opacity, transform; }
.scroll-fade.visible { opacity: 1; transform: translateY(0) scale(1); }
</style>
<script>
document.addEventListener('DOMContentLoaded', function(){
  var els = document.querySelectorAll('.scroll-fade');
  if (!els.length) return;
  if ('IntersectionObserver' in window) {
    var obs = new IntersectionObserver(function(entries, observer){
      entries.forEach(function(entry){
        if (entry.isIntersecting) { entry.target.classList.add('visible'); observer.unobserve(entry.target); }
      });
    }, { threshold: 0.18 });
    els.forEach(function(el){ obs.observe(el); });
  } else {
    els.forEach(function(el){ el.classList.add('visible'); });
  }
});
</script>
