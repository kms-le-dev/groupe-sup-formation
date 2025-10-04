<?php
// admin/change_role.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!is_admin()) { http_response_code(403); echo "Accès refusé."; exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Méthode non autorisée"; exit; }

$csrf = $_POST['csrf'] ?? '';
if (!verify_csrf_token($csrf)) { header('Location: dashboard.php?msg=csrf'); exit; }

$user_id = (int)($_POST['user_id'] ?? 0);
$role_id = (int)($_POST['role_id'] ?? 0);

if ($user_id <= 0 || $role_id <= 0) { header('Location: dashboard.php?msg=invalid'); exit; }

try {
    $stmt = $pdo->prepare("UPDATE users SET role_id = :r WHERE id = :id");
    $stmt->execute([':r'=>$role_id, ':id'=>$user_id]);
    header('Location: dashboard.php?msg=role_ok');
    exit;
} catch (PDOException $e) {
    header('Location: dashboard.php?msg=role_err');
    exit;
}
