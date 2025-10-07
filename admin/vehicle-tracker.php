<?php
session_start();
include_once 'credential.php';
$siteAccessCode = AUTH_CODE;
// echo '<pre>';
// print_r($_SESSION['authKey']);
// die();
if ($_SESSION['authKey'] !== $siteAccessCode) {
    header('Location: /admin/index.php');
}
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
                                            <h4 class="d-inline-block" style="margin-bottom: 0;">Vehicle Tracker</h4>
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
                                                        <th scope="col">Date</th>
                                                        <th scope="col">Lead Id</th>
                                                        <th scope="col">AFFID</th>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">Phone</th>
                                                        <th scope="col">Click ID</th>
                                                        <th scope="col">Sub Channel</th>
                                                        <th scope="col">Offer ID</th>
                                                    </tr>
                                                </thead>
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

    <script>
        $(document).ready(function() {
            var exclude = 'false';
            var tableName = 'track_rocket';
            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "request-table-data.php", // Path to your PHP file
                    data: function(d) {
                        // Add a custom parameter to exclude test data
                        d.exclude_test = exclude; // true or false
                        d.table_name = tableName; // true or false
                    }
                },
                pageLength: 50, // Set default number of rows
                columns: [{
                        data: "created_at"
                    }, {
                        data: "leadId"
                    },
                    {
                        data: "aff_sub"
                    },
                    {
                        data: "fullName"
                    },
                    {
                        data: "phone1"
                    },
                    {
                        data: "aff_sub2"
                    },
                    {
                        data: "aff_sub3"
                    },
                    {
                        data: "offer_id"
                    },
                ],
                createdRow: function(row, data) {
                    $(row).attr('onclick', 'window.location.href="/admin/request-response.php/?search_db=' + tableName + '&search_id=' + data.id + '"');
                    $(row).css('cursor', 'pointer');
                },
                initComplete: function() {
                    // Add placeholder text to the search input box
                    var searchInput = $('div.dt-search input');
                    searchInput.attr('placeholder', 'Enter any data you have...');
                },

            });

            //exclude test


            $('#toggleButton').change(function() {

                if (this.checked) {
                    exclude = 'true';
                    table.ajax.reload();
                } else {
                    //Clear all filters
                    exclude = 'false';
                    table.ajax.reload();
                }
            });

            //end
        });
    </script>



</body>



</html>