<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//include_once __DIR__ . '/../package/update.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$connect->exec(
	"UPDATE Lesson SET sRecognition = :text WHERE nID = :id AND nPackageID = :packageID",
	[
		"text" => $_POST["text"],
		"id" => $_POST["id"],
		"packageID" => $_POST["packageID"]
	]
);

echo "UPDATED";

exit();