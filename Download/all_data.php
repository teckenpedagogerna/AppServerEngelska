<?php

//include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$data = $connect->query(
	"SELECT sData, nStepIndex, nLessonID, nPackageID FROM Data ORDER BY nStepIndex"
);

echo '{ "Items": ' . json_encode($data) . ' }';