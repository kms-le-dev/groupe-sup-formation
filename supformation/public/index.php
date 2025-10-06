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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css" /> 
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






<style>
  

  h1 {
    text-align: center;
    color: dodgerblue;
    margin-bottom: 1rem;
  }

  /* ===== STYLE GÉNÉRAL DU CARROUSEL ===== */

  .carousel5 {
    width: 320px;
    height: 220px;
    border-radius: 18px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 12px 36px rgba(2,6,23,0.12);
    background: #fff;
    transition: transform .28s ease, box-shadow .28s ease;
  }

  .carousel5:hover { transform: translateY(-6px); box-shadow: 0 28px 60px rgba(2,6,23,0.14); }

  .carousel-track5 {
    display: flex;
    transition: transform 0.6s ease-in-out;
    will-change: transform;
  }

  .carousel-item5 {
    flex: 0 0 100%;
    text-align: center;
    position: relative;
  }

  .carousel-item5 img {
    width: 100%;
    height: 220px;
    border-radius: 12px;
    object-fit: cover;
    opacity: 0.99;
    display: block;
  }

  .carousel-item5 .caption {
    position: absolute;
    bottom: 10px;
    left: 0;
    right: 0;
    color: #fff;
    background: rgba(0,0,0,0.4);
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 0.75rem;
    text-shadow: 1px 1px 2px #000;
  }

  /* ===== BOUTONS ===== */
  .carousel-btn5 {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: linear-gradient(180deg,#1e40af,#2563eb);
    color: white;
    border: 2px solid rgba(255,255,255,0.85);
    border-radius: 50%;
    width: 42px;
    height: 42px;
    cursor: pointer;
    font-size: 1rem;
    opacity: 0.95;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(2,6,23,0.18), 0 2px 6px rgba(37,99,235,0.12) inset;
    transition: transform .16s cubic-bezier(.2,.9,.2,1), opacity .16s ease, box-shadow .16s ease;
  }

  .carousel-btn5:hover {
    opacity: 1;
    transform: translateY(-50%) scale(1.08);
    box-shadow: 0 18px 40px rgba(2,6,23,0.2), 0 4px 10px rgba(37,99,235,0.16) inset;
  }

  /* place arrows inside the rectangle */
  .carousel-btn5.prev { left: 12px; }
  .carousel-btn5.next { right: 12px; }

  /* ===== POINTS ===== */
  .carousel-dots5 { display:flex; justify-content:center; gap:.4rem; margin-top:.6rem; }
  .carousel-dot5 { width:9px; height:9px; background:#cbd5e1; border-radius:50%; cursor:pointer; transition: transform .18s ease, background .18s ease; border:2px solid transparent; }
  .carousel-dot5.active { background: linear-gradient(135deg,#2563eb,#06b6d4); transform: scale(1.25); box-shadow: 0 8px 20px rgba(37,99,235,0.12); border-color: rgba(0,0,0,0.03); }

  /* ===== CONTAINER MULTIPLE (3x2 layout) ===== */
  .carousel-group5 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    justify-content: center;
    gap: 2rem;
    align-items: start;
    max-width: 1400px;
    margin: 0 auto;
    padding: 1.25rem 0;
  }

  /* responsive sizes: 3 cols desktop, 2 cols tablet, 1 col mobile */
  @media (min-width: 1400px) { .carousel5 { width: 420px; height: 300px; } .carousel-item5 img { height:300px; } }
  @media (min-width: 900px) and (max-width: 1399px) { .carousel5 { width: 360px; height: 260px; } .carousel-item5 img { height:260px; } .carousel-group5 { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 899px) { .carousel5 { width: 100%; height: 220px; } .carousel-item5 img { height:220px; } .carousel-group5 { grid-template-columns: 1fr; gap: 1rem; } }
</style>


<h1>GSF</h1>

<div class="carousel-group5">

  <!-- ===== CAROUSEL 1 ===== -->
  <div class="carousel5" id="carousel15" tabindex="0" aria-roledescription="carousel">
    <div class="carousel-track5">
      <div class="carousel-item5"><img src="assets/fdfp0.jpg" alt="Voyage"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/IMG-20251003-WA0008.jpg" alt="Aventure"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/IMG-20251003-WA0011.jpg" alt="Découverte"><div class="caption"></div></div>
    </div>
    <button class="carousel-btn5 prev" aria-label="Précédent">❮</button>
    <button class="carousel-btn5 next" aria-label="Suivant">❯</button>
    <div class="carousel-dots5" role="tablist" aria-label="Pagination"></div>
  </div>

  <!-- ===== CAROUSEL 2 ===== -->
  <div class="carousel5" id="carousel25" tabindex="0" aria-roledescription="carousel">
    <div class="carousel-track5">
      <div class="carousel-item5"><img src="assets/IMG-20251003-WA0013.jpg" alt="Technologie"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/IMG-20251003-WA0015.jpg" alt="Innovation"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/IMG-20251003-WA0016.jpg" alt="Création"><div class="caption"></div></div>
    </div>
    <button class="carousel-btn5 prev" aria-label="Précédent">❮</button>
    <button class="carousel-btn5 next" aria-label="Suivant">❯</button>
    <div class="carousel-dots5" role="tablist" aria-label="Pagination"></div>
  </div>

  <!-- ===== CAROUSEL 3 ===== -->
  <div class="carousel5" id="carousel35" tabindex="0" aria-roledescription="carousel">
    <div class="carousel-track5">
      <div class="carousel-item5"><img src="assets/0img.jpg" alt="Mode"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/1img.jpg" alt="Style"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/2img.jpg" alt="Élégance"><div class="caption"></div></div>
    </div>
    <button class="carousel-btn5 prev" aria-label="Précédent">❮</button>
    <button class="carousel-btn5 next" aria-label="Suivant">❯</button>
    <div class="carousel-dots5" role="tablist" aria-label="Pagination"></div>
  </div>

  <!-- ===== CAROUSEL 4 ===== -->
  <div class="carousel5" id="carousel45" tabindex="0" aria-roledescription="carousel">
    <div class="carousel-track5">
      <div class="carousel-item5"><img src="assets/3img.jpg" alt="Cuisine"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/ens.jpg" alt="Saveur"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/ens0.jpg" alt="Gourmet"><div class="caption"></div></div>
    </div>
    <button class="carousel-btn5 prev" aria-label="Précédent">❮</button>
    <button class="carousel-btn5 next" aria-label="Suivant">❯</button>
    <div class="carousel-dots5" role="tablist" aria-label="Pagination"></div>
  </div>

  <!-- ===== CAROUSEL 5 ===== -->
  <div class="carousel5" id="carousel45" tabindex="0" aria-roledescription="carousel">
    <div class="carousel-track5">
      <div class="carousel-item5"><img src="assets/IMG-20251003-WA0033.jpg" alt="Cuisine"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/IMG-20251003-WA0032.jpg" alt="Saveur"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/IMG-20251003-WA0031.jpg" alt="Gourmet"><div class="caption"></div></div>
    </div>
    <button class="carousel-btn5 prev" aria-label="Précédent">❮</button>
    <button class="carousel-btn5 next" aria-label="Suivant">❯</button>
    <div class="carousel-dots5" role="tablist" aria-label="Pagination"></div>
  </div>

  <!-- ===== CAROUSEL 6 ===== -->
  <div class="carousel5" id="carousel55" tabindex="0" aria-roledescription="carousel">
    <div class="carousel-track5">
      <div class="carousel-item5"><img src="assets/ens1.jpg" alt="Sport"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/fdfp.jpg" alt="Énergie"><div class="caption"></div></div>
      <div class="carousel-item5"><img src="assets/fdfp0.jpg" alt="Force"><div class="caption"></div></div>
    </div>
    <button class="carousel-btn5 prev" aria-label="Précédent">❮</button>
    <button class="carousel-btn5 next" aria-label="Suivant">❯</button>
    <div class="carousel-dots5" role="tablist" aria-label="Pagination"></div>
  </div>

</div>



</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="assets/js/main.js" defer></script>

<script>
  // === CARROUSELS CIRCULAIRES MULTIPLES ===
  document.querySelectorAll('.carousel').forEach(carousel => {
    const track = carousel.querySelector('.carousel-track');
    const items = Array.from(track.children);
    const prevBtn = carousel.querySelector('.carousel-btn.prev');
    const nextBtn = carousel.querySelector('.carousel-btn.next');
    const dotsContainer = carousel.querySelector('.carousel-dots');
    let index = 0;

    // Crée les points
    items.forEach((_, i) => {
      const dot = document.createElement('span');
      dot.classList.add('carousel-dot');
      if (i === 0) dot.classList.add('active');
      dot.addEventListener('click', () => {
        index = i;
        updateCarousel();
      });
      dotsContainer.appendChild(dot);
    });
    const dots = dotsContainer.querySelectorAll('.carousel-dot');

    function updateCarousel() {
      track.style.transform = `translateX(-${index * 100}%)`;
      dots.forEach(dot => dot.classList.remove('active'));
      dots[index].classList.add('active');
    }

    nextBtn.addEventListener('click', () => {
      index = (index + 1) % items.length;
      updateCarousel();
    });

    prevBtn.addEventListener('click', () => {
      index = (index - 1 + items.length) % items.length;
      updateCarousel();
    });
  });
</script>
<script>
// === INIT : carrousels circulaires .carousel5 ===
(() => {
  const carousels = document.querySelectorAll('.carousel5');
  carousels.forEach(carousel => {
    const track = carousel.querySelector('.carousel-track5');
    const items = Array.from(track.children);
    const prev = carousel.querySelector('.carousel-btn5.prev');
    const next = carousel.querySelector('.carousel-btn5.next');
    const dotsContainer = carousel.querySelector('.carousel-dots5');
    let index = 0;
    let autoplayInterval = null;
    const AUTOPLAY_MS = 3500;

    // create dots
    items.forEach((_, i) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'carousel-dot5';
      dot.setAttribute('aria-label', `Aller au slide ${i+1}`);
      dot.addEventListener('click', () => { index = i; goTo(index); resetAutoplay(); });
      dotsContainer.appendChild(dot);
    });

    const dots = Array.from(dotsContainer.children);

    function goTo(i) {
      index = (i + items.length) % items.length;
      track.style.transform = `translateX(-${index * 100}%)`;
      dots.forEach(d => d.classList.remove('active'));
      if (dots[index]) dots[index].classList.add('active');
    }

    function nextSlide() { goTo(index + 1); }
    function prevSlide() { goTo(index - 1); }

    next.addEventListener('click', () => { nextSlide(); resetAutoplay(); });
    prev.addEventListener('click', () => { prevSlide(); resetAutoplay(); });

    // keyboard navigation when carousel focused
    carousel.addEventListener('keydown', e => {
      if (e.key === 'ArrowRight') { nextSlide(); resetAutoplay(); }
      if (e.key === 'ArrowLeft') { prevSlide(); resetAutoplay(); }
    });

    // touch support (swipe)
    let startX = 0;
    carousel.addEventListener('touchstart', e => { startX = e.changedTouches[0].clientX; pauseAutoplay(); }, {passive:true});
    carousel.addEventListener('touchend', e => { const dx = e.changedTouches[0].clientX - startX; if (dx < -30) { nextSlide(); } else if (dx > 30) { prevSlide(); } resetAutoplay(); }, {passive:true});

    // pause on hover/focus
    carousel.addEventListener('mouseenter', pauseAutoplay);
    carousel.addEventListener('mouseleave', () => { resetAutoplay(); });
    carousel.addEventListener('focusin', pauseAutoplay);
    carousel.addEventListener('focusout', () => { resetAutoplay(); });

    function startAutoplay() {
      stopAutoplay();
      autoplayInterval = setInterval(() => { nextSlide(); }, AUTOPLAY_MS);
    }
    function stopAutoplay() { if (autoplayInterval) { clearInterval(autoplayInterval); autoplayInterval = null; } }
    function pauseAutoplay() { stopAutoplay(); }
    function resetAutoplay() { stopAutoplay(); startAutoplay(); }

    // initialize
    goTo(0);
    startAutoplay();
  });
})();
</script>
</body>
</html>
