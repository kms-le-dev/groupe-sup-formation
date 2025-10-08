<?php
// admin/inscriptions.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if(!is_admin()){ header('HTTP/1.1 403 Forbidden'); echo 'Accès refusé.'; exit; }

// Handle deletion POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $delId = (int)$_POST['delete_id'];
  $token = $_POST['csrf_token'] ?? '';
  if (!verify_csrf_token($token)) {
    http_response_code(400);
    echo 'Jeton CSRF invalide.'; exit;
  }

  // fetch row
  $stmt = $pdo->prepare('SELECT pdf_filename FROM inscriptions WHERE id = :id LIMIT 1');
  $stmt->execute([':id' => $delId]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    // delete file if exists
    if (!empty($row['pdf_filename'])) {
      $fp = __DIR__ . '/../public/uploads/' . $row['pdf_filename'];
      if (file_exists($fp)) @unlink($fp);
    }
    // delete DB row
    $del = $pdo->prepare('DELETE FROM inscriptions WHERE id = :id');
    $del->execute([':id' => $delId]);
  }

  // Redirect to dashboard after deletion. If headers already sent (file included after output), use JS fallback.
  $redirectUrl = 'dashboard.php';
  if (!headers_sent()) {
    header('Location: ' . $redirectUrl);
    exit;
  } else {
    echo '<script>window.location.href = "' . htmlspecialchars($redirectUrl, ENT_QUOTES) . '";</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($redirectUrl, ENT_QUOTES) . '"></noscript>';
    exit;
  }
}

$ins = $pdo->query('SELECT * FROM inscriptions ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Inscriptions</title>
  <link rel="stylesheet" href="../public/assets/css/styles.css">
  <style>table{width:100%;border-collapse:collapse}th,td{padding:8px;border:1px solid #eee}</style>
</head>
<body>

<main style="max-width:980px;margin:18px auto;padding:20px;background:#fff;border-radius:8px;">
  <h1>Inscriptions</h1>
  <?php if(!$ins): ?>
    <p>Aucune inscription trouvée.</p>
  <?php else: ?>
    <table>
      <thead><tr><th>Action</th><th>ID</th><th>Cycle</th><th>Affecté</th><th>Montant</th><th>Date</th><th>Fichier</th></tr></thead>
      <tbody>
      <?php foreach($ins as $i): ?>
        <tr>
          <td style="width:140px;">
            <form method="post" onsubmit="return confirm('Confirmer la suppression de cette inscription ?');">
              <input type="hidden" name="delete_id" value="<?php echo (int)$i['id']; ?>">
              <input type="hidden" name="csrf_token" value="<?php echo e(generate_csrf_token()); ?>">
              <button type="submit" style="background:#e74c3c;color:#fff;padding:6px 10px;border:0;border-radius:6px;cursor:pointer">Supprimer</button>
            </form>
          </td>
          <td><?php echo (int)$i['id']; ?></td>
          <td><?php echo htmlspecialchars($i['cycle']); ?></td>
          <td><?php echo htmlspecialchars($i['affecte']); ?></td>
          <td><?php echo number_format($i['montant'],0,',',' '); ?> FCFA</td>
          <td><?php echo htmlspecialchars($i['created_at']); ?></td>
          <td>
            <?php if(!empty($i['pdf_filename'])): ?>
              <a href="../public/uploads/<?php echo rawurlencode($i['pdf_filename']); ?>" download>Télécharger</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</main>
</body>
</html>
