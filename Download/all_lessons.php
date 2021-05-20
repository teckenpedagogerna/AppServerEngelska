<?php

//include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$lessons = $connect->query(
	"SELECT nID, sTitle, nStepIndex, sRecognition, nPackageID FROM Lesson"
);

echo '{ "Items": ' . json_encode($lessons) . ' }';