<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//include_once __DIR__ . '/update.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("INSERT INTO Package (nID, nStepIndex, nSideIndex) VALUES (:id, :step, :side)",
	[
		"id" => $_POST["id"],
		"step" => $_POST["step"],
		"side" => $_POST["side"]
	]
);

echo "INSERTED";
exit();