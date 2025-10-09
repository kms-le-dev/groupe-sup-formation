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
  <title>Formulaire Placement - Chercheur d'emploi</title>
  <!-- Feuille de style dynamique: customise via query params: primary, mode, base_size, radius -->
  <link rel="stylesheet" href="assets/css/dynamic_style.php?primary=%2346b903&mode=light&base_size=16px&radius=8px">
</head>
<body>
  <main class="container">
    <h1>POUR LE PLACEMENT DE PERSONNEL - Formulaire du chercheur d'emploi</h1>
    <form id="placementJobseekForm">
      <label>Nom et prénoms<br>
        <input name="Nom et prenoms" required>
      </label>

      <label>Contacts<br>
        <input name="Contacts" required>
      </label>

      <label>Date et lieu de naissance<br>
        <input name="Date et lieu de naissance">
      </label>

      <label>Email<br>
        <input name="Email" type="email">
      </label>

      <label>Commune/ville/quartier<br>
        <input name="Commune /ville/ quartier">
      </label>

      <label>Domaine d'activité<br>
        <input name="Domaine d_activite">
      </label>

      <label>Emploi recherché<br>
        <input name="Emploi recherche">
      </label>

      <label>Importer le CV (PDF)<br>
        <input type="file" id="cvFile" accept="application/pdf">
      </label>

      <label>Importer la lettre de motivation (PDF)<br>
        <input type="file" id="coverFile" accept="application/pdf">
      </label>

      <label>Prétentions salariale<br>
        <input name="Pretentions salariale">
      </label>

      <div style="margin-top:1rem">
        <button type="button" id="submitJobseeker" class="btn">Soumettre</button>
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
    // Robust submit for jobseeker form with jsPDF fallback
    (function(){
      var btn = document.getElementById('submitJobseeker');
      if (!btn) return;
      btn.addEventListener('click', function(){
        (async function(){
          var form = document.getElementById('placementJobseekForm');
          if (!form) return;
          try {
            btn.disabled = true; btn.textContent = 'Envoi en cours...';

            var baseFD = new FormData(form);
            var entries = {};
            baseFD.forEach(function(v,k){ entries[k]=v; });

            // attach files from inputs
            var fd = new FormData();
            Object.keys(entries).forEach(function(k){ fd.append(k, entries[k]); });
            var cvInput = document.getElementById('cvFile');
            var coverInput = document.getElementById('coverFile');
            if (cvInput && cvInput.files[0]) fd.append('cv', cvInput.files[0]);
            if (coverInput && coverInput.files[0]) fd.append('cover', coverInput.files[0]);

            // build PDF or fallback text
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
                var doc = new jsPDF({ unit: 'pt', format: 'a4' });
                var pageWidth = doc.internal.pageSize.getWidth();
                var margin = 40;
                var maxWidth = pageWidth - margin * 2;
                // Center helper compatible with different jsPDF versions
                function textWidth(doc, str) {
                  if (typeof doc.getTextWidth === 'function') return doc.getTextWidth(String(str));
                  if (typeof doc.getStringUnitWidth === 'function') return doc.getStringUnitWidth(String(str)) * doc.internal.getFontSize();
                  return String(str).length * 6; // fallback approx
                }

                // Title and body: compute total height to vertically center
                doc.setFontSize(18);
                var title = "Formulaire Placement - Chercheur d'emploi";
                var titleHeight = 22; // approx

                // Body: prepare grouped lines per entry (to add gap and separator)
                doc.setFontSize(12);
                var leading = 18; // line height in points
                var entriesGrouped = []; // array of arrays (lines per entry)
                Object.keys(entries).forEach(function(k){
                  var label = k.replace(/_/g,' ');
                  var value = entries[k] || '';
                  var text = label + ': ' + value;
                  var lines = doc.splitTextToSize(String(text), maxWidth);
                  entriesGrouped.push(lines);
                });

                // compute total height: sum lines + separators/gaps between entries
                var gapBetweenEntries = 10; // pts space after each entry
                var separatorGap = 6; // extra space for separator
                var totalLines = entriesGrouped.reduce(function(acc, arr){ return acc + arr.length; }, 0);
                var totalBodyHeight = totalLines * leading + Math.max(0, entriesGrouped.length - 1) * (gapBetweenEntries + separatorGap);
                var totalHeight = titleHeight + 12 + totalBodyHeight; // 12pt gap after title
                var pageHeight = doc.internal.pageSize.getHeight();
                var yStart = Math.max(60, (pageHeight - totalHeight) / 2);

                // Build table rows
                const rows = Object.keys(entries).map((key) => {
                  const label = key.replace(/_/g, ' ');
                  const value = entries[key] || '';
                  const valueLines = doc.splitTextToSize(String(value), maxWidth - 8);
                  const cellHeight = Math.max(26, valueLines.length * 14 + 8);
                  return { label, valueLines, cellHeight };
                });

                // table layout
                const headerHeight = 26;
                const tableHeight = headerHeight + rows.reduce((acc, r) => acc + r.cellHeight + 6, 0) - 6;
                const titleHeightPx = 22;
                const gapAfterTitle = 12;
                const totalBlockHeight = titleHeightPx + gapAfterTitle + tableHeight;
                const yStartTable = Math.max(60, (pageHeight - totalBlockHeight) / 2);

                // title (set font & size first so measured width matches rendered width)
                doc.setFontSize(18);
                doc.setFont('helvetica', 'bold');
                const twTitle = textWidth(doc, title);
                doc.text(title, (pageWidth - twTitle) / 2, yStartTable + titleHeightPx - 6);

                // draw table
                let y = yStartTable + titleHeightPx + gapAfterTitle;
                const tableX = margin;
                const col1W = Math.floor((pageWidth - margin * 2) * 0.35);
                const col2W = (pageWidth - margin * 2) - col1W - 12;

                // header
                doc.setFontSize(13);
                doc.setFont('helvetica', 'bold');
                doc.setFillColor(230,230,230);
                doc.rect(tableX, y, col1W, headerHeight, 'F');
                doc.rect(tableX + col1W, y, col2W, headerHeight, 'F');
                doc.setDrawColor(100);
                doc.rect(tableX, y, col1W, headerHeight, 'S');
                doc.rect(tableX + col1W, y, col2W, headerHeight, 'S');
                doc.text('Champ', tableX + 6, y + 18);
                doc.text('Valeur saisie', tableX + col1W + 6, y + 18);
                y += headerHeight;

                // rows
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(12);
                doc.setDrawColor(200);
                doc.setLineWidth(0.5);
                rows.forEach(function(r){
                  doc.rect(tableX, y, col1W, r.cellHeight, 'S');
                  doc.rect(tableX + col1W, y, col2W, r.cellHeight, 'S');
                  doc.text(r.label, tableX + 6, y + 16);
                  doc.text(r.valueLines, tableX + col1W + 6, y + 16);
                  y += r.cellHeight + 6;
                  if (y > pageHeight - 100) { doc.addPage(); y = 80; }
                });

                // outer border
                doc.setDrawColor(50);
                doc.setLineWidth(2);
                doc.rect(tableX, yStartTable + titleHeightPx + gapAfterTitle, (col1W + col2W), y - (yStartTable + titleHeightPx + gapAfterTitle) - 6, 'S');

                pdfBlob = doc.output('blob');
              } else {
                var summary = "Formulaire Placement - Chercheur d'emploi\n\n" + Object.keys(entries).map(function(k){ return k.replace(/_/g,' ') + ': ' + (entries[k]||''); }).join('\n');
                pdfBlob = new Blob([summary], { type: 'application/pdf' });
              }
            } catch (e) {
              console.error('PDF generation failed', e);
              var summary2 = "Formulaire Placement - Chercheur d'emploi (fallback)\n\n" + Object.keys(entries).map(function(k){ return k.replace(/_/g,' ') + ': ' + (entries[k]||''); }).join('\n');
              pdfBlob = new Blob([summary2], { type: 'application/pdf' });
            }

            fd.append('type','jobseeker');
            fd.append('meta', JSON.stringify(entries));
            fd.append('pdf', pdfBlob, 'placement_jobseeker_' + Date.now() + '.pdf');

            var res = await fetch('save_placement.php', { method:'POST', body: fd });
            var json = await res.json();
            if (json.success) {
              var a = document.createElement('a'); a.href = json.url; a.download = json.filename || ''; document.body.appendChild(a); a.click(); a.remove();
              if (json.whatsapp) window.open(json.whatsapp, '_blank');
              alert('Formulaire envoyé. Bonne chance!');
              window.location = 'placement.php';
            } else {
              alert('Erreur: ' + (json.error || 'Impossible d\'envoyer'));
            }
          } catch (err) {
            console.error('Submit failed', err);
            alert('Erreur réseau ou interne: ' + (err.message || err));
          } finally {
            btn.disabled = false; btn.textContent = 'Soumettre';
          }
        })();
      });
    })();
  </script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>