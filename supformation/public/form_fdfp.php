<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
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
<?php include __DIR__ . '/../includes/header.php'; ?>
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
        <select name="type" required>
          <option>INSTITUTION PUBLIQUE</option>
          <option>ENTREPRISE PRIVÉE</option>
          <option>Plan de formation</option>
          <option>Projet d'apprentissage</option>
          <option>projet d'insertion</option>
          <option>Projet collectif</option>
        </select>
      </label>
      <label>7) Nombre d'employé<input name="employees" type="number" min="0" required></label>
      <label>8) NIF / DFE (facultatif)<input name="nif"></label>
      <label>RCCM (facultatif)<input name="rccm"></label>

      <div class="actions">
        <button type="button" id="cancelBtn">Annuler</button>
        <button type="submit" id="submitBtn">Soumettre et Télécharger (PDF)</button>
      </div>
    </form>
  </div>
</div>
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
  cancel.addEventListener('click', ()=> location.href = 'index.php');

  form.addEventListener('submit', async function(e){
    e.preventDefault();
    submitBtn.disabled = true; submitBtn.textContent = 'Envoi...';
    const meta = {};
    for (const p of new FormData(form).entries()) meta[p[0]] = p[1];

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
        const entries = Object.entries(meta).map(([k,v]) => ({k: k.replace(/_/g,' ').toUpperCase(), v: String(v)}));
        const rows = Math.ceil(entries.length / 2);
        const margin = 50;
        const colGap = 20;
        const colWidth = (pageW - margin * 2 - colGap) / 2;
        const rowHeight = 22;
        const tableHeight = rows * rowHeight;
        // compute start positions to vertically center table
        const title = 'Demande de formation';
        doc.setFontSize(18); doc.setFont(undefined,'bold');
        const titleW = doc.getTextWidth ? doc.getTextWidth(title) : title.length * 6;
        const startY = Math.max(80, (pageH - tableHeight) / 2 + 10);
        // draw title
        doc.text(title, (pageW - titleW) / 2, startY - 18);
        doc.setLineWidth(0.8);
        doc.setDrawColor(100);
        // draw table outer rect
        const tableX = margin;
        const tableW = pageW - margin * 2;
        doc.rect(tableX, startY - 6, tableW, tableHeight + 6);
        // draw horizontal lines
        for (let i = 0; i <= rows; i++) {
          const y = startY + i * rowHeight;
          doc.line(tableX, y, tableX + tableW, y);
        }
        // draw vertical mid line
        const midX = tableX + colWidth + colGap / 2;
        doc.line(midX, startY - 6, midX, startY + tableHeight + 6);
        // render cells text
        const leftX = tableX + 6;
        const rightX = midX + 6;
        doc.setFontSize(12); doc.setFont(undefined,'normal');
        for (let r = 0; r < rows; r++) {
          const li = r * 2;
          const left = entries[li];
          const right = entries[li + 1];
          const cellY = startY + r * rowHeight + 14; // baseline
          if (left) {
            const text = left.k + ': ' + left.v;
            const lines = doc.splitTextToSize(text, colWidth - 12);
            doc.text(lines, leftX, cellY);
          }
          if (right) {
            const text = right.k + ': ' + right.v;
            const lines = doc.splitTextToSize(text, colWidth - 12);
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
      const res = await fetch('save_fdfp.php', { method:'POST', body: out });
      const json = await res.json();
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
          const phone = '2250505051570'; // destination sans '+'
          const preferred = ['company','domain','address','contact','email','type','employees','nif','rccm'];
          const metaObj = typeof meta === 'object' ? meta : JSON.parse(meta || '{}');
          const parts = [];
          preferred.forEach(k => {
            if (metaObj[k]) parts.push(k.replace(/_/g,' ').toUpperCase() + ': ' + String(metaObj[k]));
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