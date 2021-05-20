<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//include_once __DIR__ . '/update.php';

if(!isset($_POST['pid']) || !isset($_POST['newpid']))
	exit();

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$package0 = $connect->query("SELECT nID, nZoneID, nStepIndex FROM Package WHERE nID = :id",
	[
		"id" => (int)$_POST["pid"]
	]
);

$package1 = $connect->query("SELECT nID, nZoneID, nStepIndex FROM Package WHERE nID = :id",
	[
		"id" => (int)$_POST["newpid"]
	]
);

$connect->exec("UPDATE Package SET nZoneID = :zoneID, nStepIndex = :step WHERE nID = :id",
	[
		"id" => (int)$_POST["pid"],
		"zoneID" => (int)$package1[0]['nZoneID'],
		"step" => (int)$package1[0]['nStepIndex']
	]
);

$connect->exec("UPDATE Package SET nZoneID = :zoneID, nStepIndex = :step WHERE nID = :id",
	[
		"id" => (int)$_POST["newpid"],
		"zoneID" => (int)$package0[0]['nZoneID'],
		"step" => (int)$package0[0]['nStepIndex']
	]
);

echo "SWAPPED";
exit();