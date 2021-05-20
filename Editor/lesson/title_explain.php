<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec(
	"UPDATE ExplainerLink SET sTitle = :text WHERE nPackageID = :packageID",
	[
		"text" => $_POST["text"],
		"packageID" => $_POST["packageID"]
	]
);

echo "UPDATED";
exit();