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
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Éditer publication</title>
<link rel="stylesheet" href="../public/assets/css/styles.css">
<style>
    /* ----- Container principal ----- */
.edit-container {
  max-width: 600px;
  margin: 60px auto;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  text-align: center;
  animation: fadeIn 0.8s ease;
}

/* Animation d’apparition */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.edit-container h1 {
  font-size: 2rem;
  margin-bottom: 25px;
  color: #212529;
  font-weight: 600;
}

/* ----- Formulaire ----- */
.edit-container form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

/* Champs de saisie */
.edit-container input[type="text"],
.edit-container textarea {
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #ccc;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.edit-container input[type="text"]:focus,
.edit-container textarea:focus {
  border-color: #007bff;
  box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
  outline: none;
}

/* Zone de texte */
.edit-container textarea {
  height: 150px;
  resize: vertical;
}

/* Checkbox */
.checkbox-label {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 1rem;
  color: #343a40;
}

/* Bouton */
.edit-container button {
  background: linear-gradient(135deg, #007bff, #6610f2);
  color: white;
  border: none;
  padding: 12px;
  border-radius: 10px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: transform 0.3s ease, background 0.3s ease;
}

.edit-container button:hover {
  background: linear-gradient(135deg, #6610f2, #007bff);
  transform: scale(1.05);
}

/* ----- Responsive design ----- */
@media (max-width: 600px) {
  .edit-container {
    margin: 30px 15px;
    padding: 25px;
  }

  .edit-container h1 {
    font-size: 1.6rem;
  }
}
</style>
</head>
<body>
<?php include __DIR__ . '/header2.php'; ?>
<main class="edit-container">
  <h1>Éditer la publication</h1>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
  <input type="text" name="title" value="<?=htmlspecialchars($pub['title'])?>" required>
  <textarea name="content" required><?=htmlspecialchars($pub['content'])?></textarea>
    <label><input type="checkbox" name="published" value="1" <?php if($pub['status']==='published') echo 'checked'; ?>> Publier</label>
    <button type="submit">Mettre à jour</button>
  </form>
</main>
</body>
<style>
    /* ----- Container principal ----- */
.edit-container {
  max-width: 600px;
  margin: 60px auto;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  text-align: center;
  animation: fadeIn 0.8s ease;
}

/* Animation d’apparition */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.edit-container h1 {
  font-size: 2rem;
  margin-bottom: 25px;
  color: #212529;
  font-weight: 600;
}

/* ----- Formulaire ----- */
.edit-container form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

/* Champs de saisie */
.edit-container input[type="text"],
.edit-container textarea {
  width: 100%;
  padding: 12px 14px;
  border: 1px solid #ccc;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.edit-container input[type="text"]:focus,
.edit-container textarea:focus {
  border-color: #007bff;
  box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
  outline: none;
}

/* Zone de texte */
.edit-container textarea {
  height: 150px;
  resize: vertical;
}

/* Checkbox */
.checkbox-label {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 1rem;
  color: #343a40;
}

/* Bouton */
.edit-container button {
  background: linear-gradient(135deg, #007bff, #6610f2);
  color: white;
  border: none;
  padding: 12px;
  border-radius: 10px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: transform 0.3s ease, background 0.3s ease;
}

.edit-container button:hover {
  background: linear-gradient(135deg, #6610f2, #007bff);
  transform: scale(1.05);
}

/* ----- Responsive design ----- */
@media (max-width: 600px) {
  .edit-container {
    margin: 30px 15px;
    padding: 25px;
  }

  .edit-container h1 {
    font-size: 1.6rem;
  }
}

</style>
</html>