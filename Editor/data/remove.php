<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//include_once __DIR__ . '/../package/update.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("DELETE FROM Data WHERE nStepIndex = :stepIndex AND nPackageID = :packageID AND nLessonID = :lessonID",
	[
		"lessonID" => $_POST["lessonID"],
		"stepIndex" => $_POST["stepIndex"],
		"packageID" => $_POST["packageID"]
	]
);

echo "DELETED";
exit();