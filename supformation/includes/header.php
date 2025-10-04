<?php
// includes/header.php
$user = current_user();
?>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand">
      <a href="../public/index.php"><img src="assets/logo.png" alt="logo" class="logo"></a>
    </div>
    <nav class="main-nav">
      <a href="enseignement.php">Enseignement Supérieur</a>
      <a href="placement.php">Placement de Personnel</a>
      <a href="fdfp.php">Cabinet FDFP</a>
      <a href="contact.php">Contact</a>
    </nav>
    <div class="auth">
      <?php if ($user): ?>
        <span>Salut, <?=htmlspecialchars($user['first_name'])?></span>
        <a href="logout.php" class="btn small">Déconnexion</a>
        <?php if (is_admin()): ?>
          <a href="../admin/dashboard.php" class="btn small">Admin</a>
        <?php endif; ?>
      <?php else: ?>
        <a href="login.php" class="btn small">Connexion</a>
        <a href="register.php" class="btn small primary">Inscription</a>
      <?php endif; ?>
    </div>
  </div>
</header>
