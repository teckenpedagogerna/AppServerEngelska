<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//include_once __DIR__ . '/update.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("UPDATE Zone SET sDescription = :description WHERE nID = :id",
	[
		"id" => $_POST["id"],
		"description" => $_POST["description"]
	]
);

echo "UPDATED";
exit();