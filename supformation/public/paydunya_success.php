<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo "Vous devez être connecté pour voir cette page.";
    exit;
}

// Récupérer le dernier paiement de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM payments WHERE user_id=? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Merci pour votre inscription ! 🎉</h1>

<?php if ($payment): ?>
    <p>Domaine : <b><?= e($payment['enrollment_id']) ?></b></p>
    <p>Montant : <?= number_format($payment['amount'], 0, ',', ' ') ?> <?= e($payment['currency']) ?></p>
    <p>Transaction ID : <?= e($payment['provider_txn_id']) ?></p>
    <p>Statut : <?= e($payment['status']) ?></p>
<?php else: ?>
    <p>Aucun paiement récent trouvé.</p>
<?php endif; ?>

<a href="index.php" class="btn">Retour à l'accueil</a>

<?php include __DIR__ . '/../includes/footer.php'; ?>
