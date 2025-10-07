<?php
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
session_start();
include_once 'credential.php';
$siteAccessCode = AUTH_CODE;
if ($_SESSION['authKey'] !== $siteAccessCode) {
    header('Refresh: 2; URL = /admin/index.php');
}

$search_id = !empty($_REQUEST['search_id']) ? $_REQUEST['search_id'] : NULL;
$search_db = !empty($_REQUEST['search_db']) ? $_REQUEST['search_db'] : NULL;

if (!empty($search_id) && !empty($search_db)) {
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
        die("Connection failed: " . $conn->connect_error);
    }

    // Map tables to their columns
    $columnMapping = [
        'main_site' => ['api_payload', 'api_response', 'postback_payload', 'postback_response', 'dripcell_payload', 'dripcell_response'],
        'legal_rocket' => ['legal_payload', 'legal_response', 'dripcell_payload', 'dripcell_response'],
        'pet_rocket' => ['pet_payload', 'pet_response', 'dripcell_payload', 'dripcell_response'],
        'life_rocket' => ['life_payload', 'life_response', 'dripcell_payload', 'dripcell_response'],
        'business_rocket' => ['business_payload', 'business_response', 'dripcell_payload', 'dripcell_response'],
        'health_rocket' => ['medical_aid_payload', 'medical_aid_response', 'dripcell_payload', 'dripcell_response'],
        'funeral_rocket' => ['funeral_payload', 'funeral_response', 'dripcell_payload', 'dripcell_response'],
        'loan_rocket' => ['loan_payload', 'loan_response', 'dripcell_payload', 'dripcell_response'],
        'debt_rocket' => ['debt_payload', 'debt_response', 'dripcell_payload', 'dripcell_response'],
        'auto_rocket' => ['insurance_payload', 'insurance_response', 'track_payload', 'track_response', 'warranty_payload', 'warranty_response', 'dripcell_payload', 'dripcell_response'],
        'track_rocket' => ['track_payload', 'track_response', 'auto_payload', 'auto_response', 'dripcell_payload', 'dripcell_response'],
        'motor_rocket' => ['motor_payload', 'motor_response', 'dripcell_payload', 'dripcell_response'],
    ];

    if (array_key_exists($search_db, $columnMapping)) {
        $columns = implode(',', $columnMapping[$search_db]); // Dynamically select columns
        $sql = "SELECT $columns FROM $search_db WHERE id = ?";
        // echo $sql;
        // die();
    } else {
        echo "Invalid column name!";
        die();
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $search_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $dataArray = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            foreach ($row as $key => $value) {
                $dataArray[$key] .= $value;
            }
        }
    } else {
        echo "No results found.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Please provide both search ID and database.";
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>API Details | Quote Rocket</title>
    <style>
        /* @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap'); */
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap');

        body {
            margin: 0;
            /* font-family: "Montserrat", sans-serif; */
            font-family: "DM Sans", sans-serif;

        }

        .di-log-container {
            background: #f6fbff;
            min-height: 100vh;
            height: auto;
            padding-bottom: 30px;
        }

        pre {
            color: #002e6a !important;
        }

        .di-log-container .back-bar {
            margin-top: 2rem;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 0 15px 1px rgb(38 116 217 / .1);
            -webkit-box-shadow: 0 0 15px 1px rgb(38 116 217 / .1);

        }

        .di-log-container .table {
            margin: 0;
            padding: 0;
        }

        .di-log-container .table thead th {
            border: none !important;
            font-weight: 500;
            font-size: 9px;
            text-transform: capitalize;
            color: #ababab;
            text-align: center;
        }

        .di-log-container .table tbody {

            margin-top: 1rem;

        }

        .back-box {
            margin-top: 2.0rem;
        }

        .back-box a {
            font-size: 15px;
            font-weight: 800;
            color: #0091ff;
        }

        .table-responsive {
            display: block;
            padding: 25px;
        }

        .table-responsive h5 {
            font-weight: 600;
            color: #002e6a;
        }

        @media screen and (max-width: 767px) {
            /* .di-log-container .container .row {
                flex-direction: column-reverse !important;
            } */

            .back-box {
                margin-bottom: 5px;
            }

        }
    </style>
</head>

<body>

    <section class="di-log-container">
        <div class="container">
            <div class="row align-items-center justify-content-start">
                <div class="col-lg-3 col-6">
                    <div class="back-box text-left">
                        <a href="<?= $_SERVER['HTTP_REFERER'] ?>"><i class="fa fa-angle-left" aria-hidden="true"></i> GO BACK</a>

                    </div>
                </div>
            </div>
            <div class="row">
                <?php foreach ($dataArray as $key => $value): ?>
                    <div class="col-md-12">
                        <div class="back-bar">
                            <div class="table-responsive" id="data1">
                                <h5><?= $key ?></h5>
                                <?php echo '<pre>' . json_encode(json_decode($value, true), JSON_PRETTY_PRINT) . '</pre>'; ?>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
    <script src="https://use.fontawesome.com/c15ad84f45.js"></script>
</body>

</html>