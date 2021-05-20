<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

if(!is_numeric($_POST["packageID"]) || !is_numeric($_POST["zoneID"]) || !is_numeric($_POST["stepIndex"]))
	exit();

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("INSERT INTO Explainer (nZoneID, nPackageID, nStepIndex, sData) VALUES (:zoneID, :packageID, :stepIndex, :data)",
	[
		"zoneID" => $_POST["zoneID"],
		"packageID" => $_POST["packageID"],
		"stepIndex" => $_POST["stepIndex"],
		"data" => $_POST["data"]
	]
);

$id = $connect->query("SELECT nID FROM Explainer WHERE nZoneID = :zoneID AND nPackageID = :packageID AND nStepIndex = :stepIndex AND sData = :data",
	[
		"zoneID" => $_POST["zoneID"],
		"packageID" => $_POST["packageID"],
		"stepIndex" => $_POST["stepIndex"],
		"data" => $_POST["data"]
	]
);

/*
$data = $connect->exec("SELECT * FROM Explainer WHERE nZoneID = :zoneID AND nPackageID = :packageID",
	[
		"zoneID" => $_POST["zoneID"],
		"packageID" => $_POST["packageID"],
		"stepIndex" => $_POST["stepIndex"]
	]
);
*/

echo $id[0]['nID'];
exit();