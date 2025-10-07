<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$toggleOpen = [
    'insurance' => ['legal-insurance.php', 'pet-insurance.php', 'life-insurance.php', 'business-insurance.php'],
    'medical' => ['medical-insurance.php', 'funeral-cover.php'],
    'finance' => ['personal-loan.php', 'debt-relief.php'],
    'car' => ['car-insurance.php', 'vehicle-tracker.php', 'motor-warranty.php'],
];





// Initialize an array to hold active keys
$activeKey = [];

// Loop through the array to find matches
foreach ($toggleOpen as $key => $values) {
    if (in_array($currentPage, $values)) {
        $activeKey = $key; // Add the active key
    }
}
//echo $activeKeys; die();
// Example: Output the results

//echo $currentPage;
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <div class="app-brand demo">

        <a href="dashboard.php" class="app-brand-link">

            <span class="app-brand-logo demo">

                <img src="images/quoterocket-logo.png" class="img-fluid" alt="">

                <span class="text-primary">

                </span>

            </span>



        </a>



        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">

            <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>

        </a>

    </div>



    <div class="menu-divider mt-0"></div>



    <div class="menu-inner-shadow"></div>



    <ul class="menu-inner py-1">

        <!-- Dashboards -->



        <!-- Layouts -->

        <li class="menu-item  <?php echo ($activeKey == 'insurance') ? 'active' : ''; ?>">

            <a href=" javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons bx bx-bxs-business"></i>

                <div class="text-truncate" data-i18n="Layouts">Insurance</div>

            </a>



            <ul class="menu-sub">

                <li class="menu-item <?php echo ($currentPage == 'legal-insurance.php') ? 'active' : ''; ?>">

                    <a href="legal-insurance.php" class="menu-link">

                        <div class="text-truncate">Legal Insurance</div>

                    </a>

                </li>

                <li class="menu-item <?php echo ($currentPage == 'pet-insurance.php') ? 'active' : ''; ?>">

                    <a href="pet-insurance.php" class="menu-link">

                        <div class="text-truncate">Pet Insurance</div>

                    </a>

                </li>

                <li class="menu-item <?php echo ($currentPage == 'life-insurance.php') ? 'active' : ''; ?>">

                    <a href="life-insurance.php" class="menu-link">

                        <div class="text-truncate">Life Insurance</div>

                    </a>

                </li>

                <li class="menu-item <?php echo ($currentPage == 'business-insurance.php') ? 'active' : ''; ?>">

                    <a href="business-insurance.php" class="menu-link">

                        <div class="text-truncate">Business Insurance</div>

                    </a>

                </li>



            </ul>

        </li>

        <li class="menu-item <?php echo ($activeKey == 'medical') ? 'active' : ''; ?>">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons bx bx-plus-medical"></i>

                <div class="text-truncate" data-i18n="Layouts">Medical</div>

            </a>



            <ul class="menu-sub">

                <li class="menu-item <?php echo ($currentPage == 'medical-insurance.php') ? 'active' : ''; ?>">

                    <a href="medical-insurance.php" class="menu-link">

                        <div class="text-truncate">Medical Insurance</div>

                    </a>

                </li>

                <li class="menu-item <?php echo ($currentPage == 'funeral-cover.php') ? 'active' : ''; ?>">

                    <a href="funeral-cover.php" class="menu-link">

                        <div class="text-truncate">Funeral Cover</div>

                    </a>

                </li>

            </ul>

        </li>

        <li class="menu-item <?php echo ($activeKey == 'finance') ? 'active' : ''; ?>">

            <a href="finance-insurance.php" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons bx bx-bxs-calculator"></i>

                <div class="text-truncate" data-i18n="Layouts">Finance</div>

            </a>



            <ul class="menu-sub">

                <li class="menu-item <?php echo ($currentPage == 'personal-loan.php') ? 'active' : ''; ?>">

                    <a href="personal-loan.php" class="menu-link">

                        <div class="text-truncate">Personal Loans</div>

                    </a>

                </li>

                <li class="menu-item <?php echo ($currentPage == 'debt-relief.php') ? 'active' : ''; ?>">

                    <a href="debt-relief.php" class="menu-link">

                        <div class="text-truncate">Debt Relief</div>

                    </a>

                </li>



            </ul>

        </li>

        <li class="menu-item <?php echo ($activeKey == 'car') ? 'active' : ''; ?>">

            <a href="javascript:void(0);" class="menu-link menu-toggle">

                <i class="menu-icon tf-icons bx bxs-car"></i>

                <div class="text-truncate" data-i18n="Layouts">Cars</div>

            </a>



            <ul class="menu-sub">

                <li class="menu-item <?php echo ($currentPage == 'car-insurance.php') ? 'active' : ''; ?>">

                    <a href="car-insurance.php" class="menu-link">

                        <div class="text-truncate">Car Insurance</div>

                    </a>

                </li>

                <li class="menu-item <?php echo ($currentPage == 'vehicle-tracker.php') ? 'active' : ''; ?>">

                    <a href="vehicle-tracker.php" class="menu-link">

                        <div class="text-truncate">Vehicle Tracker</div>

                    </a>

                </li>

                <li class="menu-item <?php echo ($currentPage == 'motor-warranty.php') ? 'active' : ''; ?>">

                    <a href="motor-warranty.php" class="menu-link">

                        <div class="text-truncate">Motor Warranty</div>

                    </a>

                </li>

            </ul>

        </li>





    </ul>

</aside>