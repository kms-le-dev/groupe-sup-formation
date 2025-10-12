<?php
require_once __DIR__ . '/../includes/config.php';

$input = file_get_contents("php://input");
$data = json_decode($input, true);

file_put_contents(__DIR__ . "/paydunya_log.txt", date('c')." | ".print_r($data, true)."\n", FILE_APPEND);

if (!empty($data['invoice']['token']) && $data['status'] === "completed") {
    // Tu peux mettre à jour ta base de données ici
    http_response_code(200);
    echo "OK";
} else {
    http_response_code(400);
    echo "INVALID";
}
