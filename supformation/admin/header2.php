<?php
// includes/header.php
$user = current_user();
?>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand">
      <a href="../public/index.php"><img src="../public/assets/logo.png" alt="logo" class="logo"></a>
    </div>

    <button class="menu-toggle" id="adminMenuToggle" aria-expanded="false" aria-controls="adminMobilePanel">
      <span class="hamburger"></span>
      
    </button>

    <nav class="main-nav">
      <a href="../public/enseignement.php">Enseignement Supérieur</a>
      <a href="../public/placement.php">Placement de Personnel</a>
      <a href="../public/fdfp.php">Cabinet FDFP</a>
      <a href="../public/contact.php">Contact</a>
    </nav>
    <div class="auth">
      <?php if ($user): ?>
        <span>Salut, <?=htmlspecialchars($user['first_name'])?></span>
        <a href="../public/logout.php" class="btn small">Déconnexion</a>
        <?php if (is_admin()): ?>
          <a href="dashboard.php" class="btn small">Admin</a>
        <?php endif; ?>
      <?php else: ?>
        <a href="../public/login.php" class="btn small">Connexion</a>
        <a href="../public/register.php" class="btn small primary">Inscription</a>
      <?php endif; ?>
    </div>
  </div>

  <div id="adminMobilePanel" class="mobile-panel" aria-hidden="true">
    <div class="mobile-panel-inner">
      <div class="brand-mobile">
        <a href="../public/index.php"><img src="../public/assets/logo.png" alt="logo" class="logo"></a>
      </div>
      <nav class="mobile-nav">
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <a href="../public/enseignement.php">Enseignement Supérieur</a>
        <a href="../public/placement.php">Placement de Personnel</a>
        <a href="../public/fdfp.php">Cabinet FDFP</a>
        <a href="../public/contact.php">Contact</a>
      </nav>
      <div class="mobile-auth">
        <?php if ($user): ?>
          <div class="mobile-greet">Salut, <?=htmlspecialchars($user['first_name'])?></div>
          <a href="../public/logout.php" class="btn">Déconnexion</a>
          <?php if (is_admin()): ?>
            <a href="dashboard.php" class="btn">Admin</a>
          <?php endif; ?>
        <?php else: ?>
          <a href="../public/login.php" class="btn">Connexion</a>
          <a href="../public/register.php" class="btn primary">Inscription</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

<style>
/* Reuse mobile panel styles for admin header (updated) */
.menu-toggle, #adminMenuToggle { display: none; background: transparent; border: none; width:48px; height:48px; }
.menu-toggle .hamburger, #adminMenuToggle .hamburger { display:block; width:22px; height:2px; background:#0f172a; position:relative; margin-top: 20px; }
#adminMenuToggle .hamburger::before, #adminMenuToggle .hamburger::after { content:''; position:absolute; left:0; width:22px; height:2px; background:#0f172a; }
#adminMenuToggle .hamburger::before { top:-7px; }
#adminMenuToggle .hamburger::after { top:7px; }
#adminMobilePanel { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:9999; align-items:center; justify-content:center; }
#adminMobilePanel .mobile-panel-inner { background:#fff; padding:1.5rem; border-radius:12px; width:min(92%,420px); text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.2); }

/* On small screens, hide desktop chrome and center the toggle */
@media (max-width:1024px){
  .main-nav, .auth, .brand { display:none !important; }
  #adminMenuToggle { display:flex; }
  .site-header { background: transparent !important; box-shadow:none !important; padding:0.30rem 0 !important; }
  /* center the toggle horizontally inside header */
  .header-inner { display:flex; align-items:center; justify-content:center; padding:1rem 1rem; }
}

/* admin mobile panel layout: logo centered on top and content (nav + auth) below */
#adminMobilePanel .mobile-panel-inner { display:flex; flex-direction:column; align-items:center; gap:1rem; }
#adminMobilePanel .brand-mobile { width:100%; display:flex; justify-content:center; }
#adminMobilePanel .brand-mobile .logo { height:64px; width:auto; display:block; }
#adminMobilePanel .mobile-nav {
  display:flex;
  flex-direction:column;
  gap:0.75rem;
  width:auto;
  align-items:center;
  margin:auto;
}
#adminMobilePanel .mobile-nav a {
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width:100%;
  max-width:340px;
  padding:0.5rem 1rem;
  border-radius:999px;
  text-decoration:none;
  color:white;
  font-weight:800;
  letter-spacing:0.4px;
  text-transform:uppercase;
  background: #46b903ff;
  box-shadow: 0 8px 24px rgba(15,23,42,0.12);
  transform: translateY(0) scale(1);
  opacity:1;
  transition: transform 420ms cubic-bezier(.2,.9,.2,1), opacity 300ms ease, box-shadow 200ms ease;
}
#adminMobilePanel .mobile-nav a:hover,
#adminMobilePanel .mobile-nav a:focus {
  transform: translateY(0) scale(1.02);
  box-shadow: 0 18px 40px rgba(15,23,42,0.16);
  outline: none;
}
#adminMobilePanel .mobile-nav a.revealed {
  transform: translateY(0) scale(1);
  opacity:1;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var btn = document.getElementById('adminMenuToggle');
  var panel = document.getElementById('adminMobilePanel');
  if (!btn || !panel) return;
  function ensureMobileLogoAdmin() {
    try {
      var mainLogo = document.querySelector('.brand a img.logo');
      var mobileBrand = document.querySelector('#adminMobilePanel .brand-mobile');
      if (mainLogo && mobileBrand) {
        var exist = mobileBrand.querySelector('img.logo');
        if (exist) exist.remove();
        var clone = mainLogo.cloneNode(true);
        clone.style.height = '64px';
        clone.style.width = 'auto';
        clone.classList.add('logo');
        mobileBrand.appendChild(clone);
      }
    } catch (e) { }
  }

  btn.addEventListener('click', function(){
    var expanded = btn.getAttribute('aria-expanded') === 'true';
    btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
    if (expanded) {
      // close
      panel.style.display = 'none';
      panel.setAttribute('aria-hidden','true');
      btn.setAttribute('aria-expanded','false');
    } else {
      // open
      ensureMobileLogoAdmin();
      panel.style.display = 'flex';
      panel.setAttribute('aria-hidden','false');
      btn.setAttribute('aria-expanded','true');
      // focus first link for accessibility
      var first = panel.querySelector('.mobile-nav a');
      if (first) first.focus();
    }
  });

  // close when clicking on backdrop (outside inner box)
  function closePanel() {
    panel.style.display = 'none';
    panel.setAttribute('aria-hidden','true');
    btn.setAttribute('aria-expanded','false');
  }

  // existing simple backdrop click (kept) — this catches direct clicks on the overlay
  panel.addEventListener('click', function(e){
    if (e.target === panel) {
      closePanel();
    }
  });

  // robust: close when clicking anywhere outside the inner box (covers more edge cases)
  document.addEventListener('click', function(e){
    // only consider when panel is open
    if (panel.style.display !== 'flex' && panel.getAttribute('aria-hidden') === 'true') return;
    var inner = panel.querySelector('.mobile-panel-inner');
    if (!inner) return;
    // if click target is not inside inner and not the toggle button, close
    if (!inner.contains(e.target) && !btn.contains(e.target)) {
      closePanel();
    }
  }, true); // use capture to run early

  // close on Esc key
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' || e.key === 'Esc') {
      if (panel.style.display === 'flex' || panel.getAttribute('aria-hidden') === 'false') {
        panel.style.display = 'none';
        panel.setAttribute('aria-hidden','true');
        btn.setAttribute('aria-expanded','false');
      }
    }
  });
});
// logo is ensured when needed inside the DOMContentLoaded handler
</script>
