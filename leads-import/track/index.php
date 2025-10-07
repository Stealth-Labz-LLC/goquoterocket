<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include_once '../credential.php';

$BASE_URI = "/leads-import/track/";
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
    $track_payload = $requestData['track_payload'];
    $track_response = $requestData['track_response'];
    $city = !empty($requestData['shippingCity']) ? $requestData['shippingCity'] : '';
    $firstname = $requestData['firstname'];
    $lastname = $requestData['lastname'];
    $phone1 = $requestData['phone1'];
    $optinurl = $requestData['optinurl'];
    $optindate = $requestData['optindate'];
    $acceptterms = $requestData['acceptterms'];
    $offer_id = $requestData['offer_id'];
    // Check if the record exists
    $check_duplicate = "SELECT id FROM track_rocket WHERE phone1='$phone1'";
    $result = $conn->query($check_duplicate);

    if ($result->num_rows > 0) {
        // Record exists, perform UPDATE
        $query = "UPDATE track_rocket SET  track_payload='$track_payload',track_response='$track_response' WHERE phone1='$phone1'";
        //file_put_contents('./insertleadlog_View' . time() . '.log', $query, FILE_APPEND);
    } else {
        // Record does not exist, perform INSERT
        $query = "INSERT INTO track_rocket (funnel_id,sid,aff_sub,aff_sub2,aff_sub3,track_payload,track_response,city,optindate,optinurl,firstname,lastname,phone1,acceptterms,offer_id)
    VALUES('$funnel_id','$sid','$aff_sub','$aff_sub2','$aff_sub3','$track_payload','$track_response','$city','$optindate','$optinurl','$firstname','$lastname','$phone1','$acceptterms','$offer_id')";
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
    $dripcell_payload = !empty($requestData['dripcell_payload']) ? $requestData['dripcell_payload'] : 'N/A';
    $dripcell_response = !empty($requestData['dripcell_response']) ? $requestData['dripcell_response'] : 'N/A';

    $auto_payload = !empty($requestData['auto_payload']) ? $requestData['auto_payload'] : 'N/A';
    $auto_response = !empty($requestData['auto_response']) ? $requestData['auto_response'] : 'N/A';

    $dripcellContactUpload = !empty($requestData['dripcellContactUpload']) ? $requestData['dripcellContactUpload'] : '0';
    $triggerAutoApi = !empty($requestData['triggerAutoApi']) ? $requestData['triggerAutoApi'] : '0';

    if ($dripcellContactUpload == 1) {
        $updateLeadData = "UPDATE track_rocket SET dripcell_payload='$dripcell_payload',dripcell_response='$dripcell_response',dripcellContactUpload='$dripcellContactUpload' WHERE phone1='$phone1'";
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
    if ($triggerAutoApi == 1) {
        $updateLeadData = "UPDATE track_rocket SET auto_payload='$auto_payload',auto_response='$auto_response',triggerAutoApi='$triggerAutoApi' WHERE phone1='$phone1'";
        //file_put_contents('./autoUpdateleadlog_View' . time() . '.log', $updateLeadData, FILE_APPEND);
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
$endpoints["updateAutoData"] = function (array $requestData): void {

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
    $auto_response = $requestData['auto_response'];

    $updateAutoData = "UPDATE track_rocket SET auto_response= '$auto_response', isAutoApiFired = '1' WHERE phone1='$phone1'";
    //file_put_contents('./updateTrackData' . time() . '.log', $updateAutoData, FILE_APPEND);
    if ($conn->query($updateAutoData) === TRUE) {
        $LeadResponse = array(
            "status" => "SUCCESS",
            "code" => 1,
            "status_message" => 'Leads Updated',
        );
        echo json_encode($LeadResponse);
        die();
    } else {
        $LeadResponse = array(
            "status" => "ERROR",
            "code" => 2,
            "status_message" => 'Failed Leads Update!',
        );
        echo json_encode($LeadResponse);
        die();
    }
};
//schedule task endpoint
$endpoints["scheduleTask"] = function (array $requestData): void {

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

    $triggerAutoApi = !empty($requestData['triggerAutoApi']) ? $requestData['triggerAutoApi'] : '0';

    if ($triggerAutoApi == 1) {
        // Query your_table to see if there is any row with status_column set to 1.
        $query = "SELECT * FROM track_rocket WHERE triggerAutoApi = '1' and phone1 = '$phone1' ";
        $result = $conn->query($query);

        if ($result) {
            if ($result->num_rows > 0) {

                $estTime = new DateTime('now', new DateTimeZone('America/New_York'));
                $estTime->modify('+45 minutes');
                $executeAutoApiAt = $estTime->format("Y-m-d H:i:s");

                $updateScheduleData = "UPDATE track_rocket SET scheduleAutoApi='1',executeAutoApiAt='$executeAutoApiAt' WHERE phone1='$phone1'";
                //file_put_contents('./dripcellupdateleadlog_View' . time() . '.log', $updateScheduleData, FILE_APPEND);
                if ($conn->query($updateScheduleData) === TRUE) {
                    $LeadResponse = array(
                        "status" => "SUCCESS",
                        "code" => 1,
                        "status_message" => 'Auto Schedule Updated',
                    );
                    echo json_encode($LeadResponse);
                    die();
                } else {
                    $LeadResponse = array(
                        "status" => "ERROR",
                        "code" => 2,
                        "status_message" => 'Failed Auto Schedule!',
                    );
                    echo json_encode($LeadResponse);
                    die();
                }
            }
            $result->free();
        } else {
            error_log("SELECT query error: " . $conn->error);
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
