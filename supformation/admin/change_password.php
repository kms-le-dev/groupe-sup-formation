<?php
// admin/change_password.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!is_admin()) { http_response_code(403); echo "Accès refusé."; exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Méthode non autorisée"; exit; }

$csrf = $_POST['csrf'] ?? '';
if (!verify_csrf_token($csrf)) { http_response_code(400); echo "Token CSRF invalide"; exit; }

$user_id = (int)($_POST['user_id'] ?? 0);
$new_password = trim($_POST['new_password'] ?? '');

if ($user_id <= 0 || $new_password === '') {
    header('Location: dashboard.php?msg=invalid'); exit;
}

// Hash password
$hash = password_hash($new_password, PASSWORD_BCRYPT);

try {
    // Detect column name used for password (password or password_hash)
    $cols = $pdo->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_ASSOC);
    $pwdCol = 'password';
    foreach ($cols as $c) {
        if (in_array($c['Field'], ['password_hash','password'])) { $pwdCol = $c['Field']; break; }
    }

    $stmt = $pdo->prepare("UPDATE users SET {$pwdCol} = :h WHERE id = :id");
    $stmt->execute([':h'=>$hash, ':id'=>$user_id]);
    header('Location: dashboard.php?msg=pass_ok');
    exit;
} catch (PDOException $e) {
    header('Location: dashboard.php?msg=pass_err');
    exit;
}
