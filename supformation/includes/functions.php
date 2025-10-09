<?php
// includes/functions.php
require_once __DIR__ . '/config.php';

/* CSRF */
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function verify_csrf_token($token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}

/**
 * Vérifie le token CSRF envoyé via POST et arrête l'exécution si invalide.
 * Usage: verify_csrf_token_or_die();
 */
function verify_csrf_token_or_die(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (!$token || !verify_csrf_token($token)) {
        http_response_code(403);
        echo "CSRF token invalide";
        exit;
    }
}

/**
 * Vérifie le token CSRF et redirige vers une page de login si invalide (optionnel).
 */
function verify_csrf_token_or_redirect(string $redirect = '/'): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (!$token || !verify_csrf_token($token)) {
        header('Location: ' . $redirect);
        exit;
    }
}

/* Auth helpers */
function current_user(): ?array {
    global $pdo;
    if (!empty($_SESSION['user_id'])) {
        // Select all and map in PHP to avoid SQL errors when columns differ between environments
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        // map names
        $first = $row['prenom'] ?? $row['first_name'] ?? ($row['prenom'] ?? '');
        $last = $row['nom'] ?? $row['last_name'] ?? ($row['nom'] ?? '');
        return [
            'id' => $row['id'],
            'role_id' => $row['role_id'] ?? null,
            'first_name' => $first,
            'last_name' => $last,
            'email' => $row['email'] ?? null,
        ];
    }
    return null;
}
function is_admin(): bool {
    $u = current_user();
    // Application requirement: only role_id === 2 is admin
    return $u && isset($u['role_id']) && (int)$u['role_id'] === 2;
}

/* HTML escape helper */
/**
 * Escape a string for safe HTML output.
 *
 * @param mixed $value
 * @return string
 */
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* Safe upload */
function safe_upload($file, $targetDir = __DIR__ . '/../public/uploads/', $allowed = null): array {
    // allowed can be overridden; default list expanded to common image/video types
    if ($allowed === null) {
        $allowed = [
            'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml',
            'application/pdf', 'video/mp4'
        ];
    }
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['error'=>'Upload failed'];
    }
    $mime = @mime_content_type($file['tmp_name']);
    if ($mime === false) $mime = '';
    if (!in_array($mime, $allowed)) {
        return ['error'=>'Type de fichier non autorisé'];
    }
    // increase limit for video files
    $maxBytes = 5 * 1024 * 1024;
    if (strpos($mime, 'video/') === 0) {
        $maxBytes = 50 * 1024 * 1024; // 50MB for videos
    }
    if ($file['size'] > $maxBytes) {
        return ['error'=>'Fichier trop volumineux (max ' . round($maxBytes/1024/1024) . 'MB)'];
    }
    if (!is_dir($targetDir)) mkdir($targetDir,0755,true);
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = $targetDir . $basename;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return ['error'=>'Impossible de déplacer le fichier'];
    return ['path'=>$dest, 'filename'=>$basename];
}

/* Mail sending using PHPMailer (composer) */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_mail($to, $subject, $htmlBody, $altBody = ''): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject; 
        $mail->Body = $htmlBody;
        $mail->AltBody = $altBody;
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
        return false;
    }
}
