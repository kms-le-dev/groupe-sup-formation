<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Vérifier qu'un domaine a été fourni
$domaine = $_GET['domaine'] ?? '';
if (!in_array($domaine, ['enseignement','placement','fdfp'])) {
    die("Domaine invalide");
}

// ID de l'utilisateur connecté
if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour vous inscrire à cette formation.");
}

$user_id = $_SESSION['user_id'];

// Clés PayDunya (sandbox)
$api_key = 'YOUR_PAYDUNYA_API_KEY';
$secret_key = 'YOUR_PAYDUNYA_SECRET_KEY';
$mode = 'sandbox'; // ou 'live'

// Montant et description
$montant = 50000; // Exemple montant CFA
$description = "Inscription au domaine: $domaine";

// URL callback et return
$callback_url = "http://localhost/supformation/public/paydunya_callback.php";
$return_url = "http://localhost/supformation/public/paydunya_success.php";

// Création de la session de paiement via API PayDunya
$data = [
    "store_name" => "SupFormation",
    "total_amount" => $montant,
    "currency" => "XOF",
    "cancel_url" => $return_url,
    "return_url" => $return_url,
    "callback_url" => $callback_url,
    "items" => [
        [
            "name" => $domaine,
            "quantity" => 1,
            "price" => $montant
        ]
    ],
    "custom_data" => [
        "user_id" => $user_id,
        "domaine" => $domaine
    ]
];

// Convertir en JSON
$json_data = json_encode($data);

// Initialiser cURL
$ch = curl_init("https://app.paydunya.com/checkout-invoice/create");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $api_key"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['response_text']) && $result['response_text'] === "Invoice created") {
    // Redirection vers la page de paiement PayDunya
    $invoice_url = $result['checkout_url'];
    header("Location: $invoice_url");
    exit;
} else {
    echo "<h2>Erreur lors de la création du paiement :</h2>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
}
