<?php 
session_start();
include_once 'credential.php';
$siteAccessCode = AUTH_CODE;
//echo '<pre>'; print_r($_SESSION['authKey']); die();
if($_SESSION['authKey'] !== $siteAccessCode){
    header('Location: /admin/index.php');
}

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
    echo 'Something went wrong try again!'; die();
    //die("Connection failed: " . $conn->connect_error);
}
$fetchPayQueryData = "SELECT email,merchant_payment_code,hash_code,pay_status,status,webhook_response,created_at from ebanx_pay_response";

    $fetchUserPayData = $conn->query($fetchPayQueryData);

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Supplement Machine</title>
    


    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/searchbuilder/1.8.0/css/searchBuilder.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.5.4/css/dataTables.dateTime.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

        body {
            margin: 0;
            font-family: "Montserrat", sans-serif;
        }

        .dt-search{
            background: #fff;
            border: 1px solid #cccccc;
            padding: 0 5px;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }
        .dt-search label{
            margin-bottom: 0;
        }

        div.dt-container .dt-search input {
            border: none;
            border-radius: 3px;
            padding: 5px 0;
            background-color: transparent;
            color: inherit;
            margin-left: 3px;
            width: 290px;
        }
        div.dt-container .dt-search input:focus{
            outline: none;
        }

        .search-container {
            background: #f4f5f7;
        }

        .search-container .status-bar .search-box {
            margin: 30px 0;
            display: flex;
            justify-content: space-between;
        }

        .search-container .status-bar .search-box input[type="text"] {
            width: 80%;
            font-size: 14px;
            padding: 14px;
            border: 1px solid #cccccc;
            border-radius: 3px;
            font-weight: normal;

        }

        .search-container .status-bar .search-box button {
            padding: 14px;
            border: 0;
            width: 19%;
            background: #02bbe3;
            font-size: 14px;
            color: #fff;
            border-radius: 3px;
            border: 1px solid #02bbe3;
        }

        .search-container .status-bar .search-box button:hover {
            opacity: 0.8;
        }

        .search-container .status-bar .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            background: #f4f5f7;
            border: none !important;
            border-collapse: separate;
            border-spacing: 0 5px;
        }

        .search-container .status-bar .table thead th {
            border: none !important;
            font-weight: 700;
            font-size: 12px;
            text-transform: capitalize;
            /* color: #626262; */
            color: #000;
            text-align: center;
        }

        .search-container .status-bar .table tbody tr {
            cursor:pointer;
            height: 55px;
            background: #ffffff;
            margin: 10px 0;
            /* display: block; */
            /* box-shadow: 5px 8px 14px #7570701f; */
            border-radius: 4px;
        }

        .search-container .status-bar .table tbody tr td {
            border: none !important;
            font-weight: 500;
            font-size: 12px;
            text-transform: capitalize;
            vertical-align: middle;
            text-align: center;
            color: #848282;
        }
        .dtsb-searchBuilder{
            display:none !important;
        }
        .dt-length{
            display:none !important;
        }
        @media screen and (max-width: 767px) {
            .search-container .status-bar .search-box input[type="text"] {
                width: 70%;
                font-size: 11px;
            }

            .search-container .status-bar .search-box button {
                width: 28%;
            }
            div.dt-container .dt-search input{
                width: 100%;
            }
        }
    </style>
</head>

<body class="<?= $_SESSION['authKey']; ?>">

    <section class="search-container">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="back-box" style="text-align:right">
                        <a href="/admin/logout.php"><i class="fa fa-user" aria-hidden="true"></i> [LOG OUT]</a>

                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="status-bar">
                            <div class="table-responsive">
                                <table class="table align-middle" id="dataTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">Date</th>
                                            <!-- <th scope="col">status</th> -->
                                            <th scope="col">Pay Status</th>
                                            <th scope="col">hash</th>
                                            <th scope="col">Payment Code</th>
                                            <th scope="col">Transaction Code</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">ECI</th>
                                            <th scope="col">Liability</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($fetchUserPayData->num_rows > 0) { ?>
                                            <?php while($row = $fetchUserPayData->fetch_assoc()) { 
                                            $webhook_response= json_decode($row['webhook_response']);
                                            $code= $webhook_response->payment->transaction_status->code;
                                            $description= $webhook_response->payment->transaction_status->description;
                                            $eci= $webhook_response->payment->threedsecure->threeds_result->eci;
                                            $liability= $webhook_response->payment->threedsecure->threeds_result->liability;
                                            ?>
                                                <tr class="response-row" onclick="window.location='/admin/request-response.php/?hash_code=<?=$row['hash_code']; ?>';">
                                                    <td scope="row" ><?= $row['created_at'];?></td>
                                                    <!-- <td class="text-success"><?= $row['status'];?></td> -->
                                                    <td class="text-success"><?= $row['pay_status'];?></td>
                                                    <td><?= $row['hash_code'];?></td>
                                                    <td><?= $row['merchant_payment_code'];?></td>
                                                    <td><?= $code;?></td>
                                                    <td><?= $description;?></td>
                                                    <td><?= $eci;?></td>
                                                    <td><?= $liability;?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            $payloadData= NULL;
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.8.0/js/dataTables.searchBuilder.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.8.0/js/searchBuilder.dataTables.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.5.4/js/dataTables.dateTime.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
    <script src="https://use.fontawesome.com/c15ad84f45.js"></script>
    <script>
        new DataTable('#dataTable', {
            layout: {
                top1: 'searchBuilder'
            },
            order: [[0, 'desc']],
            "pageLength": 25 
        });
        //Ajax for response data
        // $('.response-row').on('click', function(){
        //     let hash_code= $(this).attr('id');
        //     console.log(hash_code)
        //      window.location.href = "/request-response.php/?hash_code="+hash_code;
        // });
    </script>
</body>

</html>