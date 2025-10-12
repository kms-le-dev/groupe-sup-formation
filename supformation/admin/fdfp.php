<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isset($IN_DASHBOARD_PANEL)) $IN_DASHBOARD_PANEL = false;
// If this file is included from dashboard.php the variable $IN_DASHBOARD_PANEL
// will be set to true. Otherwise require admin access and redirect to login.
if (!is_admin()) {
  if (empty($IN_DASHBOARD_PANEL)) {
    header('Location: ../public/login.php');
    exit;
  }
}

// ensure a CSRF token is available for forms (dashboard provides one sometimes)
if (empty($csrf)) {
  $csrf = generate_csrf_token();
}

// Debug marker: visible in page source to confirm inclusion when loaded inside dashboard
echo "<!-- DEBUG: admin/fdfp.php included; IN_DASHBOARD_PANEL=" . ((empty($IN_DASHBOARD_PANEL)) ? '0' : '1') . " -->\n";

// handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && !headers_sent()) {
  $id = (int)$_POST['delete_id'];
  $token = $_POST['csrf_token'] ?? null;
  if (!verify_csrf_token($token)) { 
    $_SESSION['flash_error'] = 'Token CSRF invalide.'; 
    if (empty($IN_DASHBOARD_PANEL)) {
      header('Location: dashboard.php'); 
      exit;
    }
  } else {
    $stmt = $pdo->prepare('SELECT filename FROM fdfp_submissions WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      // resolve possible file locations
      $publicRoot = __DIR__ . '/../public/';
      $candidates = [
        $publicRoot . $row['filename'],
        $publicRoot . 'uploads/' . ltrim($row['filename'], '/'),
        $publicRoot . 'uploads/fdfp/' . basename($row['filename']),
        $publicRoot . 'uploads/' . basename($row['filename']),
      ];
      foreach ($candidates as $c) { if (file_exists($c)) { @unlink($c); break; } }
      $pdo->prepare('DELETE FROM fdfp_submissions WHERE id = ?')->execute([$id]);
      $_SESSION['flash_success'] = 'Soumission supprimée.';
      
      if (empty($IN_DASHBOARD_PANEL) && !headers_sent()) { 
        header('Location: dashboard.php'); 
        exit;
      }
    }
  }
}

// Fetch submissions with error reporting for easier debugging
$rows = [];
try {
  $stmt = $pdo->query('SELECT * FROM fdfp_submissions ORDER BY created_at DESC');
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  // store error in session flash to show in dashboard context
  $_SESSION['flash_error'] = 'Erreur lors de la récupération des soumissions FDFP: ' . $e->getMessage();
  $rows = [];
}
?>
<style>
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes slideIn {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
  }

  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
  }

  .admin-content {
    animation: fadeIn 0.5s ease-out;
  }

  .admin-content h2 {
    font-size: 1.875rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.75rem;
  }

  .admin-content h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    border-radius: 2px;
    transition: width 0.3s ease;
  }

  .admin-content h2:hover::after {
    width: 100px;
  }

  .panel-notice {
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    border: 1px solid #c7d2fe;
    border-radius: 8px;
    margin-bottom: 1rem;
    color: #1e293b;
    animation: slideIn 0.4s ease-out;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
  }

  .stats-box {
    margin: 0.75rem 0 1.5rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
    animation: fadeIn 0.6s ease-out;
  }

  .stats-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  }

  .stats-box strong {
    color: #3b82f6;
    font-size: 1.5rem;
    font-weight: 700;
  }

  .empty-state {
    padding: 2rem 1.5rem;
    background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
    border: 2px dashed #fed7aa;
    border-radius: 12px;
    color: #92400e;
    margin-bottom: 1.5rem;
    text-align: center;
    animation: pulse 2s ease-in-out infinite;
  }

  .empty-state code {
    background: rgba(146, 64, 14, 0.1);
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
  }

  .responsive-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    animation: fadeIn 0.7s ease-out;
  }

  .responsive-table thead {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
  }

  .responsive-table th {
    padding: 1rem 0.875rem;
    text-align: left;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    border-bottom: none;
  }

  .responsive-table tbody tr {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f1f5f9;
  }

  .responsive-table tbody tr:hover {
    background: linear-gradient(90deg, #f8fafc 0%, #f1f5f9 100%);
    transform: scale(1.01);
  }

  .responsive-table td {
    padding: 1rem 0.875rem;
    color: #334155;
    vertical-align: middle;
  }

  .responsive-table td a {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    display: inline-block;
  }

  .responsive-table td a:hover {
    color: #2563eb;
    transform: translateX(3px);
  }

  .file-path {
    font-size: 0.75rem;
    color: #94a3b8;
    margin-top: 0.5rem;
    font-family: 'Courier New', monospace;
    padding: 0.25rem 0.5rem;
    background: #f8fafc;
    border-radius: 4px;
    border-left: 3px solid #cbd5e1;
  }

  .actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    justify-content: flex-start;
  }

  .btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border: none;
    text-decoration: none;
    display: inline-block;
  }

  .btn.small {
    padding: 0.4rem 0.875rem;
    font-size: 0.8rem;
  }

  .btn:not(.danger) {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
  }

  .btn:not(.danger):hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
  }

  .btn.danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
  }

  .btn.danger:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
  }

  @media (max-width: 900px) {
    .responsive-table thead {
      display: none;
    }

    .responsive-table tbody tr {
      display: block;
      margin-bottom: 1.25rem;
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      padding: 1rem;
      border-bottom: none;
    }

    .responsive-table tbody tr:hover {
      transform: scale(1.02);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .responsive-table td {
      display: block;
      width: 100%;
      padding: 0.5rem 0;
      border: 0;
      text-align: left;
    }

    .responsive-table td::before {
      content: attr(data-label);
      font-weight: 700;
      display: inline-block;
      min-width: 120px;
      color: #475569;
      margin-right: 1rem;
    }

    .actions {
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
      margin-top: 1rem;
    }

      .actions a,
      .actions button {
        width: 70%;
        min-width: 120px;
        max-width: 180px;
        text-align: center;
        display: block;
      }
  }
</style>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>


<div class="admin-content container">
  <?php if (!empty($IN_DASHBOARD_PANEL)): ?>
    <div class="panel-notice">📊 Panel FDFP inclus depuis le dashboard</div>
  <?php endif; ?>

  <h2>Demandes de formation (FDFP)</h2>
  
  <?php
    $countRows = is_array($rows) ? count($rows) : 0;
  ?>
  <div class="stats-box">
    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #3b82f6;">
      <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
      <polyline points="14 2 14 8 20 8"></polyline>
    </svg>
    <span>Nombre d'enregistrements :</span>
    <strong><?= (int)$countRows ?></strong>
  </div>

  <?php if ($countRows === 0): ?>
    <div class="empty-state">
      <p><strong>Aucune demande trouvée.</strong></p>
    </div>
  <?php else: ?>
    <table class="responsive-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Fichier</th>
          <th>Société</th>
          <th>Email</th>
          <th>Contact</th>
          <th>NIF</th>
          <th>RCCM</th>
          <th>Créé</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td data-label="ID"><?= e($r['id']) ?></td>
            <?php
              $publicRoot = __DIR__ . '/../public/';
              if (file_exists($publicRoot . $r['filename'])) {
                $urlPath = ltrim($r['filename'], '/');
              } elseif (file_exists($publicRoot . 'uploads/' . ltrim($r['filename'], '/'))) {
                $urlPath = 'uploads/' . ltrim($r['filename'], '/');
              } elseif (file_exists($publicRoot . 'uploads/fdfp/' . basename($r['filename']))) {
                $urlPath = 'uploads/fdfp/' . basename($r['filename']);
              } else {
                $urlPath = ltrim($r['filename'], '/');
              }
              $downloadHref = (defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' : '../public/') . ltrim($urlPath, '/');
              
              $candidates = [
                $publicRoot . $r['filename'],
                $publicRoot . 'uploads/' . ltrim($r['filename'], '/'),
                $publicRoot . 'uploads/fdfp/' . basename($r['filename']),
                $publicRoot . 'uploads/' . basename($r['filename']),
              ];
              $foundPath = null;
              foreach ($candidates as $c) { if (file_exists($c)) { $foundPath = $c; break; } }
            ?>
            <td data-label="Fichier">
              <a href="<?= e($downloadHref) ?>" target="_blank">📄 <?= e(basename($r['filename'])) ?></a>
              <div class="file-path"><?= $foundPath ? 'Serveur: ' . e(str_replace(__DIR__ . '/../', '', $foundPath)) : '⚠️ Fichier non trouvé' ?></div>
            </td>
            <?php
              $decoded = [];
              if (!empty($r['meta'])) {
                $decoded = json_decode($r['meta'], true);
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                  $decoded = [];
                }
              }
              $company = isset($decoded['company']) ? e($decoded['company']) : '&mdash;';
              $email = isset($decoded['email']) ? e($decoded['email']) : '&mdash;';
              $contact = isset($decoded['contact']) ? e($decoded['contact']) : '&mdash;';
              $nif = isset($decoded['nif']) ? e($decoded['nif']) : '&mdash;';
              $rccm = isset($decoded['rccm']) ? e($decoded['rccm']) : '&mdash;';
            ?>
            <td data-label="Société"><?= $company ?></td>
            <td data-label="Email"><?= $email ?></td>
            <td data-label="Contact"><?= $contact ?></td>
            <td data-label="NIF"><?= $nif ?></td>
            <td data-label="RCCM"><?= $rccm ?></td>
            <td data-label="Créé"><?= e($r['created_at']) ?></td>
            <td data-label="Actions">
              <div class="actions">
                <a class="btn small" href="<?= '../public/uploads/fdfp/' . e(basename($r['filename'])) ?>" target="_blank">⬇️ Télécharger</a>
                <form method="post" action="fdfp.php" style="display:inline" onsubmit="return confirm('Supprimer cette soumission ?');">
                  <input type="hidden" name="delete_id" value="<?= e($r['id']) ?>">
                  <input type="hidden" name="csrf_token" value="<?= e(generate_csrf_token()) ?>">
                  <button class="btn small danger">🗑️ Supprimer</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>