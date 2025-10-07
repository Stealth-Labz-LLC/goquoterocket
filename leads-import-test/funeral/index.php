<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include_once '../credential.php';

$BASE_URI = "/leads-import/funeral/";
$endpoints = array();
$requestData = array();

//file_put_contents('./insertleadlog_View' . time() . '.log', 'api', FILE_APPEND);

//collect incoming parameters
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        $requestData = $_POST;
        break;
    case 'GET':
        $requestData = $_GET;
        break;
    case 'DELETE':
        $requestData = $_DELETE;
        break;
    case 'PUT':
    case 'PATCH':
        parse_str(file_get_contents('php://input'), $requestData);
        //if the information received cannot be interpreted as an arrangement it is ignored.
        if (!is_array($requestData)) {
            $requestData = array();
        }

        break;
    default:
        //TODO: implement here any other type of request method that may arise.
        break;
}

//If the token is sent in a header X-API-KEY
if (isset($_SERVER["HTTP_X_API_KEY"])) {
    $requestData["token"] = $_SERVER["HTTP_X_API_KEY"];
}

$parsedURI = parse_url($_SERVER["REQUEST_URI"]);
$endpointName = str_replace($BASE_URI, "", $parsedURI["path"]);

if (empty($endpointName)) {
    $endpointName = "/";
}

// closures to define each endpoint logic
/**
 * prints a default message if the API base path is queried.
 * @param array $requestData contains the parameters sent in the request, for this endpoint they are ignored.
 * @return void
 */
$endpoints["/"] = function (array $requestData): void {
    echo json_encode("Welcome to my API!-" . $requestData["name"]);
};

/**
 * prints a greeting message with the name specified in the $requestData["name"] item.
 * if the variable is empty a default name is used.
 * @param array $requestData this array must contain an item with key "name" 
 * if you want to display a custom name in the greeting.
 * @return void
 */
$endpoints["addData"] = function (array $requestData): void {

    if (!isset($requestData["name"])) {
        $requestData["name"] = "Misterious masked individual";
    }
    //DB Connection Set up
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
    //file_put_contents('./insertleadlog_View' . time() . '.log', $requestData['phone1'], FILE_APPEND);
    $sid = $requestData['sid'];
    $aff_sub = $requestData['aff_sub'];
    $aff_sub2 = $requestData['aff_sub2'];
    $aff_sub3 = !empty($requestData['aff_sub3']) ? $requestData['aff_sub3'] : 'N/A';
    $funnel_id = $requestData['funnel_id'];
    $funeral_payload = $requestData['funeral_payload'];
    $funeral_response = $requestData['funeral_response'];
    $city = !empty($requestData['city']) ? $requestData['city'] : '';
    $firstname = $requestData['firstname'];
    $lastname = $requestData['lastname'];
    $phone1 = $requestData['phone1'];
    $optinurl = $requestData['optinurl'];
    $optindate = $requestData['optindate'];
    $acceptterms = $requestData['acceptterms'];
    $offer_id = $requestData['offer_id'];
    $preferredtimeofcall = $requestData['preferredtimeofcall'];
    // Check if the record exists
    $check_duplicate = "SELECT id FROM funeral_rocket WHERE phone1='$phone1'";
    $result = $conn->query($check_duplicate);

    if ($result->num_rows > 0) {
        // Record exists, perform UPDATE
        $query = "UPDATE funeral_rocket SET  funeral_payload='$funeral_payload',funeral_response='$funeral_response' WHERE phone1='$phone1'";
        //file_put_contents('./insertleadlog_View' . time() . '.log', $query, FILE_APPEND);
    } else {
        // Record does not exist, perform INSERT
        $query =  "INSERT INTO funeral_rocket(funnel_id,sid,aff_sub,aff_sub2,aff_sub3,funeral_payload,funeral_response,city,optindate,optinurl,firstname,lastname,phone1,acceptterms,offer_id,preferredtimeofcall)
    VALUES('$funnel_id','$sid','$aff_sub','$aff_sub2','$aff_sub3','$funeral_payload','$funeral_response','$city','$optindate','$optinurl','$firstname','$lastname','$phone1','$acceptterms','$offer_id','$preferredtimeofcall')";
        //echo $insertQuery;
        //file_put_contents('./insertleadlog_View' . time() . '.log', $query, FILE_APPEND);
    }
    //END


    if ($conn->query($query) === TRUE) {
        $LeadResponse = array(
            "status" => "SUCCESS",
            "Code" => 1,
            "status_message" => 'Leads Imported',
        );
        echo json_encode($LeadResponse);
        die();
    } else {
        $LeadResponse = array(
            "status" => "ERROR",
            "Code" => 2,
            "status_message" => 'Failed Leads Import!',
        );
        echo json_encode($LeadResponse);
        die();
    }
};
$endpoints["updateData"] = function (array $requestData): void {

    if (!isset($requestData["phone1"])) {
        $requestData["phone"] = "Misterious masked individual";
    }
    //DB Connection Set up
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
    $phone1 = $requestData['phone1'];
    $dripcell_payload = $requestData['dripcell_payload'];
    $dripcell_response = $requestData['dripcell_response'];


    $dripcellContactUpload = !empty($requestData['dripcellContactUpload']) ? $requestData['dripcellContactUpload'] : '0';

    if ($dripcellContactUpload == 1) {
        $updateLeadData = "UPDATE funeral_rocket SET dripcell_payload='$dripcell_payload',dripcell_response='$dripcell_response',dripcellContactUpload='$dripcellContactUpload' WHERE phone1='$phone1'";
        //file_put_contents('./dripcellupdateleadlog_View' . time() . '.log', $updateLeadData, FILE_APPEND);
        if ($conn->query($updateLeadData) === TRUE) {
            $LeadResponse = array(
                "status" => "SUCCESS",
                "Code" => 1,
                "status_message" => 'Leads Updated',
            );
            echo json_encode($LeadResponse);
            die();
        } else {
            $LeadResponse = array(
                "status" => "ERROR",
                "Code" => 2,
                "status_message" => 'Failed Leads Update!',
            );
            echo json_encode($LeadResponse);
            die();
        }
    }
};
/**
 * prints a default message if the endpoint path does not exist.
 * @param array $requestData contains the parameters sent in the request, 
 *                           for this endpoint they are ignored.
 * @return void
 */
$endpoints["404"] = function ($requestData): void {

    echo json_encode("Endpoint " . $requestData["endpointName"] . " not found.");
};

/**
 * checks if the token is valid, and prevents the execution of 
 * the requested endpoint.
 * @param array $requestData contains the parameters sent in the request, 
 *                           for this endpoint is required an item with 
 *                           key "token" that contains the token
 *                           received to authenticate and authorize 
 *                           the request.
 * @return void
 */

$endpoints["checktoken"] = function ($requestData): void {
    $AccessToken = AUTH_TOKEN;
    $tokens = array(
        $AccessToken => ""
    );

    //echo '<pre>'; print_r($tokens);die();
    if (!isset($requestData["token"])) {
        echo json_encode("No token was received to authorize the operation. Verify the information sent");
        exit;
    }

    if (!isset($tokens[$requestData["token"]])) {
        echo json_encode("The token  " . $requestData["token"] .
            " does not exists or is not authorized to perform this operation.");

        exit;
    }
};

//we define the response encoding, by default we will use json
header("Content-Type: application/json; charset=UTF-8");

if (isset($endpoints[$endpointName])) {
    $endpoints["checktoken"]($requestData);
    $endpoints[$endpointName]($requestData);
} else {
    $endpoints["404"](array("endpointName" => $endpointName));
}
