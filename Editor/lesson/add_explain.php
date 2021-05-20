<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("INSERT INTO ExplainerLink (nPackageID) VALUES (:packageID)",
[
    "packageID" => $_POST["packageID"]
]);

echo "INSERTED";
exit();