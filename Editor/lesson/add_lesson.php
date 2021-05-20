<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//include_once __DIR__ . '/../package/update.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("INSERT INTO Lesson (nID, nPackageID, nstepIndex) VALUES (:id, :packageID, :step)",
	[
		"id" => $_POST["id"],
		"packageID" => $_POST["packageID"],
		"step" => $_POST["step"]
	]
);

echo "INSERTED";
exit();