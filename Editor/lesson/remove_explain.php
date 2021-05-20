<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("DELETE FROM ExplainerLink WHERE nPackageID = :packageID",
	[
		"packageID" => $_POST["packageID"]
	]
);

$connect->exec("DELETE FROM Explainer WHERE nPackageID = :packageID",
	[
		"packageID" => $_POST["packageID"]
	]
);

echo "DELETED";
exit();