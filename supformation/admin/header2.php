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
      <a href="../public/index.php">Accueil</a>
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
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <a href="../public/index.php">Accueil</a><br>
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
#adminMobilePanel .mobile-panel-inner { background:#fff; padding:1.5rem; border-radius:12px; width:min(92%,420px); text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.2); transform-origin: center center; transition: transform 360ms cubic-bezier(.2,.9,.2,1), opacity 300ms ease; opacity:0; transform: translateY(-8px) scale(.98); }

/* On small screens, hide desktop chrome and show toggle aligned to right */
@media (max-width:1024px){
  .main-nav, .auth, .brand { display:none !important; }
  #adminMenuToggle { display:flex; position: absolute; right: 12px; top: 12px; }
  .site-header { background: transparent !important; box-shadow:none !important; padding:0.30rem 0 !important; position: sticky; top: 0; z-index: 999; }
  /* center the inner header but leave toggle on right */
  .header-inner { display:flex; align-items:center; justify-content:center; padding:1rem 1rem; position:relative; }
  /* when panel open we will set panel inner to visible via JS */
  #adminMobilePanel[aria-hidden="false"] .mobile-panel-inner { opacity:1; transform: translateY(0) scale(1); }
}

/* Center main navigation on wider screens */
.header-inner { display:flex; align-items:center; justify-content:center; gap:1rem; }
.main-nav { display:flex; gap:1.25rem; justify-content:center; align-items:center; }
.main-nav a { text-decoration:none; color:#0f172a; font-weight:600; }

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
  transform: translateY(6px) scale(.98);
  opacity:0;
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
// Animate reveal of mobile-nav links when panel opens
(function(){
  function revealAdminLinks(panel){
    var links = panel.querySelectorAll('.mobile-nav a');
    links.forEach(function(l){ l.classList.remove('revealed'); });
    links.forEach(function(link, idx){
      setTimeout(function(){
        link.classList.add('revealed');
      }, 80 * idx + 150);
    });
  }
  // observe attribute changes to animate when shown
  var adminPanel = document.getElementById('adminMobilePanel');
  if (adminPanel) {
    var obs = new MutationObserver(function(mutations){
      mutations.forEach(function(m){
        if (m.attributeName === 'aria-hidden'){
          if (adminPanel.getAttribute('aria-hidden') === 'false') {
            // ensure inner CSS transition finishes then reveal links
            setTimeout(function(){ revealAdminLinks(adminPanel); }, 60);
          } else {
            // when closed, remove revealed classes so next open replays
            var links = adminPanel.querySelectorAll('.mobile-nav a');
            links.forEach(function(l){ l.classList.remove('revealed'); });
          }
        }
      });
    });
    obs.observe(adminPanel, { attributes: true });
  }
})();
</script>

<!-- Top bar for small screens (admin header) -->
<div class="top-bar" aria-hidden="true">
  <nav class="top-nav">
    <a href="../public/index.php" data-icon="🏠">Accueil</a>
    <a href="../public/contact.php" data-icon="✉️">Contact</a>
    <?php if ($user): ?>
      <a href="#" data-icon="👋" class="top-greet" onclick="event.preventDefault();">Salut, <?=htmlspecialchars($user['first_name'])?></a>
      <a href="../public/logout.php" data-icon="🔑">Déconnexion</a>
    <?php else: ?>
      <a href="../public/login.php" data-icon="🔑">Connexion</a>
      <a href="../public/register.php" data-icon="📝">Inscription</a>
    <?php endif; ?>
  </nav>
</div>

<!-- Bottom bar fixed on small screens (admin header) -->
<div class="bottom-bar" aria-hidden="true">
  <nav class="bottom-nav">
    <a href="../public/enseignement.php" data-icon="🎓">Enseignement Supérieur</a>
    <a href="../public/placement.php" data-icon="🤝">Placement</a>
    <a href="../public/fdfp.php" data-icon="🏢">Cabinet FDFP</a>
    <?php if ($user && is_admin()): ?>
      <a href="../admin/dashboard.php" data-icon="⚙️">Admin</a>
    <?php endif; ?>
  </nav>
</div>

<style>
/* Top/bottom bars styles for admin header (reusing same classes) */
.top-bar { display:none; }
.bottom-bar { display:none; }
@media (max-width:1024px){
  /* hide original admin toggle/panel to avoid conflicts */
  #adminMenuToggle, #adminMobilePanel { display:none !important; }
  .top-bar { display:block; position:fixed; top:0; left:0; right:0; background:rgba(255,255,255,0.98); z-index:10000; box-shadow:0 6px 20px rgba(0,0,0,0.08); }
  .top-nav{ display:flex; gap:0.25rem; justify-content:space-around; align-items:center; padding:0.5rem 0; }
  .top-nav a{ color:#063244; text-decoration:none; font-weight:700; padding:0.5rem 0.75rem; }
  .bottom-bar{ display:block; position:fixed; bottom:0; left:0; right:0; background:rgba(255,255,255,0.98); z-index:10000; box-shadow:0 -6px 20px rgba(0,0,0,0.08); }
  .bottom-nav{ display:flex; gap:0.25rem; justify-content:space-around; align-items:center; padding:0.5rem 0; }
  .bottom-nav a{ color:#063244; text-decoration:none; font-weight:700; padding:0.5rem 0.75rem; }
  body{ padding-top:56px; padding-bottom:64px; }
}

/* Enhanced look */
.top-bar .top-nav a, .bottom-bar .bottom-nav a{ position:relative; display:inline-flex; flex-direction:column; align-items:center; justify-content:center; gap:4px; padding:8px 10px; border-radius:10px; min-width:72px; text-align:center; font-size:0.92rem; color:#063244; background:transparent; transition:background 200ms ease, transform 180ms, box-shadow 200ms ease; }
.top-bar .top-nav a::before, .bottom-bar .bottom-nav a::before{ content:attr(data-icon); font-size:1.25rem; display:block; }
.top-bar .top-nav a:hover, .bottom-bar .bottom-nav a:hover{ transform:translateY(-3px); background:rgba(5,150,105,0.06); box-shadow:0 6px 18px rgba(2,6,23,0.06); }
.top-bar .top-nav a.active, .bottom-bar .bottom-nav a.active{ background:linear-gradient(135deg,#06b6d4,#2563eb); color:#fff; box-shadow:0 10px 30px rgba(37,99,235,0.14); }

@supports(padding: max(0px)){
  .top-bar{ padding-top: calc(env(safe-area-inset-top) + 8px); }
  body{ padding-top: calc(env(safe-area-inset-top) + 56px); }
  .bottom-bar{ padding-bottom: calc(env(safe-area-inset-bottom) + 8px); }
  body{ padding-bottom: calc(env(safe-area-inset-bottom) + 64px); }
}
</style>

<script>
// Manage visibility aria-hidden for admin top/bottom bars
(function(){
  function updateBars(){
    var top = document.querySelector('.top-bar');
    var bottom = document.querySelector('.bottom-bar');
    if(!top||!bottom) return;
    var small = window.matchMedia('(max-width:1024px)').matches;
    if(small){ top.setAttribute('aria-hidden','false'); top.style.display='block'; bottom.setAttribute('aria-hidden','false'); bottom.style.display='block'; }
    else{ top.setAttribute('aria-hidden','true'); top.style.display='none'; bottom.setAttribute('aria-hidden','true'); bottom.style.display='none'; }
  }
  document.addEventListener('DOMContentLoaded', updateBars);
  window.addEventListener('resize', updateBars);
})();
</script>

<script>
// mark active link in admin header bars
(function(){
  function markActive(){
    var path = window.location.pathname.split('/').pop();
    var topLinks = document.querySelectorAll('.top-bar .top-nav a');
    var bottomLinks = document.querySelectorAll('.bottom-bar .bottom-nav a');
    [topLinks, bottomLinks].forEach(function(list){
      list.forEach(function(a){
        var href = a.getAttribute('href');
        var file = href.split('/').pop();
        if(file === path || (file === 'index.php' && (path === '' || path === 'index.php'))){ a.classList.add('active'); }
        else { a.classList.remove('active'); }
      });
    });
  }
  document.addEventListener('DOMContentLoaded', markActive);
  window.addEventListener('popstate', markActive);
})();
</script>
