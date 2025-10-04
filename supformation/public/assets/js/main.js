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
});
