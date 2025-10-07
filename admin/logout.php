<?php
    session_start();
    //echo '<pre>'; print_r($_SESSION["authKey"]);
    unset($_SESSION["authKey"]);
    session_destroy();
    header('Location: /admin/index.php');
?>