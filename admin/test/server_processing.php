<?php
session_start();
include_once '../credential.php';
$siteAccessCode = AUTH_CODE;
//echo '<pre>'; print_r($_SESSION['authKey']); die();
if ($_SESSION['authKey'] !== $siteAccessCode) {
    header('Location: /admin/index.php');
}

$connectionData = [
    'db_host' => DB_HOST,
    'db_username' => DB_USERNAME,
    'db_password' => DB_PASSWORD,
    'db_name' => DB_NAME
];
$mysqli = new mysqli(
    $connectionData['db_host'],
    $connectionData['db_username'],
    $connectionData['db_password'],
    $connectionData['db_name']
);
if ($mysqli->connect_error) {
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

// Base query
$query = "SELECT id, aff_sub, aff_sub2 FROM main_site";
$totalQuery = "SELECT COUNT(*) FROM main_site";

// Apply search filter
if (!empty($search)) {
    $query .= " WHERE aff_sub LIKE ? OR aff_sub2 LIKE ?";
    $totalQuery .= " WHERE aff_sub LIKE ? OR aff_sub2 LIKE ?";
}

// Add pagination
$query .= " LIMIT ?, ?";
$stmt = $mysqli->prepare($query);
$totalStmt = $mysqli->prepare($totalQuery);

// Bind parameters
if (!empty($search)) {
    $likeSearch = "%$search%";
    $stmt->bind_param("ssii", $likeSearch, $likeSearch, $start, $length);
    $totalStmt->bind_param("ss", $likeSearch, $likeSearch);
} else {
    $stmt->bind_param("ii", $start, $length);
}

// Execute queries
$stmt->execute();
$result = $stmt->get_result();

$totalStmt->execute();
$totalRecords = $totalStmt->get_result()->fetch_row()[0];

// Fetch data
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Respond in DataTables format
echo json_encode([
    "draw" => intval($draw),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data,
]);
