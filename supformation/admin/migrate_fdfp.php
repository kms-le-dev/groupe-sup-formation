<?php
// admin/migrate_fdfp.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!is_admin()) { header('HTTP/1.1 403 Forbidden'); echo 'Accès refusé.'; exit; }

// paths
$logDir = __DIR__ . '/../public/logs'; if (!is_dir($logDir)) @mkdir($logDir,0755,true);
$backupFile = $logDir . '/fdfp_filenames_backup_' . time() . '.sql';
$logFile = $logDir . '/migrate_fdfp.log';

try {
    $pdo->beginTransaction();
    // select rows that do not start with 'uploads/' (we will normalize them)
    $stmt = $pdo->query("SELECT id, filename FROM fdfp_submissions WHERE filename NOT LIKE 'uploads/%'");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $outBackup = '';
    $updated = 0;
    foreach ($rows as $r) {
        $id = (int)$r['id'];
        $orig = $r['filename'];
        // compute basename then new path
        $basename = basename($orig);
        $new = 'uploads/fdfp/' . $basename;
        // write backup SQL
        $outBackup .= "UPDATE fdfp_submissions SET filename = '" . addslashes($orig) . "' WHERE id = " . $id . ";\n";
        // perform update
        $u = $pdo->prepare('UPDATE fdfp_submissions SET filename = ? WHERE id = ?');
        $u->execute([$new, $id]);
        $updated++;
    }

    // commit
    $pdo->commit();

    if ($outBackup !== '') {
        file_put_contents($backupFile, "-- Backup of fdfp_submissions filename before migration\n" . $outBackup, LOCK_EX);
    }
    $logLine = date('Y-m-d H:i:s') . " Migration applied. Rows touched: $updated\n";
    file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);

    echo '<!doctype html><html><head><meta charset="utf-8"><title>Migration FDFP</title></head><body style="font-family:system-ui,Segoe UI,Roboto,Arial,Helvetica,sans-serif;">';
    echo '<h2>Migration FDFP filenames</h2>';
    echo '<p>Rows found for normalization: ' . count($rows) . '</p>';
    echo '<p>Rows updated: ' . $updated . '</p>';
    if (file_exists($backupFile)) echo '<p>Backup SQL: <a href="../public/logs/' . basename($backupFile) . '" target="_blank">' . basename($backupFile) . '</a></p>';
    echo '<p>Log file: <a href="../public/logs/' . basename($logFile) . '" target="_blank">' . basename($logFile) . '</a></p>';
    echo '<p><a href="dashboard.php">Retour au dashboard</a></p>';
    echo '</body></html>';
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    $err = date('Y-m-d H:i:s') . ' Migration ERROR: ' . $e->getMessage() . "\n";
    file_put_contents($logFile, $err, FILE_APPEND | LOCK_EX);
    http_response_code(500);
    echo 'Erreur lors de la migration: ' . htmlspecialchars($e->getMessage());
    exit;
}

?>
