<?php
header('Content-Type: application/json');

// Load licenses from JSON
$licensesFile = 'licenses.json';
$licensesData = json_decode(file_get_contents($licensesFile), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $licenseKey = $input['license'] ?? '';
    $domain = $_SERVER['HTTP_HOST']; // Get current domain

    if (!$licenseKey) {
        echo json_encode(["status" => "error", "message" => "License key is required."]);
        exit;
    }

    foreach ($licensesData['licenses'] as $license) {
        if ($license['key'] === $licenseKey && $license['domain'] === $domain) {
            $expiryDate = strtotime($license['expires']);
            $currentDate = time();

            if ($expiryDate < $currentDate) {
                echo json_encode(["status" => "error", "message" => "License expired."]);
            } else {
                echo json_encode(["status" => "success", "message" => "License valid."]);
            }
            exit;
        }
    }

    echo json_encode(["status" => "error", "message" => "Invalid license."]);
}
?>
