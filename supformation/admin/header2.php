<?php
// includes/header.php
$user = current_user();
?>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand">
      <a href="../public/index.php"><img src="../public/assets/logo.png" alt="logo" class="logo"></a>
    </div>
    <nav class="main-nav">
      <a href="../public/enseignement.php">Enseignement Supérieur</a>
      <a href="../public/placement.php">Placement de Personnel</a>
      <a href="../public/fdfp.php">Cabinet FDFP</a>
      <a href="../public/contact.php">Contact</a>
    </nav>
    <div class="auth">
      <?php if ($user): ?>
        <span>Salut, <?=htmlspecialchars($user['first_name'])?></span>
        <a href="../public/logout.php" class="btn small">Déconnexion</a>
        <?php if (is_admin()): ?>
          <a href="dashboard.php" class="btn small">Admin</a>
        <?php endif; ?>
      <?php else: ?>
        <a href="../public/login.php" class="btn small">Connexion</a>
        <a href="../public/register.php" class="btn small primary">Inscription</a>
      <?php endif; ?>
    </div>
  </div>
</header>
