<?php
// supformation/admin/delete_user.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// only admin can delete users
if (!is_admin()) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Accès refusé.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php?msg=invalid');
    exit;
}

$csrf = $_POST['csrf'] ?? '';
if (!verify_csrf_token($csrf)) {
    header('Location: dashboard.php?msg=csrf');
    exit;
}

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
if ($user_id <= 0) {
    header('Location: dashboard.php?msg=invalid');
    exit;
}

// Prevent deleting yourself
if (!empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === $user_id) {
    header('Location: dashboard.php?msg=cannot_self');
    exit;
}

try {
    $pdo->beginTransaction();

    // Delete user row
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute([':id' => $user_id]);

    if ($stmt->rowCount() === 0) {
        // nothing deleted
        $pdo->rollBack();
        header('Location: dashboard.php?msg=not_found');
        exit;
    }

    $pdo->commit();
    header('Location: dashboard.php?msg=delete_ok');
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('delete_user error: ' . $e->getMessage());
    header('Location: dashboard.php?msg=delete_err');
    exit;
}
