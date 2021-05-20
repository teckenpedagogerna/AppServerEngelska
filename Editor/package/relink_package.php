<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

exit();

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("UPDATE Package SET nStepIndex = nID");