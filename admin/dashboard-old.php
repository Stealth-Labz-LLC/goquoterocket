<?php
session_start();
include_once 'credential.php';
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
$fetchQueryData = "SELECT id,created_at,api_response,aff_sub,aff_sub2,aff_sub3,funnel_id,phone1,firstname,lastname,offer_id from main_site ORDER BY 
    id DESC";

$fetchUserData = $conn->query($fetchQueryData);

?>
<!doctype html>



<html lang="en" class="layout-menu-fixed layout-compact">



<?php include('layout/header.php'); ?>



<body>

    <!-- Layout wrapper -->

    <div class="layout-wrapper layout-content-navbar">

        <div class="layout-container">

            <!-- Menu -->

            <?php include('layout/sidebar.php'); ?>

            <!-- / Menu -->



            <!-- Layout container -->

            <div class="layout-page">

                <!-- Navbar -->
                <?php include('layout/sub-header.php'); ?>

                <!-- / Navbar -->
                <!-- Content wrapper -->

                <div class="content-wrapper">

                    <div class="container-xxl flex-grow-1 container-p-y">

                        <div class="row">

                            <div class="col-12">

                                <div class="card h-100">

                                    <div class="card-body">

                                        <!-- <h4 class="text-center">Main Site</h4> -->
                                        <!-- new heading with button  -->
                                        <div class="d-flex align-items-center justify-content-between top_dashh">
                                            <h4 class="d-inline-block" style="margin-bottom: 0;">Main Site</h4>
                                            <!--<button class="exclude-btn">Exclude Tests</button>-->
                                            <div class="toggle-container">
                                                <label>Exclude Tests</label>
                                                <label class="toggle">
                                                    <input type="checkbox" id="toggleButton">
                                                    <span class="slider"></span>
                                                </label>
                                            </div>

                                        </div>


                                        <div class="table-responsive">
                                            <div id="custom-controls" class="custom-controls"></div>
                                            <table class="table align-middle table-striped" id="dataTable">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Funnel Id</th>
                                                        <th scope="col">Lead Id</th>
                                                        <th scope="col">AFFID</th>
                                                        <th scope="col">Date</th>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">Phone</th>
                                                        <th scope="col">Click ID</th>
                                                        <th scope="col">Sub Channel</th>
                                                        <th scope="col">Offer ID</th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($fetchUserData->num_rows > 0) { ?>
                                                        <?php while ($row = $fetchUserData->fetch_assoc()) {

                                                            $api_response = json_decode($row['api_response']);
                                                            //print_r($leadId);
                                                        ?>
                                                            <tr class="response-row" onclick="window.location='/admin/request-response.php/?search_db=main_site&search_id=<?= $row['id']; ?>';">
                                                                <td><?= $row['funnel_id']; ?></td>
                                                                <td><?= $api_response->leadId; ?></td>
                                                                <td class="text-affid"><?= $row['aff_sub']; ?></td>
                                                                <td scope="row"><?= $row['created_at']; ?></td>
                                                                <td><?= $row['firstname'] . ' ' . $row['lastname']; ?></td>
                                                                <td><?= $row['phone1']; ?></td>
                                                                <td><?= $row['aff_sub2']; ?></td>
                                                                <td><?= $row['aff_sub3']; ?></td>
                                                                <td><?= $row['offer_id']; ?></td>

                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        $payloadData= NULL;
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="content-backdrop fade"></div>

                </div>

                <!-- Content wrapper -->

            </div>

            <!-- / Layout page -->

        </div>





    </div>

    <!-- / Layout wrapper -->



    <!-- Core JS -->

    <?php include('layout/footer.php'); ?>





</body>



</html>