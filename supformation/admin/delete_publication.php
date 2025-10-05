<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!is_admin()) { http_response_code(403); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
if (!verify_csrf_token($_POST['csrf'] ?? '')) { header('Location: dashboard.php?msg=csrf'); exit; }
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { header('Location: dashboard.php?msg=invalid'); exit; }
// delete media file if exists
$stmt = $pdo->prepare('SELECT media_id FROM publications WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row && !empty($row['media_id'])) {
    $m = $pdo->prepare('SELECT filename FROM media WHERE id = ?');
    $m->execute([$row['media_id']]);
    $mf = $m->fetch(PDO::FETCH_ASSOC);
    if ($mf && !empty($mf['filename'])) {
        @unlink(__DIR__ . '/../public/uploads/' . $mf['filename']);
        $pdo->prepare('DELETE FROM media WHERE id = ?')->execute([$row['media_id']]);
    }
}
$pdo->prepare('DELETE FROM publications WHERE id = ?')->execute([$id]);
header('Location: dashboard.php?msg=deleted');
