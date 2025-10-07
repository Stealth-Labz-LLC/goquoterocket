<?php
ini_set('display_errors', '1');

ini_set('display_startup_errors', '1');

error_reporting(E_ALL);

include_once 'credential.php';



$BASE_URI = "/lead-log/";

$endpoints = array();

$requestData = array();

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

//End point for auto rocket log

$endpoints["writeLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO auto_rocket_log (log_payload, user_is_at, funnel_name) VALUES (?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("sss", $logPayload, $userIsAt, $funnelName);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
};

//Endpoint for health rocket log
$endpoints["writeHealthLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO health_rocket_log (log_payload, user_is_at, funnel_name) VALUES (?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("sss", $logPayload, $userIsAt, $funnelName);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
};
//Endpoint for debt rocket log
$endpoints["writeDebtLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO debt_rocket_log (log_payload, user_is_at, funnel_name) VALUES (?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("sss", $logPayload, $userIsAt, $funnelName);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
};
//Endpoint for loan rocket log
$endpoints["writeLoanLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO loan_rocket_log (log_payload, user_is_at, funnel_name) VALUES (?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("sss", $logPayload, $userIsAt, $funnelName);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
};
//Endpoint for track rocket log
$endpoints["writeTrackLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO track_rocket_log (log_payload, user_is_at, funnel_name) VALUES (?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("sss", $logPayload, $userIsAt, $funnelName);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
};
//Endpoint for motor rocket log
$endpoints["writeMotorLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO motor_rocket_log (log_payload, user_is_at, funnel_name) VALUES (?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("sss", $logPayload, $userIsAt, $funnelName);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
};
//Endpoint for business rocket log
$endpoints["writeBusinessLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO business_rocket_log (log_payload, user_is_at, funnel_name) VALUES (?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("sss", $logPayload, $userIsAt, $funnelName);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
};
//Endpoint for Legal rocket log
$endpoints["writeLegalLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO legal_rocket_log (log_payload, user_is_at, funnel_name) VALUES (?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("sss", $logPayload, $userIsAt, $funnelName);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
};
//Endpoint for life rocket log
$endpoints["writeLifeLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO life_rocket_log (log_payload, user_is_at, funnel_name) VALUES (?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("sss", $logPayload, $userIsAt, $funnelName);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
};
//Endpoint for funeral rocket log
$endpoints["writeFuneralLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO funeral_rocket_log (log_payload, user_is_at, funnel_name) VALUES (?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("sss", $logPayload, $userIsAt, $funnelName);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
};
//Endpoint for pet rocket log
$endpoints["writePetLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO pet_rocket_log (log_payload, user_is_at, funnel_name) VALUES (?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("sss", $logPayload, $userIsAt, $funnelName);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
};
//Endpoint for mai site log
$endpoints["writeMainLog"] = function (array $requestData): void {

    // DB Connection Setup

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

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Database connection failed."

        ]);

        die();
    }



    // Validate request data

    if (empty($requestData)) {

        http_response_code(400);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 400,

            "message" => "Request data is empty."

        ]);

        $conn->close();

        die();
    }



    $userIsAt = $requestData['userIsAt'] ?? 'unknown';

    $funnelName = $requestData['funnelId'] ?? 'unknown';

    $ip_address = $_SERVER['REMOTE_ADDR'];

    unset($requestData['token']);



    // Log the incoming data as JSON

    $logPayload = json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);



    $stmt = $conn->prepare("INSERT INTO main_site_log (log_payload, user_is_at, funnel_name,ip_address) VALUES (?, ?, ?, ?)");

    if ($stmt) {

        $stmt->bind_param("ssss", $logPayload, $userIsAt, $funnelName, $ip_address);

        if ($stmt->execute()) {

            http_response_code(200);

            echo json_encode([

                "status" => "SUCCESS",

                "statusCode" => 200,

                "message" => "Log entry created successfully."

            ]);
        } else {

            http_response_code(500);

            echo json_encode([

                "status" => "ERROR",

                "statusCode" => 500,

                "message" => "Failed to log entry. " . $stmt->error

            ]);
        }

        $stmt->close();
    } else {

        http_response_code(500);

        echo json_encode([

            "status" => "ERROR",

            "statusCode" => 500,

            "message" => "Failed to prepare statement. " . $conn->error

        ]);
    }



    $conn->close();
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
