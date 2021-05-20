<?php

//include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$data = $connect->query(
	"SELECT sData, nStepIndex FROM Data WHERE nPackageID = :packageID AND nLessonID = :lessonID ORDER BY nStepIndex",
	[
		"packageID" => $_GET["packageID"],
		"lessonID" => $_GET["lessonID"]
	]
);

echo '{ "Items": ' . json_encode($data) . ' }';