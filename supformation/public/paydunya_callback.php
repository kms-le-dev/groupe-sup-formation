<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

if (!$data) {
    http_response_code(400);
    exit("Aucune donnée reçue");
}

$status = $data['status'] ?? '';
$invoice_id = $data['token'] ?? '';
$custom_data = $data['custom_data'] ?? [];

if ($status === 'completed' && isset($custom_data['user_id'], $custom_data['domaine'])) {
    $user_id = (int)$custom_data['user_id'];
    $domaine = $custom_data['domaine'];
    $amount = $data['total_amount'] ?? 0;
    $currency = $data['currency'] ?? 'XOF';
    $provider = 'PayDunya';
    $provider_txn_id = $data['transaction_id'] ?? '';
    $metadata = json_encode($custom_data);

    $stmt = $pdo->prepare("INSERT INTO payments 
        (user_id, enrollment_id, amount, currency, provider, provider_txn_id, status, metadata, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$user_id, $domaine, $amount, $currency, $provider, $provider_txn_id, $status, $metadata]);

    http_response_code(200);
    echo "Paiement enregistré ✅";
} else {
    http_response_code(400);
    echo "Paiement non validé ou données manquantes";
}
