<?php
header('Content-Type: application/json');

$licensesFile = 'licenses.json';

// Check if the file exists and is readable
if (!file_exists($licensesFile) || !is_readable($licensesFile)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "License file not found or unreadable."]);
    exit;
}

// Load and decode JSON data
$licensesData = json_decode(file_get_contents($licensesFile), true);
if (!isset($licensesData['licenses']) || !is_array($licensesData['licenses'])) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Invalid license file format."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
    exit;
}

// Parse JSON input
$input = json_decode(file_get_contents('php://input'), true);
$licenseKey = trim($input['license'] ?? '');
$domain = $_SERVER['HTTP_HOST'] ?? '';

if (empty($licenseKey)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "License key is required."]);
    exit;
}

// Convert licenses array into an associative array for faster lookup
$licensesMap = [];
foreach ($licensesData['licenses'] as $license) {
    $licensesMap[$license['key']] = $license;
}

// Validate license
if (!isset($licensesMap[$licenseKey])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["status" => "error", "message" => "Invalid license key."]);
    exit;
}

$license = $licensesMap[$licenseKey];

// Validate domain
if ($license['domain'] !== $domain) {
    http_response_code(403); // Forbidden
    echo json_encode(["status" => "error", "message" => "License key is not authorized for this domain."]);
    exit;
}

// Check license expiration
$expiryDate = strtotime($license['expires']);
$currentDate = time();

if ($expiryDate < $currentDate) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "License expired."]);
    exit;
}

// License is valid
echo json_encode(["status" => "success", "message" => "License is valid."]);
exit;

// header('Content-Type: application/json');

// // Load licenses from JSON
// $licensesFile = 'licenses.json';
// $licensesData = json_decode(file_get_contents($licensesFile), true);

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $input = json_decode(file_get_contents('php://input'), true);
//     $licenseKey = $input['license'] ?? '';
//     $domain = $_SERVER['HTTP_HOST']; // Get current domain

//     if (!$licenseKey) {
//         echo json_encode(["status" => "error", "message" => "License key is required."]);
//         exit;
//     }

//     foreach ($licensesData['licenses'] as $license) {
//         if ($license['key'] === $licenseKey && $license['domain'] === $domain) {
//             $expiryDate = strtotime($license['expires']);
//             $currentDate = time();

//             if ($expiryDate < $currentDate) {
//                 echo json_encode(["status" => "error", "message" => "License expired."]);
//             } else {
//                 echo json_encode(["status" => "success", "message" => "License valid."]);
//             }
//             exit;
//         }
//     }

//     echo json_encode(["status" => "error", "message" => "Invalid license."]);
// }

?>
