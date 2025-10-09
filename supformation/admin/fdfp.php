<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!is_admin()) { if (empty($IN_DASHBOARD_PANEL)) { header('Location: ../public/login.php'); exit; } }

// ensure a CSRF token is available for forms (dashboard provides one sometimes)
if (empty($csrf)) {
  $csrf = generate_csrf_token();
}

// handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $id = (int)$_POST['delete_id'];
  $token = $_POST['csrf_token'] ?? null;
  if (!verify_csrf_token($token)) { $_SESSION['flash_error'] = 'Token CSRF invalide.'; header('Location: dashboard.php'); exit; }
  $stmt = $pdo->prepare('SELECT filename FROM fdfp_submissions WHERE id = ?');
  $stmt->execute([$id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    // resolve possible file locations
    $publicRoot = __DIR__ . '/../public/';
    $candidates = [
      $publicRoot . $row['filename'],
      $publicRoot . 'uploads/' . ltrim($row['filename'], '/'),
      $publicRoot . 'uploads/fdfp/' . basename($row['filename']),
      $publicRoot . 'uploads/' . basename($row['filename']),
    ];
    foreach ($candidates as $c) { if (file_exists($c)) { @unlink($c); break; } }
    $pdo->prepare('DELETE FROM fdfp_submissions WHERE id = ?')->execute([$id]);
    $_SESSION['flash_success'] = 'Soumission supprimée.';
  }
  if (empty($IN_DASHBOARD_PANEL)) { header('Location: dashboard.php'); exit; }
}

$rows = [];
try {
  $stmt = $pdo->query('SELECT * FROM fdfp_submissions ORDER BY created_at DESC');
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $rows = []; }
?>
<div class="admin-content container">
  <h2>Demandes de formation (FDFP)</h2>
  <?php
    // show how many rows were fetched
    $countRows = is_array($rows) ? count($rows) : 0;
    echo '<div style="margin:.5rem 0;padding:.5rem 1rem;background:#f8fafc;border:1px solid #e6eef6;border-radius:6px;color:#0f172a;">Nombre d\'enregistrements : <strong>' . (int)$countRows . '</strong></div>';
    if ($countRows === 0) {
      echo '<div style="padding:1rem;background:#fff7ed;border:1px solid #fde3bf;border-radius:6px;color:#92400e;margin-bottom:1rem;">Aucune demande trouvée. Si vous avez récemment soumis le formulaire, vérifiez que la requête a renvoyé <code>{"success":true}</code> et que le fichier a été créé dans <code>public/uploads/fdfp/</code>.</div>';
    }
  ?>
  <style>
    .responsive-table { width:100%; border-collapse:collapse; }
    .responsive-table th, .responsive-table td { padding:8px 10px; border-bottom:1px solid #e5e7eb; text-align:left; }
    @media (max-width:900px) {
      .responsive-table thead { display:none; }
      .responsive-table tr { display:block; margin-bottom:12px; background:#fff; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.04); padding:10px; }
      .responsive-table td { display:block; width:100%; padding:6px 8px; border:0; }
      .responsive-table td::before { content: attr(data-label); font-weight:700; display:inline-block; width:120px; color:#374151; }
      .actions { display:flex; gap:8px; flex-direction:column; align-items:center; }
      .actions a, .actions button { width:100%; max-width:260px; }
    }
  </style>
  <?php if (empty($rows)): ?>
    <p>Aucune demande pour le moment.</p>
  <?php else: ?>
    <table class="responsive-table">
      <thead><tr><th>ID</th><th>Fichier</th><th>Meta</th><th>Créé</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?= e($r['id']) ?></td>
            <?php
              $publicRoot = __DIR__ . '/../public/';
              if (file_exists($publicRoot . $r['filename'])) {
                $urlPath = ltrim($r['filename'], '/');
              } elseif (file_exists($publicRoot . 'uploads/' . ltrim($r['filename'], '/'))) {
                $urlPath = 'uploads/' . ltrim($r['filename'], '/');
              } elseif (file_exists($publicRoot . 'uploads/fdfp/' . basename($r['filename']))) {
                $urlPath = 'uploads/fdfp/' . basename($r['filename']);
              } else {
                $urlPath = ltrim($r['filename'], '/');
              }
            ?>
            <?php
              // build download href
              $downloadHref = (defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' : '../public/') . ltrim($urlPath, '/');
            ?>
            <td>
              <a href="<?= e($downloadHref) ?>" target="_blank"><?= e(basename($r['filename'])) ?></a>
              <?php
                // server-side resolved candidate path (helpful to show location)
                $publicRoot = __DIR__ . '/../public/';
                $candidates = [
                  $publicRoot . $r['filename'],
                  $publicRoot . 'uploads/' . ltrim($r['filename'], '/'),
                  $publicRoot . 'uploads/fdfp/' . basename($r['filename']),
                  $publicRoot . 'uploads/' . basename($r['filename']),
                ];
                $foundPath = null;
                foreach ($candidates as $c) { if (file_exists($c)) { $foundPath = $c; break; } }
                echo '<div style="font-size:0.8rem;color:#6b7280;margin-top:6px;">' . ($foundPath ? 'Fichier côté serveur: ' . e(str_replace(__DIR__ . '/../', '', $foundPath)) : 'Fichier non trouvé sur le serveur') . '</div>';
              ?>
            </td>
            <td>
              <?php
                // decode JSON meta and show as definition list for readability
                if (!empty($r['meta'])) {
                  $decoded = json_decode($r['meta'], true);
                  if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    echo '<dl style="margin:0;font-size:0.95rem;">';
                    foreach ($decoded as $mk => $mv) {
                      echo '<dt style="font-weight:700;display:inline-block;width:140px;color:#0f172a;">' . e(strtoupper(str_replace('_',' ',$mk))) . '</dt>';
                      echo '<dd style="margin:0 0 6px 0;display:inline-block;color:#374151;">' . e((string)$mv) . '</dd>';
                    }
                    echo '</dl>';
                  } else {
                    echo '<pre style="max-width:420px;white-space:pre-wrap;overflow:auto;padding:.4rem;background:#f8fafc;border-radius:6px;border:1px solid #eef2ff;">' . e($r['meta']) . '</pre>';
                  }
                } else {
                  echo '&mdash;';
                }
              ?>
            </td>
            <td><?= e($r['created_at']) ?></td>
            <td>
              <div class="actions">
                <?php
                  // prefer BASE_URL if defined for robust links
                  $downloadHref = (defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' : '../public/') . ltrim($urlPath, '/');
                ?>
                <a class="btn small" href="<?= e($downloadHref) ?>" download target="_blank">Télécharger</a>
                <form method="post" action="fdfp.php" style="display:inline" onsubmit="return confirm('Supprimer ?');">
                  <input type="hidden" name="delete_id" value="<?= e($r['id']) ?>">
                  <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">
                  <button class="btn small danger">Supprimer</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
