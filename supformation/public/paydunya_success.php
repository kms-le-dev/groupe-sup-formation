<?php
require_once __DIR__ . '/../includes/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Paiement réussi</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #00b09b, #96c93d);
      color: #333;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
    }

    .container {
      background: #fff;
      padding: 40px 50px;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      text-align: center;
      max-width: 400px;
      width: 90%;
      animation: fadeIn 0.8s ease-in-out;
    }

    h2 {
      color: #2ecc71;
      font-size: 1.8rem;
      margin-bottom: 15px;
    }

    p {
      font-size: 1rem;
      color: #555;
      margin-bottom: 25px;
    }

    a {
      display: inline-block;
      background: #2ecc71;
      color: white;
      text-decoration: none;
      padding: 12px 25px;
      border-radius: 30px;
      font-weight: bold;
      transition: 0.3s;
    }

    a:hover {
      background: #27ae60;
      transform: scale(1.05);
    }

    .checkmark {
      font-size: 4rem;
      color: #2ecc71;
      margin-bottom: 10px;
      animation: pop 0.6s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pop {
      0% { transform: scale(0.5); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="checkmark">✅</div>
    <h2>Paiement réussi</h2>
    <p>Merci pour votre paiement.<br>Votre inscription est confirmée avec succès.</p>
    <a href="index.php">Retour à l'accueil</a>
  </div>
</body>
</html>
