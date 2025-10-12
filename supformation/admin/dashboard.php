<?php
// admin/dashboard.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!is_admin()) { header('HTTP/1.1 403 Forbidden'); echo "Accès refusé."; exit; }

$csrf = generate_csrf_token();

// list users
$users = $pdo->query("SELECT id, email, first_name, last_name, role_id, created_at FROM users ORDER BY created_at DESC LIMIT 200")->fetchAll(PDO::FETCH_ASSOC);

// list publications
$pubs = $pdo->query("SELECT p.*, d.title as domain FROM publications p JOIN domains d ON p.domain_id = d.id ORDER BY created_at DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
// role mapping for display
$role_map = [1 => 'user', 2 => 'admin', 3 => 'manager'];
?>







<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../public/assets/css/styles.css">
    <style>
    /* === CSS DYNAMIQUE ET ANIMÉ POUR ADMIN DASHBOARD === */

main.container {
  --c1: #ec1c24;
  --c2: #16a34a;
  --c3: #ffffff;
  --c4: #2563eb;
  --c5: #f59e0b;
  background: linear-gradient(135deg, #ffffff, #f8fafc);
  padding: 2.5rem;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.08);
  position: relative;
  overflow: hidden;
  animation: containerFadeIn 0.6s ease-out;
}

@keyframes containerFadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Gradient animé en arrière-plan */
main.container::before {
  content: '';
  position: absolute;
  inset: -50%;
  background: 
    radial-gradient(circle at 20% 30%, rgba(236,28,36,0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(22,163,74,0.08) 0%, transparent 50%),
    radial-gradient(circle at 50% 50%, rgba(37,99,235,0.06) 0%, transparent 50%);
  z-index: 0;
  animation: gradientFlow 15s ease-in-out infinite;
  pointer-events: none;
}

@keyframes gradientFlow {
  0%, 100% { transform: translate(0, 0) rotate(0deg); }
  33% { transform: translate(5%, -5%) rotate(120deg); }
  66% { transform: translate(-5%, 5%) rotate(240deg); }
}

/* Particules flottantes décoratives */
main.container::after {
  content: '';
  position: absolute;
  width: 300px;
  height: 300px;
  top: -150px;
  right: -150px;
  background: radial-gradient(circle, rgba(245,158,11,0.12), transparent 70%);
  border-radius: 50%;
  animation: float 20s ease-in-out infinite;
  z-index: 0;
  pointer-events: none;
}

@keyframes float {
  0%, 100% { transform: translate(0, 0) scale(1); }
  50% { transform: translate(-30px, 30px) scale(1.1); }
}

/* Titre principal avec effet holographique */
main.container > h1 {
  position: relative;
  z-index: 2;
  font-size: 2.5rem;
  font-weight: 900;
  background: linear-gradient(135deg, var(--c1), var(--c4), var(--c2), var(--c5));
  background-size: 300% 300%;
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  letter-spacing: 0.5px;
  margin-bottom: 2rem;
  animation: gradientShift 8s ease infinite, titlePulse 2s ease-in-out infinite;
  text-shadow: 0 0 30px rgba(236,28,36,0.2);
}

@keyframes gradientShift {
  0%, 100% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
}

@keyframes titlePulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.02); }
}

/* Sections avec effet de survol */
main.container section {
  position: relative;
  z-index: 2;
  margin-bottom: 2.5rem;
  padding: 1.5rem;
  background: rgba(255,255,255,0.7);
  border-radius: 12px;
  border: 1px solid rgba(15,23,42,0.08);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  animation: sectionSlideIn 0.5s ease-out backwards;
}
/* Ensure tab panels are visible even when empty */
.tab-panel { min-height: 120px; padding: 1rem 0; }

main.container section:nth-child(2) { animation-delay: 0.1s; }
main.container section:nth-child(3) { animation-delay: 0.2s; }

@keyframes sectionSlideIn {
  from { 
    opacity: 0; 
    transform: translateX(-30px);
  }
  to { 
    opacity: 1; 
    transform: translateX(0);
  }
}

main.container section:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 40px rgba(0,0,0,0.1);
  border-color: rgba(37,99,235,0.2);
}

main.container section h2 {
  color: #0f172a;
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 1rem;
  position: relative;
  display: inline-block;
}

main.container section h2::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--c1), var(--c2));
  transition: width 0.4s ease;
  border-radius: 2px;
}

main.container section:hover h2::after {
  width: 100%;
}

/* Formulaires stylisés */
main.container form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  position: relative;
  z-index: 2;
}

main.container form input[type="text"],
main.container form input[name="title"],
main.container form select,
main.container form textarea {
  padding: 0.9rem 1.2rem;
  border: 2px solid rgba(15,23,42,0.1);
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: rgba(255,255,255,0.9);
  position: relative;
}

main.container form input:focus,
main.container form select:focus,
main.container form textarea:focus {
  outline: none;
  border-color: var(--c4);
  box-shadow: 0 0 0 4px rgba(37,99,235,0.1), 0 8px 20px rgba(0,0,0,0.08);
  transform: translateY(-2px);
}

main.container form textarea {
  min-height: 120px;
  resize: vertical;
  font-family: inherit;
}

/* Boutons avec effet néon */
main.container form button,
main.container button {
  position: relative;
  z-index: 2;
  background: linear-gradient(135deg, var(--c2), #0ea5a0);
  color: var(--c3);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 15px rgba(22,163,74,0.3);
  overflow: hidden;
}

main.container form button::before,
main.container button::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255,255,255,0.3);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

main.container form button:hover,
main.container button:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: 0 10px 30px rgba(22,163,74,0.4);
}

main.container form button:hover::before,
main.container button:hover::before {
  width: 300px;
  height: 300px;
}

main.container form button:active,
main.container button:active {
  transform: translateY(-1px) scale(0.98);
}

/* Tables modernes avec animations */
main.container table {
  position: relative;
  z-index: 2;
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  background: rgba(255,255,255,0.8);
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

main.container table thead {
  background: linear-gradient(135deg, rgba(236,28,36,0.08), rgba(22,163,74,0.08));
  position: relative;
}

main.container table thead::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--c1), var(--c4), var(--c2));
  animation: lineFlow 3s linear infinite;
}

@keyframes lineFlow {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

main.container table th {
  padding: 1rem 1.2rem;
  text-align: left;
  font-weight: 700;
  color: #0f172a;
  text-transform: uppercase;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
}

main.container table td {
  padding: 1rem 1.2rem;
  border-bottom: 1px solid rgba(15,23,42,0.06);
  color: #334155;
  transition: all 0.3s ease;
}

main.container table tbody tr {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  animation: rowFadeIn 0.4s ease-out backwards;
}

main.container table tbody tr:nth-child(1) { animation-delay: 0.05s; }
main.container table tbody tr:nth-child(2) { animation-delay: 0.1s; }
main.container table tbody tr:nth-child(3) { animation-delay: 0.15s; }
main.container table tbody tr:nth-child(4) { animation-delay: 0.2s; }
main.container table tbody tr:nth-child(5) { animation-delay: 0.25s; }

@keyframes rowFadeIn {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

main.container table tbody tr:hover {
  transform: translateX(8px) scale(1.01);
  background: linear-gradient(90deg, rgba(37,99,235,0.05), rgba(22,163,74,0.05));
  box-shadow: 0 4px 15px rgba(0,0,0,0.06);
}

main.container table tbody tr:hover td {
  color: #0f172a;
}

/* Colonne Role avec badge animé */
main.container table td:nth-child(4) {
  font-weight: 700;
  color: var(--c1);
  position: relative;
  padding-right: 2rem;
}

main.container table td:nth-child(4)::after {
  content: '';
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--c1), var(--c2));
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  box-shadow: 0 0 10px rgba(236,28,36,0.4);
}

@keyframes pulse {
  0%, 100% {
    box-shadow: 0 0 0 0 rgba(236,28,36,0.4);
    transform: translateY(-50%) scale(1);
  }
  50% {
    box-shadow: 0 0 0 10px rgba(236,28,36,0);
    transform: translateY(-50%) scale(1.2);
  }
}

/* Checkbox personnalisé */
main.container input[type="checkbox"] {
  appearance: none;
  width: 20px;
  height: 20px;
  border: 2px solid rgba(15,23,42,0.2);
  border-radius: 5px;
  cursor: pointer;
  position: relative;
  transition: all 0.3s ease;
  background: white;
}

main.container input[type="checkbox"]:checked {
  background: linear-gradient(135deg, var(--c2), #0ea5a0);
  border-color: var(--c2);
}

main.container input[type="checkbox"]:checked::after {
  content: '✓';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: white;
  font-size: 14px;
  font-weight: bold;
  animation: checkPop 0.3s ease;
}

@keyframes checkPop {
  0% { transform: translate(-50%, -50%) scale(0); }
  50% { transform: translate(-50%, -50%) scale(1.2); }
  100% { transform: translate(-50%, -50%) scale(1); }
}

/* File input stylisé */
main.container input[type="file"] {
  padding: 0.8rem;
  border: 2px dashed rgba(15,23,42,0.2);
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  background: rgba(255,255,255,0.5);
}

main.container input[type="file"]:hover {
  border-color: var(--c4);
  background: rgba(37,99,235,0.05);
}

/* === RESPONSIVE === */
@media (max-width: 1024px) {
  main.container {
    padding: 2rem;
  }
  
  main.container > h1 {
    font-size: 2rem;
  }
}

/* Responsive users table (stacked cards on small screens) */
.users-table { width:100%; border-collapse:collapse; }
.users-table th, .users-table td { padding:0.6rem 0.8rem; border-bottom:1px solid rgba(15,23,42,0.06); }
@media (max-width:768px) {
  .users-table thead { display:none; }
  .users-table tr { display:block; margin-bottom:12px; background:#fff; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.04); padding:10px; }
  .users-table td { display:block; width:100%; padding:6px 8px; border:0; }
  .users-table td::before { content: attr(data-label); font-weight:700; display:inline-block; width:110px; color:#374151; }
  .users-table td.form-actions { display:flex; gap:8px; flex-direction:column; align-items:flex-start; }
  .users-table td.form-actions form, .users-table td.form-actions button, .users-table td.form-actions select, .users-table td.form-actions input[type="password"] { width:100%; }
  .users-table td.form-actions button { justify-content:center; }
}

@media (max-width: 768px) {
  main.container {
    padding: 1.5rem;
    border-radius: 12px;
  }
  
  main.container > h1 {
    font-size: 1.75rem;
  }
  
  main.container section {
    padding: 1rem;
  }
  
  main.container table {
    font-size: 0.9rem;
  }
  
  main.container table th,
  main.container table td {
    padding: 0.75rem 0.8rem;
  }
  
  /* On force l'empilement des tables sur petits écrans (pas de scroll horizontal) */
  main.container section:has(table) {
    overflow: visible;
  }

  /* Make any table stack into readable cards on small screens */
  main.container table thead { display:none; }
  main.container table, main.container table tbody, main.container table tr, main.container table td { display:block; width:100%; }
  main.container table tr { margin-bottom:12px; background:#fff; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.04); padding:10px; }
  main.container table td { padding:6px 8px; border:0; }
  main.container table td::before { content: attr(data-label); font-weight:700; display:inline-block; width:120px; color:#374151; }
}

@media (max-width: 480px) {
  main.container {
    padding: 1rem;
  }
  
  main.container > h1 {
    font-size: 1.5rem;
  }
  
  main.container form button,
  main.container button {
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
  }
  
  main.container table {
    font-size: 0.85rem;
  }
  
  main.container table th,
  main.container table td {
    padding: 0.6rem;
  }
  
  /* Empiler les formulaires sur mobile */
  main.container td form {
    display: block;
    margin-bottom: 0.5rem;
  }
}

/* Animation de chargement pour les boutons */
main.container button.loading {
  pointer-events: none;
  position: relative;
  color: transparent;
}

main.container button.loading::after {
  content: '';
  position: absolute;
  width: 16px;
  height: 16px;
  top: 50%;
  left: 50%;
  margin-left: -8px;
  margin-top: -8px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Effet de brillance sur les éléments interactifs */
@keyframes shine {
  0% { background-position: -200% center; }
  100% { background-position: 200% center; }
}

main.container button:not(:hover)::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
  background-size: 200% 100%;
  animation: shine 3s infinite;
  pointer-events: none;
}
    </style>
  </head>


  <body>
  <?php include __DIR__ . '/header2.php'; ?>
<main class="container">
  <h1>Admin Dashboard</h1>

  <?php if (!empty($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'role_ok'): ?>
      <div style="padding:.7rem 1rem; background:#d1fae5; color:#064e3b; border-radius:8px; margin-bottom:1rem;">Rôle mis à jour avec succès.</div>
    <?php elseif ($_GET['msg'] === 'role_err'): ?>
      <div style="padding:.7rem 1rem; background:#fee2e2; color:#7f1d1d; border-radius:8px; margin-bottom:1rem;">Erreur lors de la mise à jour du rôle.</div>
    <?php elseif ($_GET['msg'] === 'csrf'): ?>
      <div style="padding:.7rem 1rem; background:#fee2e2; color:#7f1d1d; border-radius:8px; margin-bottom:1rem;">Token CSRF invalide.</div>
    <?php elseif ($_GET['msg'] === 'invalid'): ?>
      <div style="padding:.7rem 1rem; background:#fef3c7; color:#7c2d12; border-radius:8px; margin-bottom:1rem;">Données invalides fournies.</div>
    <?php endif; ?>
  <?php
    // messages pour suppression utilisateur
    if (!empty($_GET['msg']) && $_GET['msg'] === 'delete_ok') {
      echo '<div style="padding:.7rem 1rem; background:#d1fae5; color:#064e3b; border-radius:8px; margin-bottom:1rem;">Utilisateur supprimé avec succès.</div>';
    } elseif (!empty($_GET['msg']) && $_GET['msg'] === 'delete_err') {
      echo '<div style="padding:.7rem 1rem; background:#fee2e2; color:#7f1d1d; border-radius:8px; margin-bottom:1rem;">Erreur lors de la suppression de l\'utilisateur.</div>';
    } elseif (!empty($_GET['msg']) && $_GET['msg'] === 'not_found') {
      echo '<div style="padding:.7rem 1rem; background:#fef3c7; color:#7c2d12; border-radius:8px; margin-bottom:1rem;">Utilisateur non trouvé.</div>';
    } elseif (!empty($_GET['msg']) && $_GET['msg'] === 'cannot_self') {
      echo '<div style="padding:.7rem 1rem; background:#fee2e2; color:#7f1d1d; border-radius:8px; margin-bottom:1rem;">Impossible de vous supprimer vous-même.</div>';
    }
  ?>
    <?php if ($_GET['msg'] === 'pass_ok'): ?>
      <div style="padding:.7rem 1rem; background:#d1fae5; color:#064e3b; border-radius:8px; margin-bottom:1rem;">Mot de passe mis à jour avec succès.</div>
    <?php elseif ($_GET['msg'] === 'pass_err'): ?>
      <div style="padding:.7rem 1rem; background:#fee2e2; color:#7f1d1d; border-radius:8px; margin-bottom:1rem;">Erreur lors de la mise à jour du mot de passe.</div>
    <?php elseif ($_GET['msg'] === 'invalid'): ?>
      <div style="padding:.7rem 1rem; background:#fef3c7; color:#7c2d12; border-radius:8px; margin-bottom:1rem;">Données invalides fournies.</div>
    <?php endif; ?>
  <?php endif; ?>

  <section>
    <h2>Publier un article</h2>
    <form action="publish.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
      <input name="title" placeholder="Titre" required>
      <select name="domain_id" required>
        <?php foreach($pdo->query("SELECT id,title FROM domains")->fetchAll() as $d): ?>
          <option value="<?=$d['id']?>"><?=htmlspecialchars($d['title'])?></option>
        <?php endforeach;?>
      </select>
      <textarea name="content" placeholder="Contenu" required></textarea>
      <input type="file" name="media">
      <label><input type="checkbox" name="published" value="1"> Publier maintenant</label>
      <button>Publier</button>
    </form>
  </section>
  <?php include __DIR__ . '/fdfp.php'; ?>
  <!-- Tabs header: publications / utilisateurs / inscriptions / placement -->
  <nav class="admin-tabs" style="margin:1rem 0 1.5rem;">
    <div class="tabs-container" style="display:flex;gap:.5rem;flex-wrap:wrap;">
  <button type="button" class="tab-btn active" data-target="tab-publications">Publications</button>
  <button type="button" class="tab-btn" data-target="tab-users">Utilisateurs</button>
  <button type="button" class="tab-btn" data-target="tab-inscriptions">Inscriptions</button>
  <button type="button" class="tab-btn" data-target="tab-placements">Placement de personnel</button>

    </div>
  </nav>

  <section id="tab-publications" class="tab-panel">
    <h2>Publications</h2>
    <?php if ($pubs): ?>
      <table>
        <thead>
          <tr>
            <th>Actions</th>
            <th>Titre</th>
            <th>Domaine</th>
            <th>Date</th>
            <th>Images</th>
            <th>Contenu</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach($pubs as $p): ?>
          <?php
            $media = $pdo->prepare("SELECT filename FROM media WHERE id = ?");
            $media->execute([(int)$p['media_id']]);
            $m = $media->fetch(PDO::FETCH_ASSOC);
            $thumbHtml = '';
            if ($m && !empty($m['filename'])) {
              $ext = pathinfo($m['filename'], PATHINFO_EXTENSION);
              if (in_array(strtolower($ext), ['jpg','jpeg','png','gif'])) {
                $thumb = htmlspecialchars($m['filename']);
                $thumbHtml = "<img src=\"../public/uploads/{$thumb}\" alt=\"vignette\" style=\"width:80px;height:60px;object-fit:cover;border-radius:6px;\">";
              } elseif (strtolower($ext) === 'mp4') {
                $thumbHtml = "<video src=\"../public/uploads/".htmlspecialchars($m['filename'])."\" style=\"width:80px;height:60px;object-fit:cover;border-radius:6px;\" muted></video>";
              }
            }
          ?>
          <tr>
            <td data-label="Actions" style="white-space:nowrap;">
              <a href="edit_publication.php?id=<?= (int)$p['id'] ?>" style="margin-right:6px;display:inline-block;padding:.35rem .5rem;background:#f3f4f6;border-radius:6px;text-decoration:none;color:#0f172a;border:1px solid #e5e7eb;">Modifier</a>
              <form method="post" action="delete_publication.php" style="display:inline;" onsubmit="return confirm('Supprimer cette publication ?');">
                <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button type="submit" style="padding:.35rem .5rem;background:#fee2e2;color:#7f1d1d;border-radius:6px;border:1px solid #fecaca;">Supprimer</button>
              </form>
            </td>
            <td data-label="Titre"><?=htmlspecialchars($p['title'])?></td>
            <td data-label="Domaine"><?=htmlspecialchars($p['domain'] ?? '')?></td>
            <td data-label="Date"><?=htmlspecialchars($p['created_at'])?></td>
            <td data-label="Images"><?= $thumbHtml ?></td>
            <td data-label="Contenu" style="max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?=htmlspecialchars($p['excerpt'] ?: (strlen($p['content'])>200?substr($p['content'],0,197).'...':$p['content']))?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>Aucune publication trouvée.</p>
    <?php endif; ?>
  </section>
  <?php
    // Debug block removed: cleaned up per request
  ?>
  <?php
    // show session flash messages (e.g., from admin panels that redirect here)
    if (!empty($_SESSION['flash_success'])) {
      echo '<div style="padding:.7rem 1rem; background:#d1fae5; color:#064e3b; border-radius:8px; margin-bottom:1rem;">' . htmlspecialchars($_SESSION['flash_success']) . '</div>';
      unset($_SESSION['flash_success']);
    }
    if (!empty($_SESSION['flash_error'])) {
      echo '<div style="padding:.7rem 1rem; background:#fee2e2; color:#7f1d1d; border-radius:8px; margin-bottom:1rem;">' . htmlspecialchars($_SESSION['flash_error']) . '</div>';
      unset($_SESSION['flash_error']);
    }
  ?>

  <section id="tab-users" class="tab-panel" style="display:none;">
    <h2>Utilisateurs</h2>
    <table>
      <thead><tr><th>Id</th><th>Nom</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach($users as $u): ?>
        <tr>
          <td data-label="Id"><?=$u['id']?></td>
          <td data-label="Nom"><?=htmlspecialchars($u['first_name'].' '.$u['last_name'])?></td>
          <td data-label="Email"><?=htmlspecialchars($u['email'])?></td>
          <td data-label="Role"><?=htmlspecialchars($role_map[$u['role_id']] ?? $u['role_id'])?></td>
          <td data-label="Actions" class="form-actions">
            <form method="post" action="change_role.php" style="display:inline">
              <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
              <input type="hidden" name="user_id" value="<?=$u['id']?>">
              <select name="role_id">
                <?php foreach($role_map as $rid => $rlabel): ?>
                  <option value="<?= $rid ?>" <?php if ((int)$rid === (int)$u['role_id']) echo 'selected'; ?>><?=htmlspecialchars($rlabel)?></option>
                <?php endforeach; ?>
              </select>
              <button>Changer</button>
            </form>
            <form method="post" action="delete_user.php" style="display:inline" onsubmit="return confirm('Supprimer?')">
              <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
              <input type="hidden" name="user_id" value="<?=$u['id']?>">
              <button>Supprimer</button>
            </form>
            <!-- Small change-password form -->
            <form method="post" action="change_password.php" style="display:inline;margin-left:6px;" onsubmit="return confirm('Changer le mot de passe pour cet utilisateur ?')">
              <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
              <input type="hidden" name="user_id" value="<?=$u['id']?>">
              <input type="password" name="new_password" placeholder="Nouveau mot de passe" style="padding:.35rem .5rem; border-radius:6px; border:1px solid #ccc;" required>
              <button type="submit" style="padding:.35rem .6rem;">Modifier</button>
            </form>
          </td>
        </tr>
      <?php endforeach;?>
      </tbody>
    </table>
  </section>

  <div id="tab-inscriptions" class="tab-panel" style="display:none;">
    <?php include __DIR__ . '/inscriptions.php'; ?>
  </div>

  <div id="tab-placements" class="tab-panel" style="display:none;">
    <?php include __DIR__ . '/placements.php'; ?>
  </div>

  

  <script>
    (function(){
      // Notification system
      function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          padding: 15px 25px;
          border-radius: 8px;
          box-shadow: 0 4px 12px rgba(0,0,0,0.15);
          z-index: 1000;
          animation: slideIn 0.3s ease forwards;
          color: #fff;
          max-width: 400px;
        `;
        
        if (type === 'success') {
          notification.style.background = '#16a34a';
        } else if (type === 'error') {
          notification.style.background = '#dc2626';
        }

        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
          notification.style.animation = 'slideOut 0.3s ease forwards';
          setTimeout(() => notification.remove(), 300);
        }, 3000);
      }

      // Handle flash messages from PHP session
      if (window.flashMessage) {
        showNotification(window.flashMessage.message, window.flashMessage.type);
      }

      // Tab management
      function showPanel(id){
        document.querySelectorAll('.tab-panel').forEach(function(p){ p.style.display = (p.id === id) ? 'block' : 'none'; });
        document.querySelectorAll('.tab-btn').forEach(function(b){ b.classList.toggle('active', b.dataset.target === id); });
      }

      document.addEventListener('click', function(e){
        var btn = e.target.closest('.tab-btn');
        if (!btn) return;
        var target = btn.dataset.target;
        if (target) {
          showPanel(target);
          try { localStorage.setItem('admin_active_tab', target); } catch (e){}
        }

        // Handle delete actions
        if (e.target.matches('.btn-delete')) {
          e.preventDefault();
          if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
            const form = e.target.closest('form');
            const formData = new FormData(form);
            
            fetch(form.action, {
              method: 'POST',
              body: formData
            })
            .then(response => response.text())
            .then(result => {
              showNotification('Élément supprimé avec succès', 'success');
              // Refresh the page after a short delay
              setTimeout(() => window.location.reload(), 1000);
            })
            .catch(error => {
              showNotification('Erreur lors de la suppression', 'error');
            });
          }
        }
      });

      // on load restore last tab or default to publications
      var last = null;
      try { last = localStorage.getItem('admin_active_tab'); } catch (e){}
      if (last && document.getElementById(last)) {
        showPanel(last);
      } else {
        showPanel('tab-publications');
      }
      
      // Add keyframes for animations
      const style = document.createElement('style');
      style.textContent = `
        @keyframes slideIn {
          from { transform: translateX(100%); opacity: 0; }
          to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
          from { transform: translateX(0); opacity: 1; }
          to { transform: translateX(100%); opacity: 0; }
        }
      `;
      document.head.appendChild(style);
    })();
  </script>
</main>
</body></html>
