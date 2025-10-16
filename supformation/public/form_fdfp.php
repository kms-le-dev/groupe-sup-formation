<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';
// simple public form page for FDFP requests
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Demander une formation - FDFP</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <style>
    .fdfp-form { max-width:760px; margin:1.5rem auto; padding:1.25rem; background:#fff; border-radius:10px; box-shadow:0 8px 30px rgba(0,0,0,0.06); }
    .fdfp-form .row { display:flex; gap:10px; }
    .fdfp-form label { display:block; font-weight:600; margin-bottom:6px; }
    .fdfp-form input, .fdfp-form select, .fdfp-form textarea { width:100%; padding:.7rem 1rem; border-radius:8px; border:1px solid #d1d5db; }
    .fdfp-form .actions { display:flex; gap:.75rem; margin-top:1rem; }
    @media (max-width:720px) {
      .fdfp-form .row { flex-direction:column; }
      .fdfp-form { padding:1rem; margin:1rem; }
    }
  </style>
</head>


<body>
<?php include 'loader.php'; ?>

<div class="container">
  <div class="fdfp-form">
    <h1>Demander une formation</h1>
    <form id="fdfpForm">
      <label>1) Nom de l'entreprise ou de l'institution<input name="company" required></label>
      <label>2) Domaine d'activité<input name="domain" required></label>
      <label>3) Adresse géographique<input name="address" required></label>
      <label>4) Contacts<input name="contact" required></label>
      <label>5) Email<input name="email" type="email" required></label>
      <label>6) Type de formation demandé
        <select name="type" id="typeSelect" required>
          <option value="INSTITUTION PUBLIQUE">INSTITUTION PUBLIQUE</option>
          <option value="ENTREPRISE PRIVÉE">ENTREPRISE PRIVÉE</option>
        </select>
      </label>
      <div id="privateOptions" style="display:none; margin: 10px 0 0 0; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; background: #f8f9fa;">
        <span style="font-weight:600;">Pour les entreprises privées, cochez le(s) projet(s) :</span><br>
        <label style="display:inline-block; margin-right:12px;"><input type="checkbox" name="private_projects[]" value="Plan de formation"> Plan de formation</label>
        <label style="display:inline-block; margin-right:12px;"><input type="checkbox" name="private_projects[]" value="Projet d'apprentissage"> Projet d'apprentissage</label>
        <label style="display:inline-block; margin-right:12px;"><input type="checkbox" name="private_projects[]" value="Projet d'insertion"> Projet d'insertion</label>
        <label style="display:inline-block; margin-right:12px;"><input type="checkbox" name="private_projects[]" value="Projet collectif"> Projet collectif</label>
      </div>
      <label>7) Nombre d'employé<input name="employees" type="number" min="0" required></label>
      <label>8) NIF / DFE (facultatif)<input name="nif"></label>
      <label>RCCM (facultatif)<input name="rccm"></label>

      <div class="actions">
        <button type="button" id="cancelBtn">Annuler</button>
        <button type="submit" id="submitBtn">Soumettre et Télécharger (PDF)</button>
      </div>
<!-- SCRIPT DYNAMIQUE DÉPLACÉ EN BAS DE PAGE -->
</body>
<script>
// Load jsPDF from CDN if missing and provide a helper
function loadJsPDF(timeout = 5000) {
  return new Promise((resolve) => {
    if (window.jspdf && window.jspdf.jsPDF) return resolve(true);
    // already added
    if (document.getElementById('jspdf-cdn')) {
      // wait until available
      const start = Date.now();
      (function wait(){ if (window.jspdf && window.jspdf.jsPDF) return resolve(true); if (Date.now()-start>timeout) return resolve(false); setTimeout(wait,100); })();
      return;
    }
    const s = document.createElement('script');
    s.id = 'jspdf-cdn';
    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
    s.onload = function(){ // wait a tick
      setTimeout(()=> resolve(!!(window.jspdf && window.jspdf.jsPDF)), 50);
    };
    s.onerror = function(){ resolve(false); };
    document.head.appendChild(s);
  });
}

(function(){
  const form = document.getElementById('fdfpForm');
  const cancel = document.getElementById('cancelBtn');
  const submitBtn = document.getElementById('submitBtn');
  const typeSelect = document.getElementById('typeSelect');
  const privateOptions = document.getElementById('privateOptions');

  // Afficher/masquer les options privées selon le choix
  typeSelect.addEventListener('change', function() {
    if (typeSelect.value === 'ENTREPRISE PRIVÉE') {
      privateOptions.style.display = '';
    } else {
      privateOptions.style.display = 'none';
      // décocher toutes les cases si on repasse sur institution publique
      privateOptions.querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false);
    }
  });

  cancel.addEventListener('click', ()=> location.href = 'index.php');

  form.addEventListener('submit', async function(e){
    e.preventDefault();
    submitBtn.disabled = true; submitBtn.textContent = 'Envoi...';
    const meta = {};
    for (const p of new FormData(form).entries()) {
      // Pour les cases à cocher multiples
      if (p[0] === 'private_projects[]') {
        if (!meta['private_projects']) meta['private_projects'] = [];
        meta['private_projects'].push(p[1]);
      } else {
        meta[p[0]] = p[1];
      }
    }

    // ensure jsPDF is available (best-effort)
    const hasJsPDF = await loadJsPDF(4000);
    let out = new FormData();
    out.append('meta', JSON.stringify(meta));
    out.append('type','fdfp');

    // Keep a reference to a client-generated PDF blob (if available) so we can force download later
    let clientPdfBlob = null;
    if (hasJsPDF && window.jspdf && window.jspdf.jsPDF) {
      try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({unit:'pt',format:'a4'});
        const pageW = doc.internal.pageSize.getWidth();
        const pageH = doc.internal.pageSize.getHeight();
        doc.setFont('Helvetica');
        // prepare entries
        const entries = Object.entries(meta).map(([k,v]) => {
          if (Array.isArray(v)) {
            return {k: k.replace(/_/g,' ').toUpperCase(), v: v.join(', ')};
          }
          return {k: k.replace(/_/g,' ').toUpperCase(), v: String(v)};
        });
        const rows = Math.ceil(entries.length / 2);
        const margin = 30; // marges réduites pour agrandir le tableau
        const colGap = 40; // plus d'espace entre les colonnes
        const colWidth = (pageW - margin * 2 - colGap) / 2;
        const rowHeight = 70; // hauteur de ligne augmentée
        const tableHeight = rows * rowHeight;
        // calcul du point de départ pour centrer verticalement
        const title = 'Demande de formation';
        doc.setFontSize(22); doc.setFont(undefined,'bold');
        const titleW = doc.getTextWidth ? doc.getTextWidth(title) : title.length * 8;
        const startY = Math.max(80, (pageH - tableHeight) / 2 + 10);
        // titre
        doc.text(title, (pageW - titleW) / 2, startY - 28);
        doc.setLineWidth(2.2); // lignes plus épaisses
        doc.setDrawColor(44, 62, 80); // couleur plus foncée
        // rectangle extérieur du tableau
        const tableX = margin;
        const tableW = pageW - margin * 2;
        doc.rect(tableX, startY - 10, tableW, tableHeight + 20);
        // lignes horizontales
        for (let i = 0; i <= rows; i++) {
          const y = startY + i * rowHeight;
          doc.line(tableX, y, tableX + tableW, y);
        }
        // ligne verticale centrale
        const midX = tableX + colWidth + colGap / 2;
        doc.line(midX, startY - 10, midX, startY + tableHeight + 10);
        // texte dans les cellules
        const leftX = tableX + 16;
        const rightX = midX + 16;
        doc.setFontSize(14); doc.setFont(undefined,'normal');
        for (let r = 0; r < rows; r++) {
          const li = r * 2;
          const left = entries[li];
          const right = entries[li + 1];
          const cellY = startY + r * rowHeight + 26; // baseline ajustée
          if (left) {
            const text = left.k + ': ' + left.v;
            const lines = doc.splitTextToSize(text, colWidth - 24);
            doc.text(lines, leftX, cellY);
          }
          if (right) {
            const text = right.k + ': ' + right.v;
            const lines = doc.splitTextToSize(text, colWidth - 24);
            doc.text(lines, rightX, cellY);
          }
        }
        const blob = doc.output('blob');
        clientPdfBlob = blob;
        out.append('pdf', blob, 'fdfp_' + Date.now() + '.pdf');
      } catch (err) {
        console.warn('jsPDF generation failed, falling back', err);
      }
    }

    try {
      console.log('Envoi du formulaire...');
      const res = await fetch('save_fdfp.php', { method:'POST', body: out });
      console.log('Réponse reçue:', res.status);
      const json = await res.json();
      console.log('Données reçues:', json);
      if (json.success) {
        // Force download: if we have a client-side blob, download it; otherwise fetch server PDF and download
        try {
          if (clientPdfBlob) {
            const url = URL.createObjectURL(clientPdfBlob);
            const a = document.createElement('a'); a.href = url; a.download = json.filename || ('fdfp_' + Date.now() + '.pdf'); document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
          } else {
            // fetch server PDF and force download
            const r = await fetch(json.url); const b = await r.blob(); const url = URL.createObjectURL(b);
            const a = document.createElement('a'); a.href = url; a.download = json.filename || ('fdfp_' + Date.now() + '.pdf'); document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
          }
        } catch (e) {
          console.warn('Forcing download failed, opening in new tab as fallback', e);
          try { window.open(json.url, '_blank', 'noopener'); } catch (err) { const a = document.createElement('a'); a.href = json.url; a.target = '_blank'; a.rel='noopener'; a.click(); }
        }
        // prepare WhatsApp message and redirect the user to WhatsApp (wa.me)
        try {
          const phone = '2250706591243'; // destination sans '+'
          const preferred = ['company','domain','address','contact','email','type','employees','nif','rccm','private_projects'];
          const metaObj = typeof meta === 'object' ? meta : JSON.parse(meta || '{}');
          const parts = [];
          preferred.forEach(k => {
            if (metaObj[k]) parts.push(k.replace(/_/g,' ').toUpperCase() + ': ' + (Array.isArray(metaObj[k]) ? metaObj[k].join(', ') : String(metaObj[k])));
          });
          // add any other fields not in preferred order
          Object.keys(metaObj).forEach(k => { if (!preferred.includes(k) && metaObj[k]) parts.push(k.replace(/_/g,' ').toUpperCase() + ': ' + String(metaObj[k])); });
          let message = 'Nouvelle demande FDFP\n' + parts.join('\n');
          // sanitize control characters
          message = message.replace(/[\x00-\x1F\x7F]+/g, ' ');
          const waUrl = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
          // open WhatsApp in a new tab (do not replace current page)
          try { window.open(waUrl, '_blank', 'noopener'); } catch (e) { window.location.href = waUrl; return; }
          // after actions, redirect current page back to index
          setTimeout(()=> { window.location.href = 'index.php'; }, 350);
        } catch (err) {
          // if anything fails, fallback to index
          console.warn('WhatsApp redirect failed', err);
          alert('Soumission enregistrée. Le PDF est téléchargeable.');
          location.href = 'index.php';
        }
      } else {
        alert('Erreur: ' + (json.error||'Erreur serveur'));
      }
    } catch (err) {
      alert('Erreur réseau: ' + err.message);
    } finally {
      submitBtn.disabled = false; submitBtn.textContent = 'Soumettre et Télécharger (PDF)';
    }
  });
})();
</script>
</body>
</html>