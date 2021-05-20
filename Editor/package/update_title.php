<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//include_once __DIR__ . '/update.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("UPDATE Package SET sTitle = :title WHERE nID = :id",
	[
		"id" => $_POST["id"],
		"title" => $_POST["title"]
	]
);

echo "UPDATED";
exit();