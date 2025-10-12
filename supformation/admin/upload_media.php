<?php
session_start();
require_once "../includes/config.php";

if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 1) {
    header("Location: ../public/login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["media"])) {
    $file = $_FILES["media"];
    $targetDir = "../public/uploads/";

    // sécurité : taille max 5MB
    if ($file["size"] > 5*1024*1024) {
        $message = "Fichier trop volumineux (max 5MB)";
    } else {
        $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg","jpeg","png","gif","mp4","pdf"];

        if (!in_array($ext, $allowed)) {
            $message = "Extension non autorisée.";
        } else {
            $filename = time() . "_" . basename($file["name"]);
            $targetFile = $targetDir . $filename;

            if (move_uploaded_file($file["tmp_name"], $targetFile)) {
                $message = "Fichier uploadé avec succès : " . htmlspecialchars($filename);
            } else {
                $message = "Erreur lors de l'upload.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Upload Media - Admin</title>
  <link rel="stylesheet" href="../public/assets/css/styles.css">
</head>
<body>
    
<h2>Upload de média</h2>

<?php if (!empty($message)): ?>
    <div class="alert"><?= $message ?></div>
<?php endif; ?>

<form action="" method="POST" enctype="multipart/form-data">
    <label for="media">Choisir un fichier (image, vidéo, pdf)</label>
    <input type="file" name="media" id="media" required>
    <button type="submit">Uploader</button>
</form>

<p><a href="dashboard.php">⬅ Retour au dashboard</a></p>
</body>
</html>
