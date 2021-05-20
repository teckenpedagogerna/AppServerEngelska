<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

if(!is_numeric($_POST["packageID"]) || !is_numeric($_POST["lessonID"]) || !is_numeric($_POST["stepIndex"]))
	exit();

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("INSERT INTO Data (nLessonID, nPackageID, sData, nStepIndex) VALUES (:lessonID, :packageID, :data, :stepIndex)",
	[
		"lessonID" => $_POST["lessonID"],
		"packageID" => $_POST["packageID"],
		"data" => $_POST["data"],
		"stepIndex" => $_POST["stepIndex"]
	]
);

echo "INSERTED";
exit();