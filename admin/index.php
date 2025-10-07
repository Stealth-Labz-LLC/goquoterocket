<?php
ob_start();
session_start();
include_once 'credential.php';
$siteAccessCode = AUTH_CODE;
//echo $value; die();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Account Login | Quote Rocket</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

        body {
            margin: 0;
            font-family: "Montserrat", sans-serif;
        }

        .log-block {
            background: #f6fbff;
            height: 100vh;
        }

        .frm-ent {
            line-height: 30px;
            width: 73%;
            height: 49px;
            color: #323232;
            font-size: 16px;
            background: #fff;
            text-align: left;
            border-radius: 5px;
            border: 1px solid #bdbdbd;
            font-weight: 400;
            padding: 0 15px;
            outline: none;
            -webkit-appearance: none;
            margin-top: 5px;
            display: inline-block;
            margin: 0 auto;
        }

        .log-block .log-form {
            background: #fff;
            overflow: hidden;
            border-radius: 4px;
            border: 0px solid #dfe3e7;
            margin: 20px;
            padding: 20px;
            box-shadow: 0 0 15px 1px rgb(38 116 217 / .1);
            -webkit-box-shadow: 0 0 15px 1px rgb(38 116 217 / .1);
        }

        .log-block .log-form h6 {
            text-align: center;
            padding: 30px 10px 010px;
            font-size: 16px;
            font-weight: 500;
            color: #646464;
            line-height: 1.5;
            margin: 0;
        }

        .log-block .log-form .logo-box {
            background: #ffffff;
            text-align: center;
            padding: 10px;
        }

        .log-block .log-form .logo-box img {
            display: block;
            max-width: 275px;
            margin: 0px auto;
            margin-top: 20px;
        }

        .log-block .log-form form {
            padding: 0 10px;
            margin: 30px 0;
        }

        .log-block .log-form form input[type="text"].frm-ent {
            display: block;
            width: 100%;
            padding: 10px;
            border: 1px solid #cccccc;
            border-radius: 3px;
            font-weight: normal;
        }

        .log-block .log-form .frm-bt {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 10px 0;
        }

        .log-block .log-form .frm-bt label.m-0 {
            font-size: 13px;
            line-height: 15px;
            color: #737373;
            display: flex;
            align-items: center;
            column-gap: 5px;
        }

        .log-block .log-form .frm-bt a {
            color: #007bff;
            font-size: 13px;
            line-height: 15px;
            display: flex;
            align-items: center;
        }

        .log-block .log-form .submit-btn-nb button.submit-btn {
            display: inline-block;
            width: 73%;
            /* background: #a38d65; */
            background: #ff6100;
            border-radius: 4px;
            margin: 0 auto;
            color: #fff;
            padding: 14px 20px;
            border: 0px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            outline: none;
        }

        .log-block .log-form .submit-btn-nb button.submit-btn:hover {
            opacity: 0.8;
        }
        .error_msg {
          color: rgb(220, 68, 20);
          font-weight: 500;
          font-size: 15px !important;
          margin: 5px 0px;
          display: none;
        }

        @media screen and (max-width: 767px) {
            .log-block .log-form form {
                margin: 10px 0 20px 0;
            }

            .log-block .log-form .submit-btn-nb button.submit-btn {
                margin-top: 20px;
            }
            .log-block .log-form {
                margin: 0px !important;
                padding: 5px !important;
            }
        }
    </style>
</head>

<body>

    <section class="log-block">
        <div class="container">
            <div class="row vh-100 justify-content-center align-items-center">
                <div class="col-md-7">
                    <div class="log-form">
                        <div class="logo-box"><img src="images/quoterocket-logo.svg" alt=""></div>
                        <h6>Please enter the password below to access the reporting dashboard:</h6>
                        <form method="post" action="login.php" name="auth_login">
                            <div class="frm-bt">
                                <input type="password" class="frm-ent"  title="username" name="site_access_cred" id="site_access_cred" placeholder="F**********" autocomplete="off" required />
                            </div>
                            <div class="frm-bt">
                                <span id="error_msg" class="error_msg">Please enter a valid password.  If you don't know it, hit up Keating.</span>
                            </div>
                            <div class="submit-btn-nb text-center">
                                <button type="button" class="submit-btn">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
    <script src="https://use.fontawesome.com/c15ad84f45.js"></script>
    <script>
        $('.submit-btn').on('click', function(e) {
            e.preventDefault();
            let access_data = '<?= $siteAccessCode ?>';
            let user_access_code = $('#site_access_cred').val();
            if (access_data === user_access_code) {
                $('#error_msg').hide();
                $('[name=auth_login]').submit()
                //window.location.href="/ebanx/pay-details.php";
            } else {
                $('#error_msg').show();
            }

        });
    </script>
    <script>
    // Function to get URL parameter by name
    function getQueryParam(name) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(name);
    }

    // Get 'password' from the URL
    const passwordFromURL = getQueryParam('password');

    // If password is found, set it as the input value
    if (passwordFromURL) {
        const passwordField = document.getElementById('site_access_cred');
        if (passwordField) {
            passwordField.value = passwordFromURL;
        }
    }
</script>

</body>

</html>
