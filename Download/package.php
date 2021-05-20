<?php

//include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$packages = $connect->query("SELECT nID, sTitle, nStepIndex, nSideIndex FROM Package WHERE bActivated = 1");

echo '{ "Items": ' . json_encode($packages) . ' }';