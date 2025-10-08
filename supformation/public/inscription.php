<?php
require_once __DIR__ . '/../includes/config.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // sanitize inputs
    $affecte = isset($_POST['affecte']) && $_POST['affecte'] === '1' ? 'oui' : 'non';
    $cycle = $_POST['cycle'] ?? '';
    $mode_paiement = $_POST['mode_paiement'] ?? '';
    $num_paiement = trim($_POST['num_paiement'] ?? '');

    // require file
    if (!isset($_FILES['fiche_pdf']) || $_FILES['fiche_pdf']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Veuillez téléverser le fichier PDF.';
    }

    if (empty($errors)) {
        // Calcul montant
        $montants = [
            'bts1' => ['oui' => 70000, 'non' => 80000],
            'bts2' => ['oui' => 70000, 'non' => 80000],
            'lic3' => ['oui' => 100000, 'non' => 100000],
            'm1' => ['oui' => 150000, 'non' => 150000],
            'm2' => ['oui' => 150000, 'non' => 150000],
        ];
        $key = $cycle;
        $montant = $montants[$key][$affecte] ?? 0;

    // Ensure table exists (helps when install_db.php wasn't executed)
    $pdo->exec("CREATE TABLE IF NOT EXISTS inscriptions (
      id INT AUTO_INCREMENT PRIMARY KEY,
      cycle VARCHAR(50) NOT NULL,
      affecte ENUM('oui','non') NOT NULL,
      mode_paiement VARCHAR(50),
      num_paiement VARCHAR(100),
      pdf_filename VARCHAR(255) NOT NULL,
      montant INT NOT NULL DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;");

    // Save file securely (uses helper safe_upload)
    require_once __DIR__ . '/../includes/functions.php';
        $upload = safe_upload($_FILES['fiche_pdf']);
        if (isset($upload['error'])) {
            $errors[] = $upload['error'];
        } else {
            $storedFilename = $upload['filename'];
            // Insert into DB
            $stmt = $pdo->prepare('INSERT INTO inscriptions (cycle, affecte, mode_paiement, num_paiement, pdf_filename, montant) VALUES (:cycle, :affecte, :mode, :num, :pdf, :montant)');
            $ok = $stmt->execute([
                ':cycle' => $cycle,
                ':affecte' => $affecte,
                ':mode' => $mode_paiement,
                ':num' => $num_paiement,
                ':pdf' => $storedFilename,
                ':montant' => $montant
            ]);

            if ($ok) {
                // Redirect to PayDunya
                $paydunya = '';
                if (defined('PAYDUNYA_CHECKOUT_URL')) {
                    $paydunya = constant('PAYDUNYA_CHECKOUT_URL');
                } elseif (isset($paydunya_checkout_url) && $paydunya_checkout_url) {
                    $paydunya = $paydunya_checkout_url;
                }
                if ($paydunya) {
                    header('Location: ' . $paydunya);
                    exit;
                } else {
                    $success = 'Inscription enregistrée. URL PayDunya non configurée.';
                }
            } else {
                $errors[] = 'Erreur lors de l\'enregistrement en base.';
            }
        }
    }
}

?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Inscription - Upload fiche</title>
  <style>
    :root{--primary:#2c7be5; --accent:#ffb703; --bg:#f6f9fc; --card:#fff}
    body{font-family:Arial,Helvetica,sans-serif;background:linear-gradient(180deg,#f6f9fc, #eef6ff);margin:0;padding:22px;color:#111}
    .wrapper{max-width:720px;margin:18px auto;background:var(--card);padding:20px;border-radius:12px;box-shadow:0 8px 30px rgba(16,24,40,0.06)}
    h1{color:var(--primary);margin:0 0 12px;font-size:1.25rem}
    label{display:block;margin:10px 0;font-weight:600}
    input[type=file]{display:block}
    .row{display:flex;gap:12px;align-items:center}
    .small{font-size:0.9rem;color:#555}
    .montant{margin-top:12px;padding:12px;background:#f8fbff;border:1px solid #e6f0ff;border-radius:8px;font-weight:700}
    .actions{display:flex;justify-content:space-between;gap:8px;margin-top:14px}
    .btn{background:var(--primary);color:#fff;padding:10px 14px;border-radius:8px;border:none;cursor:pointer}
    select,input[type=text]{padding:8px;border-radius:8px;border:1px solid #ddd}
    .muted{color:#666;font-size:0.9rem}
    .notice{padding:10px;background:#fff8e6;border:1px solid #ffecb5;border-radius:8px}
  </style>
</head>
<body>
  <div class="wrapper">
    <h1>Inscription - Téléversement fiche</h1>
    <?php if(!empty($errors)): ?>
      <div class="notice">
        <strong>Erreurs :</strong>
        <ul>
          <?php foreach($errors as $e): ?>
            <li><?php echo s($e); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if(!empty($success)): ?>
      <div class="notice">
        <?php echo s($success); ?>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" id="inscForm">
      <label>Fiche d'inscription (PDF)
        <input type="file" name="fiche_pdf" accept="application/pdf" required>
      </label>

      <label>Affecté ?
        <div class="row">
          <label><input type="radio" name="affecte" value="1" checked> Oui</label>
          <label><input type="radio" name="affecte" value="0"> Non</label>
        </div>
      </label>

      <label>Cycle
        <select name="cycle" id="cycleSelect" required>
          <option value="bts1">BTS1 / Licence1</option>
          <option value="bts2">BTS2 / Licence2</option>
          <option value="lic3">Licence 3</option>
          <option value="m1">Master 1</option>
          <option value="m2">Master 2</option>
        </select>
      </label>

      <label>Mode de paiement
        <select name="mode_paiement">
          <option value="wave">Wave</option>
          <option value="mtn">MTN Money</option>
          <option value="orange">Orange Money</option>
        </select>
      </label>

      <label>Numéro de paiement
        <input type="text" name="num_paiement" placeholder="Ex: 77xxxxxxx">
      </label>

      <div class="montant" id="montantBox">Montant: <span id="montantVal">0</span> FCFA</div>

      <div class="actions">
        <button type="button" class="btn" id="calcBtn">Calculer montant</button>
        <button type="submit" class="btn">Passer au paiement</button>
      </div>
    </form>
  </div>

  <script>
    // client-side montant logic
    const montants = {
      bts1: {oui:70000, non:80000},
      bts2: {oui:70000, non:80000},
      lic3: {oui:100000, non:100000},
      m1: {oui:150000, non:150000},
      m2: {oui:150000, non:150000}
    };
    const sel = document.getElementById('cycleSelect');
    const montantVal = document.getElementById('montantVal');
    const calcBtn = document.getElementById('calcBtn');

    function compute(){
      const cycle = sel.value;
      const aff = document.querySelector('input[name="affecte"]:checked').value === '1' ? 'oui' : 'non';
      const val = montants[cycle] ? montants[cycle][aff] : 0;
      montantVal.textContent = val.toLocaleString('fr-FR');
    }
    calcBtn.addEventListener('click', compute);
    sel.addEventListener('change', compute);
    document.querySelectorAll('input[name="affecte"]').forEach(i=>i.addEventListener('change', compute));
    // compute initial
    compute();
  </script>
</body>
</html>
