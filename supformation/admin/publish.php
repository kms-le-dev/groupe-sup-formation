<?php
// admin/publish.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!is_admin()) { http_response_code(403); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
if (!verify_csrf_token($_POST['csrf'] ?? '')) { http_response_code(400); exit; }

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$domain_id = (int)($_POST['domain_id'] ?? 0);
$published = !empty($_POST['published']) ? 'published' : 'draft';

$media_id = null;
if (!empty($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
    $res = safe_upload($_FILES['media'], __DIR__ . '/../public/uploads/');
    if (!empty($res['error'])) { echo $res['error']; exit; }
    // insert media record
    $stmt = $pdo->prepare("INSERT INTO media (filename, original_name, mime_type, size_bytes, uploaded_by) VALUES (:fn,:on,:mt,:sz,:u)");
    $stmt->execute([':fn'=>$res['filename'],':on'=>$_FILES['media']['name'],':mt'=>mime_content_type(__DIR__ . '/../public/uploads/'.$res['filename']),':sz'=>$_FILES['media']['size'],':u'=>$_SESSION['user_id']]);
    $media_id = $pdo->lastInsertId();
}

$stmt = $pdo->prepare("INSERT INTO publications (title, excerpt, content, domain_id, media_id, author_id, status, published_at) VALUES (:t,:ex,:c,:d,:m,:a,:s, :pa)");
$excerpt = substr(strip_tags($content),0,250);
$published_at = ($published==='published') ? date('Y-m-d H:i:s') : null;
$stmt->execute([':t'=>$title,':ex'=>$excerpt,':c'=>$content,':d'=>$domain_id,':m'=>$media_id,':a'=>$_SESSION['user_id'],':s'=>$published, ':pa'=>$published_at]);
header('Location: dashboard.php?ok=1');
