<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$data = $connect->query("SELECT * FROM Report WHERE nID = :id",
	[
		"id" => $_POST["id"]
	]
);

unlink(__DIR__ . '/Screenshots/' . $data[0]['sCode'] . '-' . $data[0]['sFile']);

$connect->exec("DELETE FROM Report WHERE nID = :id",
	[
		"id" => $_POST["id"]
	]
);

echo "DELETED";
exit();