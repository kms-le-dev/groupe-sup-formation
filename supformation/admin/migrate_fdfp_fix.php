<?php
// admin/migrate_fdfp_fix.php
// Safe migration UI to normalize fdfp_submissions.filename values to uploads/fdfp/<basename>
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!is_admin()) { header('HTTP/1.1 403 Forbidden'); echo "Accès refusé."; exit; }

$csrf = generate_csrf_token();

// helper to write log
function ensure_logs_dir() {
    $dir = __DIR__ . '/../public/logs';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return $dir;
}

// find candidate rows
$candidates = $pdo->query("SELECT id, filename FROM fdfp_submissions WHERE filename NOT LIKE 'uploads/%'")->fetchAll(PDO::FETCH_ASSOC);
$count = count($candidates);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf']) || !verify_csrf_token($_POST['csrf'])) {
        die('Token CSRF invalide.');
    }

    if ($count === 0) {
        $msg = "Aucune ligne à normaliser.";
    } else {
        $backup_sql = [];
        foreach ($candidates as $r) {
            $id = (int)$r['id'];
            $old = $r['filename'];
            $new = 'uploads/fdfp/' . basename($old);
            $backup_sql[] = "UPDATE fdfp_submissions SET filename = " . $pdo->quote($new) . " WHERE id = $id;";
        }

        $logs_dir = ensure_logs_dir();
        $backup_file = $logs_dir . DIRECTORY_SEPARATOR . 'fdfp_filenames_backup_' . time() . '.sql';
        file_put_contents($backup_file, implode("\n", $backup_sql));

        // apply updates in transaction
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('UPDATE fdfp_submissions SET filename = ? WHERE id = ?');
            $updated = 0;
            foreach ($candidates as $r) {
                $id = (int)$r['id'];
                $new = 'uploads/fdfp/' . basename($r['filename']);
                $stmt->execute([$new, $id]);
                $updated += $stmt->rowCount();
            }
            $pdo->commit();
            $msg = "Migration appliquée : $updated lignes mises à jour. Backup : " . basename($backup_file);
        } catch (Exception $e) {
            $pdo->rollBack();
            $msg = 'Erreur lors de la migration: ' . $e->getMessage();
        }
    }

}

?><!doctype html>
<html><head><meta charset="utf-8"><title>Migrate FDFP filenames</title>
<style>body{font-family:Arial,Helvetica,sans-serif;padding:20px;}</style></head><body>
<h1>Migration – normaliser fdfp_submissions.filename</h1>
<?php if (!empty($msg)): ?>
  <div style="padding:1rem;background:#eef6ff;border:1px solid #cfe3ff;border-radius:6px;margin-bottom:1rem;"><?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>
<p>Entrées détectées ne commençant pas par <code>uploads/</code> : <strong><?php echo (int)$count; ?></strong></p>
<?php if ($count > 0): ?>
  <table style="border-collapse:collapse;width:100%;max-width:900px;margin-bottom:1rem;">
    <thead><tr><th style="text-align:left;border-bottom:1px solid #ddd;padding:.4rem">Id</th><th style="text-align:left;border-bottom:1px solid #ddd;padding:.4rem">Ancien</th><th style="text-align:left;border-bottom:1px solid #ddd;padding:.4rem">Normalisé proposé</th></tr></thead>
    <tbody>
      <?php foreach($candidates as $r): $id=(int)$r['id']; $old = $r['filename']; $new = 'uploads/fdfp/' . basename($old); ?>
        <tr><td style="padding:.4rem;border-bottom:1px solid #f0f0f0"><?php echo $id; ?></td><td style="padding:.4rem;border-bottom:1px solid #f0f0f0"><?php echo htmlspecialchars($old); ?></td><td style="padding:.4rem;border-bottom:1px solid #f0f0f0"><?php echo htmlspecialchars($new); ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <form method="post" onsubmit="return confirm('Exécuter la migration et mettre à jour les filenames ?');">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
    <button type="submit" style="padding:.6rem 1rem;border-radius:6px;background:#2563eb;color:#fff;border:none;">Exécuter la migration</button>
  </form>
<?php else: ?>
  <p>Rien à faire. Les filenames semblent déjà normalisés.</p>
<?php endif; ?>
<p style="margin-top:1rem;color:#666;font-size:.9rem">Le script écrit un fichier de backup SQL dans <code>public/logs/</code> avant d'appliquer les modifications.</p>
<p><a href="dashboard.php">Retour au dashboard</a></p>
</body></html>
