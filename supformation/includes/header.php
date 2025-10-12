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

    <!-- Toggle button visible on small screens -->
    <button class="menu-toggle" id="menuToggle" aria-expanded="false" aria-controls="mobilePanel">
      <span class="hamburger"></span>
      
    </button>

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

  <!-- Mobile panel: centered content shown when toggle active -->
  <div id="mobilePanel" class="mobile-panel" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="mobile-panel-inner">
      <div class="brand-mobile">
        <a href="../public/index.php"><img src="assets/logo.png" alt="logo" class="logo"></a>
      </div>
      <nav class="mobile-nav" aria-label="Menu mobile">
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <a href="index.php">Accueil</a><br>
        <a href="enseignement.php">Enseignement Supérieur</a><br>
        <a href="placement.php">Placement de Personnel</a><br>
        <a href="fdfp.php">Cabinet FDFP</a><br>
        <a href="contact.php">Contact</a>
      </nav>
      <div class="mobile-auth">
        <?php if ($user): ?>
          <div class="mobile-greet">Salut, <?=htmlspecialchars($user['first_name'])?></div>
          <a href="logout.php" class="btn">Déconnexion</a>
          <?php if (is_admin()): ?>
            <a href="../admin/dashboard.php" class="btn">Admin</a>
          <?php endif; ?>
        <?php else: ?>
          <a href="login.php" class="btn">Connexion</a>
          <a href="register.php" class="btn primary">Inscription</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

<style>
/* Toggle & mobile panel styles */
.menu-toggle {
  display: none;
  background: transparent;
  border: none;
  width: 48px;
  height: 48px;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

/* Keep header visible on scroll and ensure the mobile toggle is fixed to the right */
.site-header { position: sticky; top: 0; z-index: 9999; background: rgba(255,255,255,0.95); }

.menu-toggle { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); }
.menu-toggle .hamburger,
.menu-toggle .hamburger::before,
.menu-toggle .hamburger::after {
  display: block;
  width: 22px;
  height: 2px;
  background: #0f172a;
  border-radius: 2px;
  position: relative;
}
.menu-toggle .hamburger::before,
.menu-toggle .hamburger::after {
  content: '';
  position: absolute;
  left: 0;
}
.menu-toggle .hamburger::before { top: -7px; }
.menu-toggle .hamburger::after { top: 7px; }



.mobile-panel {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.0);
  z-index: 9999;
  align-items: center;
  justify-content: center;
  transition: background-color 260ms ease;
}
/* when open, make panel visible using .open to avoid inline styles manipulation */
.mobile-panel.open { display: flex; background: rgba(0,0,0,0.45); }
.mobile-panel-inner {
  background: #fff;
  padding: 1.5rem;
  border-radius: 12px;
  width: min(92%, 420px);
  text-align: center;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  /* place logo on top centered, then nav/auth below */
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center; /* center vertically */
  min-height: 60vh; /* ensure panel content is centered in viewport */
  gap: 1rem;
  transform: translateY(12px) scale(0.98);
  opacity: 0;
  transition: transform 300ms cubic-bezier(.2,.9,.2,1), opacity 260ms ease;
}
.brand-mobile .logo { height:64px; width:auto; display:block; margin:0 auto; }
  .mobile-nav {
  display:flex;
  flex-direction:column;
  gap:0.75rem;
  width:100%;
  align-items:center;
  /* center the nav horizontally and vertically inside the panel */
  margin: 0 auto;
  padding: 0; }

  .mobile-nav a {
    display:block;
    text-align: center;
    align-items:center;
    justify-content:center;
    width: min(92%, 340px);
    padding:0.5rem 1rem;
    border-radius:50px;
    text-decoration:none;
    color:white;
    font-weight:800;
    letter-spacing:0.4px;
    text-transform:uppercase;
    background: #46b903ff;
    box-shadow: 0 8px 24px rgba(15,23,42,0.12);
    transform: translateY(16px) scale(0.98);
    opacity:0;
    margin: 0 auto;
    transition: transform 320ms cubic-bezier(.2,.9,.2,1), opacity 260ms ease;
  }
  .mobile-nav a.revealed { transform: translateY(0) scale(1); opacity:1; }
  .mobile-nav a:hover { transform: translateY(0) scale(1.03); box-shadow: 0 18px 40px rgba(15,23,42,0.16); }
.mobile-nav a:hover,
.mobile-nav a:focus {
  transform: translateY(0) scale(1.02);
  box-shadow: 0 18px 40px rgba(15,23,42,0.16);
  outline: none;
}
.mobile-nav a.revealed {
  transform: translateY(0) scale(1);
  opacity:1;
}
.mobile-auth .btn { display:block; margin:0.4rem auto; }
.mobile-greet { margin-bottom:0.6rem; font-weight:700; }

/* Responsive: show toggle on smaller and medium screens and hide desktop nav/auth */
/* Change breakpoint to 1024px so the compact toggle appears earlier */
@media (max-width: 1024px) {
  /* Hide nav and auth and reduce header chrome so toggle is the only visible element */
  .main-nav, .auth, .brand { display: none !important; }
  .site-header { background: transparent !important; box-shadow: none !important; padding: 0.25rem 0 !important; }
  .menu-toggle { display: flex; }
  /* keep the header height minimal so only toggle occupies space */
  .header-inner { display:flex; align-items:center; justify-content:center; padding: 0.25rem 1rem; }
  /* ensure menu toggle is on the right and visible */
  .menu-toggle { display: flex; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); }
}

</style>

<script>
// Toggle mobile panel with small debug and safeguard to ensure mobile links are visible
document.addEventListener('DOMContentLoaded', function(){
  var btn = document.getElementById('menuToggle');
  var panel = document.getElementById('mobilePanel');
  if (!btn || !panel) return;

  // ensure logo is present when opening the panel
  function ensureMobileLogo() {
    try {
      var mainLogo = document.querySelector('.brand a img.logo');
      var mobileBrand = document.querySelector('#mobilePanel .brand-mobile');
      if (mainLogo && mobileBrand) {
        var exist = mobileBrand.querySelector('img.logo');
        if (exist) exist.remove();
        var clone = mainLogo.cloneNode(true);
        clone.style.height = '64px';
        clone.style.width = 'auto';
        clone.classList.add('logo');
        mobileBrand.appendChild(clone);
        console.log('[header] mobile logo cloned');
        return true;
      }
    } catch (e) { console.error('[header] ensureMobileLogo error', e); }
    return false;
  }

  btn.addEventListener('click', function(){
    console.log('[header] menuToggle click, before toggle aria-expanded=', btn.getAttribute('aria-expanded'));
    var expanded = btn.getAttribute('aria-expanded') === 'true';
    btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
    if (expanded) {
      // close
      panel.classList.remove('open');
      panel.setAttribute('aria-hidden','true');
      btn.setAttribute('aria-expanded','false');
      console.log('[header] mobile panel closed');
    } else {
      // open
      // clone logo just before opening to guarantee it is present
      ensureMobileLogo();
      // ensure mobile nav links are visible even if some global CSS hides them
      try {
        var mobileNav = panel.querySelector('.mobile-nav');
        if (mobileNav) {
          mobileNav.style.display = 'block';
          var links = mobileNav.querySelectorAll('a');
          // don't override color here — keep the CSS (e.g. white) so buttons stay readable
          links.forEach(function(a){ a.style.display = 'block'; a.style.visibility = 'visible'; });
        }
      } catch(e) { console.error('[header] forcing mobile-nav visible failed', e); }
      panel.classList.add('open');
      panel.setAttribute('aria-hidden','false');
      btn.setAttribute('aria-expanded','true');
        // animate inner panel in after open to allow CSS transitions
        try { var inner = panel.querySelector('.mobile-panel-inner'); if (inner) { inner.style.transform = 'translateY(0) scale(1)'; inner.style.opacity = '1'; } } catch(e){}
      // focus the panel for accessibility so Esc will work predictably
      var focusable = panel.querySelector('a, button, input, [tabindex]');
      if (focusable) focusable.focus();
      // staggered reveal of links
      try {
        var links = panel.querySelectorAll('.mobile-nav a');
        // clear any existing classes/timers
        links.forEach(function(l){ l.classList.remove('revealed'); });
        if (window._mobileRevealTimers && window._mobileRevealTimers.length) {
          window._mobileRevealTimers.forEach(clearTimeout);
        }
        window._mobileRevealTimers = [];
        links.forEach(function(link, idx){
          var t = setTimeout(function(){ link.classList.add('revealed'); }, 80 * idx + 120);
          window._mobileRevealTimers.push(t);
        });
      } catch(e) { console.error('[header] reveal links failed', e); }
      console.log('[header] mobile panel opened');
    }
  });

  // close when clicking outside inner box (click on backdrop)
  panel.addEventListener('click', function(e){
    if (e.target === panel) {
      // remove open class instead of triggering button to avoid double actions
      // cleanup reveal timers and classes
      if (window._mobileRevealTimers && window._mobileRevealTimers.length) {
        window._mobileRevealTimers.forEach(clearTimeout);
        window._mobileRevealTimers = [];
      }
      var links = panel.querySelectorAll('.mobile-nav a');
      links.forEach(function(l){ l.classList.remove('revealed'); });
      panel.classList.remove('open');
      panel.setAttribute('aria-hidden','true');
      btn.setAttribute('aria-expanded','false');
      console.log('[header] mobile panel closed by backdrop click');
    }
  });

  // helper to close panel
  function closePanel() {
    if (window._mobileRevealTimers && window._mobileRevealTimers.length) {
      window._mobileRevealTimers.forEach(clearTimeout);
      window._mobileRevealTimers = [];
    }
    var links = panel.querySelectorAll('.mobile-nav a');
    links.forEach(function(l){ l.classList.remove('revealed'); });
    panel.classList.remove('open');
    panel.setAttribute('aria-hidden','true');
    btn.setAttribute('aria-expanded','false');
  }

  // robust: close when clicking anywhere outside the inner box (capture phase)
  document.addEventListener('click', function(e){
    if (!panel.classList.contains('open')) return;
    var inner = panel.querySelector('.mobile-panel-inner');
    if (!inner) return;
    if (!inner.contains(e.target) && !btn.contains(e.target)) {
      closePanel();
    }
  }, true);

  // close on Esc key (also already handled, keep for safety)
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' || e.key === 'Esc') {
      if (panel.classList.contains('open')) closePanel();
    }
  });

  // close on Esc key
  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape' || e.key === 'Esc') {
      if (panel.classList.contains('open')) {
        // cleanup reveal timers and classes
        if (window._mobileRevealTimers && window._mobileRevealTimers.length) {
          window._mobileRevealTimers.forEach(clearTimeout);
          window._mobileRevealTimers = [];
        }
        var links = panel.querySelectorAll('.mobile-nav a');
        links.forEach(function(l){ l.classList.remove('revealed'); });
        panel.classList.remove('open');
        panel.setAttribute('aria-hidden','true');
        btn.setAttribute('aria-expanded','false');
        console.log('[header] mobile panel closed by Esc');
      }
    }
  });

  // ensure logo is present at load too (avoids race / path issues)
  ensureMobileLogo();
});
</script>
