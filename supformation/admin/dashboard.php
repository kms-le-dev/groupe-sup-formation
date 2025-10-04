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
?>
<!doctype html><html><head><meta charset="utf-8"><title>Admin</title><link rel="stylesheet" href="../public/assets/css/styles.css"></head><body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<main class="container">
  <h1>Admin Dashboard</h1>

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

  <section>
    <h2>Utilisateurs</h2>
    <table>
      <thead><tr><th>Id</th><th>Nom</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach($users as $u): ?>
        <tr>
          <td><?=$u['id']?></td>
          <td><?=htmlspecialchars($u['first_name'].' '.$u['last_name'])?></td>
          <td><?=htmlspecialchars($u['email'])?></td>
          <td><?=$u['role_id']?></td>
          <td>
            <form method="post" action="change_role.php" style="display:inline">
              <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
              <input type="hidden" name="user_id" value="<?=$u['id']?>">
              <select name="role_id"><option value="1">user</option><option value="2">admin</option><option value="3">manager</option></select>
              <button>Change</button>
            </form>
            <form method="post" action="delete_user.php" style="display:inline" onsubmit="return confirm('Supprimer?')">
              <input type="hidden" name="csrf" value="<?=htmlspecialchars($csrf)?>">
              <input type="hidden" name="user_id" value="<?=$u['id']?>">
              <button>Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach;?>
      </tbody>
    </table>
  </section>
</main>
</body></html>
