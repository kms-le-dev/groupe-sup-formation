<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';


// check admin
if (!is_admin()) {
  header('Location: ../public/login.php'); exit;
}

// show flash messages (if any)
if (!empty($_SESSION['flash_error'])) {
  echo '<div class="container"><div class="alert alert-danger">' . htmlspecialchars($_SESSION['flash_error']) . '</div></div>';
  unset($_SESSION['flash_error']);
}
if (!empty($_SESSION['flash_success'])) {
  echo '<div class="container"><div class="alert alert-success">' . htmlspecialchars($_SESSION['flash_success']) . '</div></div>';
  unset($_SESSION['flash_success']);
}

// handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $id = (int)$_POST['delete_id'];
  $token = $_POST['csrf_token'] ?? null;
  if (!verify_csrf_token($token)) {
    $_SESSION['flash_error'] = 'Token CSRF invalide. Veuillez réessayer.';
    header('Location: placements.php'); exit;
  }

  try {
    $stmt = $pdo->prepare('SELECT filename FROM placements WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      // resolve possible locations (DB may store 'placements/xxx.pdf' but files are under 'uploads/placements')
      $publicRoot = __DIR__ . '/../public/';
      $candidates = [
        $publicRoot . $row['filename'],
        $publicRoot . 'uploads/' . ltrim($row['filename'], '/'),
        $publicRoot . 'uploads/placements/' . basename($row['filename']),
        $publicRoot . 'uploads/' . basename($row['filename']),
      ];
      $found = null;
      foreach ($candidates as $c) { if (file_exists($c)) { $found = $c; break; } }
      if ($found) unlink($found);
      $pdo->prepare('DELETE FROM placements WHERE id = ?')->execute([$id]);
      $_SESSION['flash_success'] = 'Soumission supprimée.';
    } else {
      $_SESSION['flash_error'] = 'Enregistrement non trouvé.';
    }
  } catch (Exception $e) {
    // log and show friendly message
    error_log('placements delete error: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'Erreur lors de la suppression: ' . $e->getMessage();
  }
  // If deletion succeeded, redirect to dashboard; otherwise stay on placements page
  if (!empty($_SESSION['flash_success'])) {
    header('Location: dashboard.php'); exit;
  }
  header('Location: placements.php'); exit;
}

// fetch records
try {
  $stmt = $pdo->query('SELECT * FROM placements ORDER BY created_at DESC');
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $rows = [];
}
?>


<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<div class="admin-content container">
  <h1>Placement de Personnel</h1>
  <style>
    /* Responsive table -> stacked cards on small screens */
    .responsive-table { width:100%; border-collapse:collapse; }
    .responsive-table th, .responsive-table td { padding:8px 10px; border-bottom:1px solid #e5e7eb; text-align:left; }
    /* Action container: default horizontal alignment */
    .responsive-actions { display:flex; gap:8px; align-items:center; }
    .responsive-actions .btn { display:inline-flex; align-items:center; justify-content:center; min-height:38px; padding:8px 10px; }
    @media (max-width:900px) {
      .responsive-table thead { display:none; }
      .responsive-table tr { display:block; margin-bottom:12px; background:#fff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.04); padding:10px; }
      .responsive-table td { display:block; width:100%; padding:6px 8px; border:0; }
      .responsive-table td::before { content: attr(data-label); font-weight:700; display:inline-block; width:120px; color:#374151; }
      /* On mobile, stack actions and center them with reduced size */
      .responsive-actions { flex-direction:column; align-items:center; }
      .responsive-actions .btn { display:inline-flex; justify-content:center; width:auto; padding:6px 10px; font-size:14px; min-height:34px; border-radius:8px; margin:6px auto; max-width:260px; }
      .responsive-actions a.btn { text-align:center; }
    }
    /* On slightly larger mobile keep horizontal scroll */
    @media (min-width:901px) and (max-width:1200px) {
      .table-wrap { overflow:auto; }
    }
  </style>
  <?php if (empty($rows)): ?>
    <p>Aucune soumission pour le moment.</p>
  <?php else: ?>
    <div class="table-wrap">
    <table class="table responsive-table">
      <thead><tr><th>ID</th><th>Type</th><th>Fichier</th><th>CV</th><th>Lettre de motivation</th><th>Meta</th><th>Créé</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td data-label="ID"><?= e($r['id']) ?></td>
            <td data-label="Type"><?= e($r['type']) ?></td>
            <?php
              $publicRoot = __DIR__ . '/../public/';
              // Fichier principal (formulaire)
              $candidate1 = $publicRoot . $r['filename'];
              if (file_exists($candidate1)) {
                $urlPath = ltrim($r['filename'], '/');
              } elseif (file_exists($publicRoot . 'uploads/' . ltrim($r['filename'], '/'))) {
                $urlPath = 'uploads/' . ltrim($r['filename'], '/');
              } elseif (file_exists($publicRoot . 'uploads/placements/' . basename($r['filename']))) {
                $urlPath = 'uploads/placements/' . basename($r['filename']);
              } else {
                $urlPath = ltrim($r['filename'], '/');
              }
              // CV
              $cvUrl = null;
              if (!empty($r['cv_file'])) {
                $cvUrl = $r['cv_file'];
              }
              // Lettre de motivation
              $coverUrl = null;
              if (!empty($r['cover_file'])) {
                $coverUrl = $r['cover_file'];
              }
            ?>
            <td data-label="Fichier"><a href="../public/<?= e($urlPath) ?>" target="_blank">Formulaire</a></td>
            <td data-label="CV">
              <?php if ($cvUrl): ?>
                <a href="../public/<?= e($cvUrl) ?>" target="_blank">Télécharger CV</a>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
            <td data-label="Lettre de motivation">
              <?php if ($coverUrl): ?>
                <a href="../public/<?= e($coverUrl) ?>" target="_blank">Télécharger Lettre</a>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
            <td data-label="Meta"><pre style="max-width:360px;white-space:pre-wrap;"><?= e($r['meta']) ?></pre></td>
            <td data-label="Créé"><?= e($r['created_at']) ?></td>
            <td data-label="Actions">
              <div class="responsive-actions">
                <a class="btn small" href="../public/download_placement_file.php?id=<?= e($r['id']) ?>&type=form">Télécharger</a>
                <?php if ($cvUrl): ?><a class="btn small" href="../public/download_placement_file.php?id=<?= e($r['id']) ?>&type=cv">Télécharger CV</a><?php endif; ?>
                <?php if ($coverUrl): ?><a class="btn small" href="../public/download_placement_file.php?id=<?= e($r['id']) ?>&type=cover">Télécharger Lettre</a><?php endif; ?>
                <form method="post" action="placements.php" style="display:inline" onsubmit="return confirm('Supprimer ?');">
                  <input type="hidden" name="delete_id" value="<?= e($r['id']) ?>">
                  <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">
                  <button type="submit" name="delete_btn" class="btn small danger">Supprimer</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>


