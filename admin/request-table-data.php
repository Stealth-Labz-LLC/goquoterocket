<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include_once 'credential.php';


$connectionData = [
    'db_host' => DB_HOST,
    'db_username' => DB_USERNAME,
    'db_password' => DB_PASSWORD,
    'db_name' => DB_NAME
];
$conn = new mysqli(
    $connectionData['db_host'],
    $connectionData['db_username'],
    $connectionData['db_password'],
    $connectionData['db_name']
);
if ($conn->connect_error) {
    echo 'Something went wrong try again!';
    die();
    //die("Connection failed: " . $conn->connect_error);
}


/// Database connection
//$mysqli = new mysqli("localhost", "username", "password", "database");

// Get parameters from the client
$draw = $_GET['draw'] ?? 1;
$start = $_GET['start'] ?? 0;
$length = $_GET['length'] ?? 10;
$search = $_GET['search']['value'] ?? '';

// Parameters from DataTable
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$length = isset($_GET['length']) ? intval($_GET['length']) : 10;
$searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
//exclude
$excludeTest = isset($_GET['exclude_test']) ? $_GET['exclude_test'] : 'false';
$tableName = isset($_GET['table_name']) ? $_GET['table_name'] : 'main_site';

$columnMapping = [
    'main_site' => ['api_payload', 'api_response'],
    'legal_rocket' => ['legal_payload', 'legal_response'],
    'pet_rocket' => ['pet_payload', 'pet_response'],
    'life_rocket' => ['life_payload', 'life_response'],
    'business_rocket' => ['business_payload', 'business_response'],
    'health_rocket' => ['medical_aid_payload', 'medical_aid_response'],
    'funeral_rocket' => ['funeral_payload', 'funeral_response'],
    'loan_rocket' => ['loan_payload', 'loan_response'],
    'debt_rocket' => ['debt_payload', 'debt_response'],
    'auto_rocket' => ['insurance_payload', 'insurance_response'],
    'track_rocket' => ['track_payload', 'track_response'],
    'motor_rocket' => ['motor_payload', 'motor_response'],
];

if (array_key_exists($tableName, $columnMapping)) {
    $Apicolumns = implode(',', $columnMapping[$tableName]);
    $columnLists = explode(',', $Apicolumns);
    $apiPayloads = $columnLists[0];
    $apiResponse = $columnLists[1];
} else {
    $apiPayloads = 'api_payload';
    $apiResponse = 'api_response';
}

// Search filter
$searchQuery = "";
if (!empty($searchValue)) {
    $searchQuery = "WHERE $apiPayloads LIKE '%$searchValue%' OR  $apiResponse LIKE '%$searchValue%' OR aff_sub LIKE '%$searchValue%' OR aff_sub2 LIKE '%$searchValue%'";
}
if (!empty($excludeTest) && $excludeTest == 'true') {
    if (!empty($searchValue)) {
        $searchQuery = "WHERE $apiPayloads LIKE '%$searchValue%' OR  $apiResponse LIKE '%$searchValue%' OR aff_sub LIKE '%$searchValue%' OR aff_sub2 LIKE '%$searchValue%' AND firstname NOT LIKE '%test%'";
    } else {
        $searchQuery = " WHERE firstname NOT LIKE '%test%' AND lastname NOT LIKE '%test%' "; // Replace `column_name` with your specific column
    }
}
$totalQuery = "SELECT COUNT(*) as total FROM $tableName";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];

// Get filtered records
$filteredQuery = "SELECT COUNT(*) as filtered FROM $tableName $searchQuery";
$filteredResult = $conn->query($filteredQuery);
$filteredRow = $filteredResult->fetch_assoc();
$filteredRecords = $filteredRow['filtered'];

// Fetch data
$dataQuery = "SELECT * FROM $tableName $searchQuery  ORDER BY id DESC LIMIT $start, $length";
$dataResult = $conn->query($dataQuery);
$data = [];
$leadId = [];
while ($row = $dataResult->fetch_assoc()) {

    $api_response = json_decode($row[$apiResponse]);
    $row['leadId'] = !empty($api_response->leadId) ? $api_response->leadId : '';
    $row['fullName'] = $row['firstname'] . ' ' . $row['lastname'];
    $data[] = $row;
}

// Prepare response
$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data,
    "query" => $dataQuery,
];

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
