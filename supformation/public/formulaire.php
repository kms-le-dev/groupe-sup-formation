<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';


// formulaire.php
// Remplace les valeurs ci-dessous par celles de ton projet
$admin_email = "admin@moncentre.com"; // email qui recevra le PDF
// URL PayDunya de checkout (à remplacer par la vraie URL/endpoint de PayDunya)
$paydunya_checkout_url = "https://paydunya.com/pay/TON_CHECKOUT_ID"; 
?>
<!doctype html>
<html lang="fr">


<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Fiche d'inscription</title>

  <!-- CSS -->
  <link rel="stylesheet" href="assets/css/styles.css" />

  <!-- CORRECTION: html2pdf avec TOUTES ses dépendances -->
  <!-- Option 1: Version complète avec SRI (recommandée) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" 
          integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" 
          crossorigin="anonymous" 
          referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<style>
    /* formulaire.php  */

/* style.css */

/* Colors - adapte pour matcher index.php */
:root{
  --primary: #2c7be5;   /* changer selon index.php */
  --accent: #ffb703;
  --bg: #f6f9fc;
  --card: #ffffff;
  --text: #1f2937;
}

*{box-sizing:border-box}
body{
  font-family: "Helvetica Neue", Arial, sans-serif;
  margin:0;
  background:var(--bg);
  color:var(--text);
  -webkit-font-smoothing:antialiased;
}

.container{
  max-width:980px;
  margin:28px auto;
  padding:20px;
}

h1{
  text-align:center;
  font-size:1.4rem;
  margin-bottom:18px;
  color: #05660dff;
}

.card{
  background:var(--card);
  padding:16px;
  border-radius:12px;
  box-shadow:0 6px 18px rgba(16,24,40,0.06);
  margin-bottom:16px;
  display:block;
}

.card h2{
  margin-top:0;
  font-size:1.05rem;
  color:var(--primary);
}

label{
  display:block;
  margin-bottom:10px;
  font-size:0.95rem;
}

label input[type="text"],
label input[type="email"],
label input[type="tel"],
label input[type="date"],
label select,
label input[type="file"],
label input[type="number"]{
  display:block;
  width:100%;
  padding:10px 12px;
  border-radius:8px;
  border:1px solid #e6e9ef;
  margin-top:6px;
  font-size:0.95rem;
}

.checkbox-group{
  margin:10px 0;
  display:flex;
  flex-wrap:wrap;
  gap:10px;
}

.checkbox-group label{
  display:flex;
  align-items:center;
  gap:8px;
  background:#fafafa;
  padding:8px 10px;
  border-radius:8px;
  border:1px solid #eee;
  font-size:0.9rem;
}

.actions{
  display:flex;
  gap:12px;
  justify-content:flex-end;
  margin-top:10px;
}

.btn{
  padding:10px 14px;
  border-radius:10px;
  border:none;
  background:#e6eefc;
  cursor:pointer;
  font-weight:600;
}

.btn2{
  padding:10px 14px;
  border-radius:10px;
  border:none;
  background: #e03c3cff;
  cursor:pointer;
  font-weight:600;
  color: white;
}

.btn2:hover {
    transform: scale(1.08);
}

.btn.primary{
  background:var(--primary);
  color:white;
}

.photo-preview{
  margin-top:8px;
  width:90px;
  height:90px;
  border-radius:50%;
  overflow:hidden;
  display:flex;
  align-items:center;
  justify-content:center;
  background:#f0f2f7;
  border:1px dashed #dde6f2;
  font-size:0.8rem;
}

/* pdf-content formatting (used when exported) */
.pdf-content{
  width:800px;
  padding:20px;
  font-family:Arial, sans-serif;
  color:#111;
  background:white;
}

/* Responsive */
@media (max-width:700px){
  .actions{ flex-direction:column; align-items:stretch; }
  .container{ padding:12px; }
  .actions .btn2 {
    margin-left: auto;
    margin-right: auto;
    display: block;
    width: fit-content;
    text-align: center;
  }
}


</style>

<body>
  <?php include 'loader.php'; ?>  
  <main class="container">
    <h1>FICHE D’INSCRIPTION — INFORMATIONS PERSONNELLES ET ACADÉMIQUES</h1>

    <form id="inscriptionForm" enctype="multipart/form-data">
      <section class="card">
        <h2>Informations personnelles</h2>
        <label>Nom et prénom
          <input type="text" name="nom_prenom" required>
        </label>

        <label>Date de naissance
          <input type="date" name="date_naissance" required>
        </label>

        <label>Lieu de naissance
          <input type="text" name="lieu_naissance">
        </label>

        <label>N° téléphone
          <input type="tel" name="telephone" required>
        </label>

        <label>Email (obligatoire)
          <input type="email" name="email" required>
        </label>

        <label>Niveau d'étude actuel
          <input type="text" name="niveau_actuel">
        </label>

        <label>Lieu de résidence
          <input type="text" name="lieu_residence">
        </label>
      </section>

      <section class="card">
        <h2>Formation demandée</h2>
        <label>Formation demandée (DUT, BTS, Licence, Master etc...)
          <input type="text" name="formation_demandee">
        </label>

        <label>Niveau demandé (bac + 1, bac + 2 etc...)
          <input type="text" name="niveau_demandee">
        </label>

        <label>Filière demandée (le nom de la filière)
          <input type="text" name="filiere_demandee">
        </label>

        <div class="checkbox-group">
          <p>Type de cours :</p>
          <label><input type="checkbox" name="type_cours[]" value="Jour"> Cours du jour</label>
          <label><input type="checkbox" name="type_cours[]" value="Soir"> Cours du soir</label>
          <label><input type="checkbox" name="type_cours[]" value="En ligne"> Cours en ligne</label>
          <label><input type="checkbox" name="type_cours[]" value="Autre"> Autre (préciser)
            <input type="text" name="type_cours_autre" placeholder="Préciser si autre">
          </label>
        </div>
      </section>

      <section class="card">
        <h2>Personne à contacter en cas d'urgence</h2>
        <div class="checkbox-group">
          <label><input type="radio" name="contact_urgence_type" value="PERE" required> Père</label>
          <label><input type="radio" name="contact_urgence_type" value="MERE"> Mère</label>
          <label><input type="radio" name="contact_urgence_type" value="TUTEUR"> Tuteur</label>
          <label><input type="radio" name="contact_urgence_type" value="EPOUX"> Époux(se)</label>
          <label><input type="radio" name="contact_urgence_type" value="AUTRE"> Autre
            <input type="text" name="contact_urgence_autre" placeholder="Préciser si autre">
          </label>
        </div>

        <label>Nom et prénom (contact urgence)
          <input type="text" name="contact_nom_prenom" required>
        </label>

        <label>Lieu de résidence (contact urgence)
          <input type="text" name="contact_lieu">
        </label>

        <label>Contact (téléphone)
          <input type="tel" name="contact_telephone" required>
        </label>
      </section>

      <section class="card">
        <h2>Paiement & photo</h2>

        <label>Mode de paiement
          <select name="mode_paiement" required>
            <option value="">-- Choisir --</option>
            <option value="wave">Wave</option>
            <option value="mtn">MTN Money</option>
            <option value="orange">Orange Money</option>
          </select>
        </label>

        <label>Numéro de paiement
          <input type="text" name="num_paiement" required>
        </label>

        <label>Photo en fond blanc (JPEG/PNG) :
          <input type="file" name="photo" id="photoInput" accept="image/*" >
        </label>
        <div id="photoPreview" class="photo-preview">Aucune photo</div>
      </section>

      <div class="actions">
        <a href="index.php" class="btn2" style="text-decoration:none;display:inline-block;line-height:28px;padding:8px 12px;">Retour à l'accueil</a>
       <button type="button" id="previewPdfBtn" class="btn">Télécharger le PDF (obligatoire)</button>
       <button type="button" id="paySendBtn" class="btn primary">Suivant</button>
      </div>
    </form>

    <!-- Zone invisible qui sera convertie en PDF (on y clone les données) -->
    <div id="pdfContent" class="pdf-content" style="display:none;"></div>

    <!-- Message pour le paiement différé -->

    <div class="card payment-later-message">
      <p class="alert-payment" style="text-align: center; margin-bottom: 20px;">
        Si vous voulez effectuer le paiement plus tard, téléchargez le PDF et gardez-le précieusement.<br>
        Lorsque vous serez prêt, revenez ici et cliquez sur le bouton ci-dessous.
      </p>
      <div style="text-align: center;">
        <a href="inscription.php" class="btn primary" style="text-decoration: none;">
          Passer directement au paiement
        </a>
      </div>
    </div>

    <style>
      .alert-payment {
        color: #e03c3c;
        font-weight: bold;
        font-size: 1.08rem;
        background: linear-gradient(90deg, #ffeaea 60%, #fff6f6 100%);
        border-radius: 8px;
        padding: 12px 8px;
        box-shadow: 0 2px 12px rgba(224,60,60,0.08);
        animation: alertFadeIn 1.2s cubic-bezier(.4,0,.2,1);
      }
      @keyframes alertFadeIn {
        0% { opacity: 0; transform: scale(0.95) translateY(18px); }
        60% { opacity: 1; transform: scale(1.04) translateY(-4px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
      }
    </style>

  </main>


 <script>
  const PAYDUNYA_CHECKOUT_URL = "<?php echo $paydunya_checkout_url; ?>";
 </script>


<script>
document.addEventListener('DOMContentLoaded', () => {
  console.log('=== DIAGNOSTIC DES ÉLÉMENTS ===');
  console.log('Form:', document.getElementById('inscriptionForm'));
  console.log('PhotoInput:', document.getElementById('photoInput'));
  console.log('PhotoPreview:', document.getElementById('photoPreview'));
  console.log('PreviewBtn:', document.getElementById('previewPdfBtn'));
  console.log('PayBtn:', document.getElementById('paySendBtn'));
});
</script>


<script>
  document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('inscriptionForm');
  const photoInput = document.getElementById('photoInput');
  const photoPreview = document.getElementById('photoPreview');
  const previewPdfBtn = document.getElementById('previewPdfBtn');
  const paySendBtn = document.getElementById('paySendBtn');

  let lastPhotoDataUrl = null;

  if (!form || !previewPdfBtn || !paySendBtn) {
    console.error('❌ Éléments manquants');
    return;
  }

  // Preview photo
  if (photoInput && photoPreview) {
    photoInput.addEventListener('change', () => {
      const file = photoInput.files[0];
      if (!file) {
        photoPreview.innerHTML = 'Aucune photo';
        lastPhotoDataUrl = null;
        return;
      }
      const reader = new FileReader();
      reader.onload = e => {
        lastPhotoDataUrl = e.target.result;
        photoPreview.innerHTML = '';
        const img = document.createElement('img');
        img.src = lastPhotoDataUrl;
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        photoPreview.appendChild(img);
      };
      reader.readAsDataURL(file);
    });
  }

  // Générer PDF avec jsPDF direct (NOUVELLE MÉTHODE)
  async function generatePdfDirect() {
    const fd = new FormData(form);
    
    // Accéder à jsPDF depuis html2pdf
    const { jsPDF } = window.jspdf || window;
    
    if (!jsPDF) {
      // Fallback: utiliser html2pdf qui contient jsPDF
      console.log('Utilisation de html2pdf.jsPDF');
      return await generatePdfWithHtml2Canvas();
    }
    
    const doc = new jsPDF({
      orientation: 'portrait',
      unit: 'mm',
      format: 'a4'
    });
    
    let y = 20;
    const leftMargin = 20;
    const rightMargin = 190;
    
    // Titre
    doc.setFontSize(18);
    doc.setTextColor(44, 123, 229);
    doc.text('FICHE D\'INSCRIPTION', leftMargin, y);
    y += 10;
    
    doc.setFontSize(10);
    doc.setTextColor(100, 100, 100);
    doc.text('Informations personnelles et académiques', leftMargin, y);
    y += 15;
    
    // Photo (si disponible)
    if (lastPhotoDataUrl) {
      try {
        doc.addImage(lastPhotoDataUrl, 'JPEG', rightMargin - 30, 15, 30, 30);
      } catch (e) {
        console.warn('Erreur ajout photo:', e);
      }
    }
    
    // Section: Informations personnelles
    doc.setFontSize(14);
    doc.setTextColor(44, 123, 229);
    doc.text('Informations personnelles', leftMargin, y);
    y += 8;
    
    doc.setFontSize(10);
    doc.setTextColor(0, 0, 0);
    
    const addField = (label, value) => {
      doc.setFont(undefined, 'bold');
      doc.text(label + ':', leftMargin, y);
      doc.setFont(undefined, 'normal');
      doc.text(value || 'Non renseigné', leftMargin + 50, y);
      y += 6;
    };
    
    addField('Nom et prénom', fd.get('nom_prenom'));
    addField('Date de naissance', fd.get('date_naissance'));
    addField('Lieu de naissance', fd.get('lieu_naissance'));
    addField('Téléphone', fd.get('telephone'));
    addField('Email', fd.get('email'));
    addField('Niveau d\'étude', fd.get('niveau_actuel'));
    addField('Lieu de résidence', fd.get('lieu_residence'));
    
    y += 5;
    
    // Section: Formation demandée
    doc.setFontSize(14);
    doc.setTextColor(44, 123, 229);
    doc.text('Formation demandée', leftMargin, y);
    y += 8;
    
    doc.setFontSize(10);
    doc.setTextColor(0, 0, 0);
    
    addField('Formation', fd.get('formation_demandee'));
    addField('Niveau demandé', fd.get('niveau_demandee'));
    addField('Filière', fd.get('filiere_demandee'));
    
    const typeCours = fd.getAll('type_cours[]').join(', ') || 'Non spécifié';
    const typeCoursAutre = fd.get('type_cours_autre') || '';
    addField('Type de cours', typeCours + ' ' + typeCoursAutre);
    
    y += 5;
    
    // Section: Contact d'urgence
    doc.setFontSize(14);
    doc.setTextColor(44, 123, 229);
    doc.text('Contact d\'urgence', leftMargin, y);
    y += 8;
    
    doc.setFontSize(10);
    doc.setTextColor(0, 0, 0);
    
    const contactType = fd.get('contact_urgence_type') || '';
    const contactAutre = fd.get('contact_urgence_autre') || '';
    addField('Relation', contactType + ' ' + contactAutre);
    addField('Nom et prénom', fd.get('contact_nom_prenom'));
    addField('Lieu de résidence', fd.get('contact_lieu'));
    addField('Téléphone', fd.get('contact_telephone'));
    
    y += 5;
    
    // Section: Paiement
    doc.setFontSize(14);
    doc.setTextColor(44, 123, 229);
    doc.text('Informations de paiement', leftMargin, y);
    y += 8;
    
    doc.setFontSize(10);
    doc.setTextColor(0, 0, 0);
    
    addField('Mode de paiement', fd.get('mode_paiement'));
    addField('Numéro de paiement', fd.get('num_paiement'));
    
    // Footer
    y = 280;
    doc.setFontSize(8);
    doc.setTextColor(150, 150, 150);
    doc.text('Formulaire généré le ' + new Date().toLocaleString('fr-FR'), leftMargin, y);
    
    return doc;
  }
  
  // Méthode de secours avec html2canvas
  async function generatePdfWithHtml2Canvas() {
    const fd = new FormData(form);
    const html = document.createElement('div');
    
    html.style.cssText = `
      width: 700px;
      padding: 30px;
      background-color: white;
      font-family: Arial, sans-serif;
      color: #333;
      line-height: 1.6;
    `;
    
    html.innerHTML = `
      <h1 style="color: #2c7be5; font-size: 24px; margin: 0 0 10px 0;">FICHE D'INSCRIPTION</h1>
      <p style="font-size: 12px; color: #666; margin: 0 0 20px 0;">Informations personnelles et académiques</p>
      
      <h2 style="color: #2c7be5; font-size: 16px; margin: 20px 0 10px 0;">Informations personnelles</h2>
      <p style="font-size: 13px; line-height: 1.8;">
        <strong>Nom et prénom:</strong> ${fd.get('nom_prenom') || ''}<br>
        <strong>Date de naissance:</strong> ${fd.get('date_naissance') || ''}<br>
        <strong>Lieu de naissance:</strong> ${fd.get('lieu_naissance') || ''}<br>
        <strong>Téléphone:</strong> ${fd.get('telephone') || ''}<br>
        <strong>Email:</strong> ${fd.get('email') || ''}<br>
        <strong>Niveau d'étude:</strong> ${fd.get('niveau_actuel') || ''}<br>
        <strong>Lieu de résidence:</strong> ${fd.get('lieu_residence') || ''}
      </p>
      
      <h2 style="color: #2c7be5; font-size: 16px; margin: 20px 0 10px 0;">Formation demandée</h2>
      <p style="font-size: 13px; line-height: 1.8;">
        <strong>Formation:</strong> ${fd.get('formation_demandee') || ''}<br>
        <strong>Niveau:</strong> ${fd.get('niveau_demandee') || ''}<br>
        <strong>Filière:</strong> ${fd.get('filiere_demandee') || ''}<br>
        <strong>Type de cours:</strong> ${fd.getAll('type_cours[]').join(', ')}
      </p>
      
      <h2 style="color: #2c7be5; font-size: 16px; margin: 20px 0 10px 0;">Contact d'urgence</h2>
      <p style="font-size: 13px; line-height: 1.8;">
        <strong>Relation:</strong> ${fd.get('contact_urgence_type') || ''}<br>
        <strong>Nom:</strong> ${fd.get('contact_nom_prenom') || ''}<br>
        <strong>Lieu:</strong> ${fd.get('contact_lieu') || ''}<br>
        <strong>Téléphone:</strong> ${fd.get('contact_telephone') || ''}
      </p>
      
      <h2 style="color: #2c7be5; font-size: 16px; margin: 20px 0 10px 0;">Paiement</h2>
      <p style="font-size: 13px; line-height: 1.8;">
        <strong>Mode:</strong> ${fd.get('mode_paiement') || ''}<br>
        <strong>Numéro:</strong> ${fd.get('num_paiement') || ''}
      </p>
      
      <p style="margin-top: 40px; font-size: 11px; color: #999; text-align: center;">
        Formulaire généré le ${new Date().toLocaleString('fr-FR')}
      </p>
    `;
    
    document.body.appendChild(html);
    html.style.position = 'fixed';
    html.style.top = '50px';
    html.style.left = '50px';
    
    await new Promise(r => setTimeout(r, 200));
    
    const opt = {
      margin: 10,
      filename: 'fiche_inscription.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2, useCORS: true, logging: true, backgroundColor: '#ffffff' },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    const pdf = await html2pdf().set(opt).from(html).toPdf().get('pdf');
    document.body.removeChild(html);
    return pdf;
  }

  // Preview PDF
  previewPdfBtn.addEventListener('click', async () => {
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    try {
      console.log('📝 Génération du PDF avec jsPDF direct...');
      
      const doc = await generatePdfDirect();
      const pdfBlob = doc.output('blob');
      
      console.log('✅ PDF généré, taille:', pdfBlob.size, 'bytes');
      
      if (pdfBlob.size < 5000) {
        console.warn('⚠️ PDF petit, essai méthode alternative...');
        const altDoc = await generatePdfWithHtml2Canvas();
        const altBlob = altDoc.output('blob');
        console.log('✅ PDF alternatif généré, taille:', altBlob.size, 'bytes');
        downloadPdf(altBlob);
      } else {
        downloadPdf(pdfBlob);
      }
      
    } catch (err) {
      console.error('❌ Erreur:', err);
      alert('Erreur: ' + err.message);
    }
  });
  
  function downloadPdf(blob) {
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'fiche_inscription_' + Date.now() + '.pdf';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    alert('PDF téléchargé ! Vérifiez votre dossier de téléchargements.');
    
    setTimeout(() => {
      window.open(url, '_blank');
    }, 500);
    
    setTimeout(() => URL.revokeObjectURL(url), 60000);
  }

  // Rediriger vers la page d'inscription (inscription.php) quand on clique sur Suivant
  paySendBtn.addEventListener('click', (e) => {
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    // Si vous voulez transférer les données du formulaire automatiquement vers inscription.php,
    // on peut poster via un formulaire caché. Pour l'instant on redirige simplement.
    window.location.href = 'inscription.php';
  });

});
</script>



</body>
</html>
