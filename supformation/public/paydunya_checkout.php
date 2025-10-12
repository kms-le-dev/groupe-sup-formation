<?php
require_once __DIR__ . '/../includes/config.php';

// Vérifier les données minimales
if (!isset($_GET['montant']) || !is_numeric($_GET['montant'])) {
    http_response_code(400);
    exit('Montant invalide.');
}

$montant = (int) $_GET['montant'];
$description = 'Frais d\'inscription SUP\'FORMATION';
$nomClient = 'Étudiant(e)';

// Créer une session PayDunya Checkout
$payload = [
    "invoice" => [
        "items" => [
            [
                "name" => $description,
                "quantity" => 1,
                "unit_price" => $montant,
                "total_price" => $montant
            ]
        ],
        "total_amount" => $montant,
        "description" => $description
    ],
    "store" => [
        "name" => "GROUPE SUP'FORMATION",
        "tagline" => "Paiement en ligne des frais d'inscription",
        "postal_address" => "Côte d'Ivoire",
        "phone" => "+2250505051570",
        "website_url" => "http://www.groupesupformation.com",
        "logo_url" => "https://www.groupesupformation.com/logo.png"
    ],
    "actions" => [
        "cancel_url" => PAYDUNYA_CANCEL_URL,
        "return_url" => PAYDUNYA_SUCCESS_URL,
        "callback_url" => PAYDUNYA_CALLBACK_URL
    ],
    "custom_data" => [
        "etudiant" => $nomClient
    ]
];

// Envoyer la requête à PayDunya
$ch = curl_init("https://app.paydunya.com/sandbox-api/v1/checkout-invoice/create");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "PAYDUNYA-MASTER-KEY: " . PAYDUNYA_MASTER_KEY,
    "PAYDUNYA-PRIVATE-KEY: " . PAYDUNYA_PRIVATE_KEY,
    "PAYDUNYA-TOKEN: " . PAYDUNYA_TOKEN
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['response_code']) && $data['response_code'] === "00") {
    // Rediriger l'utilisateur vers la page de paiement
    header("Location: " . $data['response_text']);
    exit;
} else {
    echo "<h3>Erreur PayDunya :</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
