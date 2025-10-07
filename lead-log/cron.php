<?php

ini_set('display_errors', '1');

ini_set('display_startup_errors', '1');

error_reporting(E_ALL);

include_once 'credential.php';

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$cutoffDate = date('Y-m-d H:i:s', strtotime('-180 days'));

$query = "DELETE FROM auto_rocket_log WHERE created_at < ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $cutoffDate);
$stmt->execute();
if ($stmt->affected_rows > 0) {

    echo "Old records deleted successfully.";

} else {

    echo "No old records to delete.";

}
$stmt->close();
$conn->close();



?>

