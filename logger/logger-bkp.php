<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Get raw POST data
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true);

// Check if the data was parsed successfully
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid JSON data"
    ]);
    http_response_code(400);
    exit();
}

$data['token'] = 'm-f7fd694964k8b165ffeb3afc65eb27a4';

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://quoterocket.co.za/lead-log/writeMainLog',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded',
        'Access-Control-Allow-Origin: *',
    ),
));
$response = curl_exec($curl);
curl_close($curl);
echo $response;
