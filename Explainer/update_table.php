<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';

if(!is_numeric($_POST['stepIndex']) || !is_numeric($_POST['packageID']) || !isset($_POST['data']))
	exit();

// === GET ALT NUMBER ===
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$connect->exec("UPDATE Explainer SET sData = :data WHERE nPackageID = :packageID AND nStepIndex = :stepIndex",
[
	'data' => $_POST['data'],
	'packageID' => $_POST['packageID'],
	'stepIndex' => $_POST['stepIndex']
]);

exit();
