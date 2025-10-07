<?php 

session_start();
include_once 'credential.php';
$siteAccessCode = AUTH_CODE;

      if (isset($_POST['site_access_cred']) && !empty($_POST['site_access_cred'])) {  
            if ($_POST['site_access_cred'] == $siteAccessCode){
               $_SESSION['valid'] = true;
               $_SESSION['timeout'] = time();
               $_SESSION['authKey'] = $siteAccessCode;
               header("Location: /admin/dashboard.php");
            }
            else {
               header("Location: /admin/index.php");
            }
         
      }
?>