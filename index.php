<?php
include_once $_SERVER['LIBRARY'] . '/ssl.class.php';

$loginfailed = false;
session_start();

if(isset($_SESSION) && $_SESSION["IP"] == $_SERVER['REMOTE_ADDR'] && intval($_SESSION["DATE"]) > intval(date("his") - 10000))
{
    header("Location: https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Editor/package.php");
    exit();
}

$_SESSION["IP"] = "";
$_SESSION["DATE"] = "";
$_SESSION["USER"] = "";

if(!empty($_POST))
{
    include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
    $connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

    if(strval(count($connect->query('SELECT * FROM Admin WHERE username = :username AND keypass = :keypass',
        [
            'username' => $_POST['username'],
            'keypass' => hash("sha512", $_POST['keypass'])
        ]
    ))))
    {
        $_SESSION["IP"] = $_SERVER['REMOTE_ADDR'];
        $_SESSION["DATE"] = date("his");
        $_SESSION["USER"] = $_POST['username'];

        header("Location: https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Editor/package.php");
        exit();
    }
    else
    {
        $loginfailed = true;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - Brand</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/divider-text-middle.css">
    <link rel="stylesheet" href="assets/css/Drag--Drop-Upload-Form.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
</head>

<body id="page-top">
    <div class="login-clean">
        <form  action="/AppServer/Engelska/" method="post">
            <h2 class="sr-only">Login Form</h2>
            <div class="illustration"><i class="icon ion-ios-locked" style="color: #88a;"></i></div>
            <div class="form-group"><input class="form-control" type="text" name="username" placeholder="Användarnamn"></div>
            <div class="form-group"><input class="form-control" type="password" name="keypass" placeholder="Lösenord"></div>
            <div class="form-group"><button class="btn btn-primary btn-block" type="submit" style="background-color: #09d;">Logga in</button></div>
        <?php if($loginfailed) { ?>
            <div style="color: #f00;">Inloggning misslyckades</div>
        <?php } ?>
        </form>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
    <script src="assets/js/theme.js"></script>
</body>

</html>