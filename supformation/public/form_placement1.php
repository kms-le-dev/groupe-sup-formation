<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Formulaire Placement - Entreprise</title>
  <!-- Feuille de style dynamique: customise via query params: primary, mode, base_size, radius -->
  <link rel="stylesheet" href="assets/css/dynamic_style.php?primary=%2346b903&mode=light&base_size=16px&radius=8px">
</head>
<body>
  <main class="container">
    <h1>POUR LE PLACEMENT DE PERSONNEL - Formulaire de l'entreprise</h1>
    <form id="placementCompanyForm">
      <label>1) Nom de l'entreprise ou de l'institution<br>
        <input name="Nom de l'entreprise" required>
      </label>

      <label>2) Domaine d'activité<br>
        <input name="Domaine d_activite" required>
      </label>

      <label>3) Adresse géographique<br>
        <input name="Adresse geographique" required>
      </label>

      <label>4) Contacts<br>
        <input name="contacts" required>
      </label>

      <label>5) Email<br>
        <input name="Email" type="email">
      </label>

      <fieldset>
        <legend>6) Niveau de qualification de l'employé</legend>
        <label><input type="radio" name="Niveau de qualification" value="EMPLOYES/OUVRIERS QUALIFIES" required> EMPLOYES/ OUVRIERS QUALIFIES</label><br>
        <label><input type="radio" name="Niveau de qualification" value="AGENTS DE MAITRISE OU TECHNICIENS"> AGENTS DE MAITRISE OU TECHNICIENS</label><br>
        <label><input type="radio" name="Niveau de qualification" value="CADRES"> CADRES</label>
      </fieldset>

      <label>7) Nombre d'employé souhaité<br>
        <input name="Nombre d_employe souhaité" type="number" min="1">
      </label>

      <label>8) Numéro d'identification fiscale DFE (Facultatif)<br>
        <input name="Numero DFE">
      </label>

      <label>RCCM (Facultatif)<br>
        <input name="RCCM">
      </label>

      <div style="margin-top:1rem">
        <button type="button" id="submitCompany" class="btn">Soumettre</button>
        <a href="placement.php" class="btn">Annuler</a>
      </div>
    </form>
  </main>

  <!-- Load jsPDF from CDN as fallback if local copy missing -->
  <script>
    // dynamically insert jsPDF from CDN
    (function(){
      var s = document.createElement('script');
      s.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
      s.crossOrigin = 'anonymous';
      s.onload = function(){ console.log('jsPDF loaded'); };
      s.onerror = function(){ console.warn('Failed to load jsPDF from CDN'); };
      document.head.appendChild(s);
    })();
  </script>
  <script>
    // Robust PDF generation + submit with fallback if jsPDF missing
    (function(){
      var btn = document.getElementById('submitCompany');
      if (!btn) return;
      btn.addEventListener('click', function(){
        (async function(){
          var form = document.getElementById('placementCompanyForm');
          if (!form) return;
          try {
            btn.disabled = true;
            btn.textContent = 'Envoi en cours...';

            var data = new FormData(form);
            var entries = {};
            data.forEach(function(v,k){ entries[k]=v; });

            // try to build PDF with jsPDF; if unavailable, build a simple text blob and send it as .pdf
            var pdfBlob = null;
            try {
              var jsPDF = (window.jspdf && window.jspdf.jsPDF) ? window.jspdf.jsPDF : null;

              // wait for jsPDF to be available (in case CDN is still loading)
              var waitForJsPDF = function(timeoutMs){
                return new Promise(function(resolve, reject){
                  var start = Date.now();
                  (function check(){
                    if (window.jspdf && window.jspdf.jsPDF) return resolve(window.jspdf.jsPDF);
                    if (Date.now() - start > timeoutMs) return resolve(null);
                    setTimeout(check, 120);
                  })();
                });
              };
              var jsPDFctor = await waitForJsPDF(3000);
              if (typeof jsPDFctor === 'function') {
                const doc = new jsPDF({ unit: 'pt', format: 'a4' });

                const pageWidth = doc.internal.pageSize.getWidth();
                const pageHeight = doc.internal.pageSize.getHeight();
                const margin = 50;
                const col1Width = 200;
                const col2Width = pageWidth - margin * 2 - col1Width;
                const rowHeight = 26;
                const cellPadding = 8;
                const lineSpacing = 6;

                // prepare rows and their heights first
                const rows = Object.keys(entries).map((key) => {
                  const label = key.replace(/_/g, ' ');
                  const value = entries[key] || '';
                  const valueLines = doc.splitTextToSize(String(value), col2Width - cellPadding * 2);
                  const cellHeight = Math.max(rowHeight, valueLines.length * 14 + cellPadding);
                  return { label, valueLines, cellHeight };
                });

                // compute total table height
                const headerHeight = rowHeight;
                const tableHeight = headerHeight + rows.reduce((acc, r) => acc + r.cellHeight + lineSpacing, 0) - lineSpacing; // no extra after last

                // compute total block height (title + gap + table)
                const title = "FORMULAIRE DE PLACEMENT - ENTREPRISE";
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(18);
                const titleHeight = 22;
                const gapAfterTitle = 12;
                const totalBlockHeight = titleHeight + gapAfterTitle + tableHeight;

                // vertical centering
                let yStart = Math.max(60, (pageHeight - totalBlockHeight) / 2);

                // draw title centered
                const titleWidth = doc.getTextWidth(title);
                const titleX = (pageWidth - titleWidth) / 2;
                doc.text(title, titleX, yStart + titleHeight - 6);

                // draw table starting at
                let y = yStart + titleHeight + gapAfterTitle;
                const tableX = margin;

                // header
                doc.setFontSize(13);
                doc.setFont('helvetica', 'bold');
                doc.setFillColor(230, 230, 230);
                doc.rect(tableX, y, col1Width, headerHeight, 'F');
                doc.rect(tableX + col1Width, y, col2Width, headerHeight, 'F');
                doc.setDrawColor(100);
                doc.rect(tableX, y, col1Width, headerHeight, 'S');
                doc.rect(tableX + col1Width, y, col2Width, headerHeight, 'S');
                doc.text('Champ', tableX + cellPadding, y + 17);
                doc.text('Valeur saisie', tableX + col1Width + cellPadding, y + 17);
                y += headerHeight;

                // rows
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(12);
                doc.setDrawColor(200);
                doc.setLineWidth(0.5);
                rows.forEach(function(r, idx) {
                  doc.rect(tableX, y, col1Width, r.cellHeight, 'S');
                  doc.rect(tableX + col1Width, y, col2Width, r.cellHeight, 'S');
                  doc.text(r.label, tableX + cellPadding, y + 16);
                  doc.text(r.valueLines, tableX + col1Width + cellPadding, y + 16);
                  y += r.cellHeight + lineSpacing;
                  if (y > pageHeight - 100) { doc.addPage(); y = 80; }
                });

                // outer border
                const tableEndY = y - lineSpacing;
                doc.setDrawColor(50);
                doc.setLineWidth(2);
                doc.rect(tableX, yStart + titleHeight + gapAfterTitle, col1Width + col2Width, tableEndY - (yStart + titleHeight + gapAfterTitle), 'S');

                // signature
                doc.setFont('helvetica', 'italic');
                doc.setFontSize(12);
                doc.text('Signature et cachet de l\'entreprise :', pageWidth / 2, pageHeight - 50, { align: 'center' });

                pdfBlob = doc.output('blob');
              } else {
                console.warn('jsPDF non disponible, envoi d\'un résumé texte en fallback');
                var summary = 'Formulaire Placement - Entreprise\n\n' + Object.keys(entries).map(function(k){ return k.replace(/_/g,' ') + ': ' + (entries[k]||''); }).join('\n');
                pdfBlob = new Blob([summary], { type: 'application/pdf' });
              }
            } catch (e) {
              console.error('Erreur génération PDF', e);
              var summary2 = 'Formulaire Placement - Entreprise (fallback)\n\n' + Object.keys(entries).map(function(k){ return k.replace(/_/g,' ') + ': ' + (entries[k]||''); }).join('\n');
              pdfBlob = new Blob([summary2], { type: 'application/pdf' });
            }

            var upload = new FormData();
            upload.append('type','company');
            upload.append('meta', JSON.stringify(entries));
            upload.append('pdf', pdfBlob, 'placement_company_' + Date.now() + '.pdf');

            var res = await fetch('save_placement.php', { method:'POST', body: upload });
            var json = await res.json();
            if (json.success) {
              // trigger download
              var a = document.createElement('a');
              a.href = json.url;
              a.download = json.filename || '';
              document.body.appendChild(a);
              a.click();
              a.remove();
              if (json.whatsapp) window.open(json.whatsapp, '_blank');
              alert('Formulaire envoyé. Nous vous contacterons bientôt.');
              window.location = 'placement.php';
            } else {
              alert('Erreur: ' + (json.error || 'Impossible d\'envoyer'));
            }
          } catch (err) {
            console.error('Envoi échoué', err);
            alert('Erreur réseau ou interne: ' + (err.message || err));
          } finally {
            btn.disabled = false;
            btn.textContent = 'Soumettre';
          }
        })();
      });
    })();
  </script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>