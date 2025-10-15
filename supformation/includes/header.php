<?php
// includes/header.php
$user = current_user();
?>

<?php include 'loader.php'; ?>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand">
      <a href="../public/index.php"><img src="assets/logo.png" alt="logo" class="logo"></a>
    </div>

  

    <nav class="main-nav">
      <a href="index.php">Accueil</a>
      <a href="enseignement.php">Enseignement Supérieur</a>
      <a href="placement.php">Placement de Personnel</a>
      <a href="fdfp.php">Cabinet FDFP</a>
      <a href="contact.php">Contact</a>
    </nav>
    <div class="auth">
      <?php if ($user): ?>
        <span>Salut, <?=htmlspecialchars($user['first_name'])?></span>
        <a href="logout.php" class="btn small">Déconnexion</a>
        <?php if (is_admin()): ?>
          <a href="../admin/dashboard.php" class="btn small">Admin</a>
        <?php endif; ?>
      <?php else: ?>
        <a href="login.php" class="btn small">Connexion</a>
        <a href="register.php" class="btn small primary">Inscription</a>
      <?php endif; ?>
    </div>
  </div>

 
</header>

<!-- Top bar for small screens: Accueil, Contact, and auth (Connexion/Inscription or Salut + Déconnexion) -->
<div class="top-bar" aria-hidden="true">
  <nav class="top-nav">
    <a href="index.php" data-icon="🏠">Accueil</a>
    <a href="contact.php" data-icon="✉️">Contact</a>
    <?php if ($user): ?>
      <a href="#" data-icon="👋" class="top-greet" onclick="event.preventDefault();">Salut, <?=htmlspecialchars($user['first_name'])?></a>
      <a href="logout.php" data-icon="🔑">Déconnexion</a>
    <?php else: ?>
      <a href="login.php" data-icon="🔑">Connexion</a>
      <a href="register.php" data-icon="📝">Inscription</a>
    <?php endif; ?>
  </nav>
</div>

<!-- Bottom bar fixed on small screens: Enseignement Supérieur, Placement, Cabinet FDFP (+ Admin for admins) -->
<div class="bottom-bar" aria-hidden="true">
  <nav class="bottom-nav">
    <a href="enseignement.php" data-icon="🎓">Enseignement Sup</a>
    <a href="placement.php" data-icon="🤝">Placement</a>
    <a href="fdfp.php" data-icon="🏢">Cabinet FDFP</a>
    <?php if ($user && is_admin()): ?>
      <a href="../admin/dashboard.php" data-icon="⚙️">Admin</a>
    <?php endif; ?>
  </nav>
</div>


<style>
/* Header collé en haut */
.site-header {
  position: sticky;
  top: 0;
  z-index: 9999;
  background: rgba(255, 255, 255, 0.95);
}

/* --- Mode mobile --- */
@media (max-width: 1024px) {
  /* Cache header desktop */
  .main-nav, .auth, .brand {
    display: none !important;
  }

  .site-header {
    background: transparent !important;
    box-shadow: none !important;
    padding: 0 !important;
  }

  /* --- TOP BAR --- */
  .top-bar {
    display: block;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: rgba(255,255,255,0.98);
    z-index: 10000;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08); /* un peu plus visible */
  }

  .top-nav {
    display: flex;
    justify-content: space-around;
    align-items: center;
    gap: 0.3rem;
    padding: 0.25rem 0; /* ↑ légèrement plus haut */
  }

  /* --- BOTTOM BAR --- */
  .bottom-bar {
    display: block;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255,255,255,0.98);
    z-index: 10000;
    box-shadow: 0 -3px 12px rgba(0,0,0,0.08); /* un peu plus visible */
  }

  .bottom-nav {
    display: flex;
    justify-content: space-around;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0; /* ↑ légèrement plus haut */
  }

  /* --- Liens top & bottom --- */
  .top-bar .top-nav a,
  .bottom-bar .bottom-nav a {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #063244;
    font-weight: 600;
    font-size: 0.78rem; /* ↑ légèrement plus grand */
    min-width: 52px;
    padding: 4px 6px; /* ↑ légèrement plus grand */
    border-radius: 8px;
    transition: all 0.2s ease;
  }

  .top-bar .top-nav a::before,
  .bottom-bar .bottom-nav a::before {
    content: attr(data-icon);
    font-size: 1rem; /* ↑ légèrement plus grand */
    line-height: 1;
  }

  .top-bar .top-nav a:hover,
  .bottom-bar .bottom-nav a:hover {
    background: rgba(5,150,105,0.06);
    transform: translateY(-2px);
  }

  .top-bar .top-nav a.active,
  .bottom-bar .bottom-nav a.active {
    background: #028343ff;
    color: #fff;
    box-shadow: 0 5px 16px rgba(37,99,235,0.14); /* un peu plus visible */
  }

  /* Espace pour éviter chevauchement */
  body {
    padding-top: 38px;  /* ↑ plus confortable */
    padding-bottom: 44px;
  }
}

/* Cache menu toggle sur toutes tailles */
#menuToggle,
.menu-toggle,
#mobilePanel {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  pointer-events: none !important;
}
</style>




<script>
// Manage top/bottom bars visibility and aria-hidden for accessibility
(function(){
  function updateBars() {
    var top = document.querySelector('.top-bar');
    var bottom = document.querySelector('.bottom-bar');
    if (!top || !bottom) return;
    var small = window.matchMedia('(max-width:1024px)').matches;
    if (small) {
      top.setAttribute('aria-hidden','false'); top.style.display='block';
      bottom.setAttribute('aria-hidden','false'); bottom.style.display='block';
    } else {
      top.setAttribute('aria-hidden','true'); top.style.display='none';
      bottom.setAttribute('aria-hidden','true'); bottom.style.display='none';
    }
  }
  document.addEventListener('DOMContentLoaded', updateBars);
  window.addEventListener('resize', updateBars);
})();
</script>

<script>
// Mark active link in top/bottom bars
(function(){
  function markActive(){
    var path = window.location.pathname.split('/').pop();
    var topLinks = document.querySelectorAll('.top-bar .top-nav a');
    var bottomLinks = document.querySelectorAll('.bottom-bar .bottom-nav a');
    [topLinks, bottomLinks].forEach(function(list){
      list.forEach(function(a){
        var href = a.getAttribute('href');
        var file = href.split('/').pop();
        if (file === path || (file === 'index.php' && (path === '' || path === 'index.php'))) {
          a.classList.add('active');
        } else {
          a.classList.remove('active');
        }
      });
    });
  }
  document.addEventListener('DOMContentLoaded', markActive);
  window.addEventListener('popstate', markActive);
})();
</script>
