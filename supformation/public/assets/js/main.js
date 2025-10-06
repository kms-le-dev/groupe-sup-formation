// assets/js/main.js
document.addEventListener('DOMContentLoaded',function(){
  // simple carousel
  const slides = document.querySelectorAll('#mainCarousel img');
  let i = 0;
  function show(idx){
    slides.forEach(s=>s.classList.remove('active'));
    slides[idx].classList.add('active');
  }
  if (slides.length){
    show(0);
    setInterval(()=>{ i = (i+1)%slides.length; show(i); }, 4000);
  }

  // register validation example (if page contains register form)
  const regForm = document.querySelector('#registerForm');
  if (regForm){
    regForm.addEventListener('submit', e=>{
      const pass = regForm.querySelector('input[name="password"]').value;
      const pass2 = regForm.querySelector('input[name="password_confirm"]').value;
      const email = regForm.querySelector('input[name="email"]').value;
      if (!email.includes('@')) {
        alert('Email invalide');
        e.preventDefault();
        return;
      }
      if (pass.length < 8 || pass !== pass2) {
        alert('Vérifiez les mots de passe (>=8 chars et identiques).');
        e.preventDefault();
        return;
      }
    });
  }
  
  // Scroll reveal with direction detection (non-intrusive)
  (function(){
    var lastY = window.scrollY || window.pageYOffset;
    var ticking = false;

    // targets: if element has .scroll-animate keep it; otherwise add it to common blocks
    var autoTargets = Array.prototype.slice.call(document.querySelectorAll('.scroll-animate'));
    var selectors = ['.service-card', '.post', '.GSF h1', '.carousel', '.media-section', '.services-section', '#publications'];
    selectors.forEach(function(sel){
      document.querySelectorAll(sel).forEach(function(el){
        if (autoTargets.indexOf(el) === -1) autoTargets.push(el);
      });
    });

    // unify: add baseline class if missing
    autoTargets.forEach(function(el){ if (!el.classList.contains('scroll-animate')) el.classList.add('scroll-animate'); });

    function onScroll(){
      var y = window.scrollY || window.pageYOffset;
      var dir = (y > lastY) ? 'down' : 'up';
      lastY = y;

      autoTargets.forEach(function(el, idx){
        var rect = el.getBoundingClientRect();
        var vh = window.innerHeight || document.documentElement.clientHeight;
        // reveal when element enters 85% of viewport
        if (rect.top < vh * 0.85 && rect.bottom > 0) {
          // small stagger for multiple items
          var delay = (el.dataset && el.dataset.delay) ? el.dataset.delay : (idx % 6) * 60;
          el.style.setProperty('--delay', delay + 'ms');
          el.classList.add('visible');
          el.classList.remove('dir-up','dir-down');
          el.classList.add('dir-' + dir);
        } else {
          // optionally hide when out of view (keeps it dynamic)
          // remove visible to allow re-trigger on scroll back
          el.classList.remove('visible');
        }
      });
    }

    window.addEventListener('scroll', function(){
      if (!ticking) { window.requestAnimationFrame(function(){ onScroll(); ticking = false; }); }
      ticking = true;
    }, { passive: true });

    // initial run
    onScroll();
  })();

    // Ripple effect for download buttons
    document.querySelectorAll('.download-link a').forEach(function(btn){
      btn.addEventListener('click', function(e){
        var rect = btn.getBoundingClientRect();
        var ripple = document.createElement('span');
        ripple.className = 'ripple';
        ripple.style.left = (e.clientX - rect.left - 50) + 'px';
        ripple.style.top = (e.clientY - rect.top - 50) + 'px';
        btn.appendChild(ripple);
        ripple.style.opacity = '1';
        ripple.style.transform = 'scale(1)';
        setTimeout(function(){
          ripple.style.opacity = '0';
          ripple.style.transform = 'scale(1.5)';
          setTimeout(function(){ if (ripple.parentNode) ripple.parentNode.removeChild(ripple); }, 400);
        }, 200);
        // allow default download to proceed
      });
    });

      // Lightweight fallback for #customCarousel2 (testimonials) if Bootstrap JS not present
      (function(){
        if (!document.querySelector('#customCarousel2')) return;
        // If Bootstrap's carousel exists we skip
        if (window.jQuery && typeof jQuery.fn.carousel === 'function') return;

        const carousel = document.querySelector('#customCarousel2');
        const items = carousel.querySelectorAll('.carousel-item');
        const indicators = carousel.querySelectorAll('.carousel-indicators li');
        let idx = 0;
        function showCarousel(i){
          items.forEach((it, j)=> it.classList.toggle('active', j===i));
          if (indicators.length) indicators.forEach((ind,j)=> ind.classList.toggle('active', j===i));
          idx = i;
        }
        // auto play
        let timer = setInterval(()=> showCarousel((idx+1)%items.length), 4500);
        // indicators click
        indicators.forEach((ind, j)=> ind.addEventListener('click', ()=>{ showCarousel(j); clearInterval(timer); timer = setInterval(()=> showCarousel((idx+1)%items.length), 4500); }));
        // keyboard
        carousel.tabIndex = 0;
        carousel.addEventListener('keydown', function(e){ if (e.key === 'ArrowLeft') { showCarousel((idx-1+items.length)%items.length); } else if (e.key === 'ArrowRight') { showCarousel((idx+1)%items.length); } });
        // init
        showCarousel(0);
      })();
});






