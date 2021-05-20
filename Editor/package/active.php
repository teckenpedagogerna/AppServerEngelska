<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

include_once __DIR__ . '/update.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$connect->exec(
	"UPDATE Zone SET bActivated = :active WHERE nID = :id",
	[
		"active" => $_POST["active"],
		"id" => $_POST["id"]
	]
);

switch ($_POST["active"])
{
	case '1':
		echo "ACTIVATED";
		break;	
	default:
		echo "DEACTIVATED";
		break;
}

exit();