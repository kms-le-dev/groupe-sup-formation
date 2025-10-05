<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!is_admin()) { http_response_code(403); exit; }
$csrf = generate_csrf_token();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: dashboard.php?msg=invalid'); exit; }
$stmt = $pdo->prepare('SELECT * FROM publications WHERE id = ?');
$stmt->execute([$id]);
$pub = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pub) { header('Location: dashboard.php?msg=invalid'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!verify_csrf_token($_POST['csrf'] ?? '')) { header('Location: dashboard.php?msg=csrf'); exit; }
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');
  $published = !empty($_POST['published']) ? 'published' : 'draft';
  $pdo->prepare('UPDATE publications SET title = ?, excerpt = ?, content = ?, status = ? WHERE id = ?')
      ->execute([$title, substr(strip_tags($content),0,250), $content, $published, $id]);
  header('Location: dashboard.php?msg=updated');
  exit;
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Éditer publication</title>
<link rel="stylesheet" href="../public/assets/css/styles.css"></head><body>
<?php include __DIR__ . '/header2.php'; ?>
<main class="container">
  <h1>Éditer la publication</h1>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
    <input name="title" value="<?=htmlspecialchars($pub['title'])?>" required>
    <textarea name="content" required><?=htmlspecialchars($pub['content'])?></textarea>
    <label><input type="checkbox" name="published" value="1" <?php if($pub['status']==='published') echo 'checked'; ?>> Publier</label>
    <button type="submit">Mettre à jour</button>
  </form>
</main>
</body></html>