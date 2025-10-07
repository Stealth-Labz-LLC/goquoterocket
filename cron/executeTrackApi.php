<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include_once '../leads-import/credential.php';

// DB Connection Setup
$connectionData = [
    'db_host' => DB_HOST,
    'db_username' => DB_USERNAME,
    'db_password' => DB_PASSWORD,
    'db_name' => DB_NAME
];

try {
    $conn = new mysqli(
        $connectionData['db_host'],
        $connectionData['db_username'],
        $connectionData['db_password'],
        $connectionData['db_name']
    );

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    $estTime = new DateTime('now', new DateTimeZone('America/New_York'));
    $currentTime = $estTime->format('Y-m-d H:i:s');

    // Query scheduled_tasks for tasks to execute
    $query = "SELECT id, phone1, track_payload FROM auto_rocket WHERE executeTrackApiAt <= '$currentTime' AND scheduletrackApi = '1' AND isTrackApiFired = '0'";
    // echo $query;
    // die();
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("Error executing query: " . $conn->error);
    }

    while ($task = $result->fetch_assoc()) {
        try {
            $trackPayload = json_decode($task['track_payload'], true);
            $phone1 = $task['phone1'];
            trackApiCall($trackPayload, $phone1, $connectionData);
        } catch (Exception $e) {
            $updateQuery = "UPDATE auto_rocket SET error_log = 'Function Failed' WHERE phone1 = " . $phone1;
        }
    }
    $result->free();
    $conn->close();
} catch (Exception $e) {
    error_log("General error: " . $e->getMessage(), 3, "./error_log.log");
}

function trackApiCall($trackPayload, $phone1, $connectionData)
{
    try {

        $conn = new mysqli(
            $connectionData['db_host'],
            $connectionData['db_username'],
            $connectionData['db_password'],
            $connectionData['db_name']
        );

        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        $trackCurl = curl_init();
        curl_setopt_array($trackCurl, [
            CURLOPT_URL => 'https://returnxdigital.leadbyte.co.uk/api/submit.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($trackPayload),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
        ]);
        $trackResponse = curl_exec($trackCurl);
        curl_close($trackCurl);
        $trackResponseData = json_decode($trackResponse);
        // Lead import API
        if ($trackResponseData->code !== -2) {
            $leadUpdateData = [
                'token' => AUTH_TOKEN,
                'phone1' => $phone1,
                'track_response' => $trackResponse
            ];
            $leadUpdateCurl = curl_init();
            curl_setopt_array($leadUpdateCurl, [
                CURLOPT_URL => 'https://quoterocket.co.za/leads-import/updateTrackData',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query($leadUpdateData),
                CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded']
            ]);
            $LeadUpdateResponse = curl_exec($leadUpdateCurl);
            if ($LeadUpdateResponse === false) {
                throw new Exception("cURL error: " . curl_error($leadUpdateCurl));
            }
            curl_close($leadUpdateCurl);
            echo $LeadUpdateResponse;
        }
        // Update table status after API execution
        $updateQuery = "UPDATE auto_rocket SET isTrackApiFired = '1' WHERE id = " . $phone1;
        if (!$conn->query($updateQuery)) {
            $updateQuery = "UPDATE auto_rocket SET error_log = '$conn->error' WHERE phone1 = " . $phone1;
        }
    } catch (Exception $e) {
        error_log("Task processing error: " . $e->getMessage(), 3, "./error_log.log");
    }
}
