<?php
if(!is_numeric($_POST['packageID']) || !is_numeric($_POST['stepIndex']) || !is_numeric($_POST['newStepIndex']))
	exit();

require_once $_SERVER['LIBRARY'] . '/AWS/vendor/autoload.php';
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

// === DATA ===
$data = $connect->query("SELECT sData AS data, nID AS id FROM Explainer WHERE nPackageID = :packageID AND (nStepIndex = :stepIndex OR nStepIndex = :newStepIndex) ORDER BY nStepIndex",
[
    "packageID" => $_POST["packageID"],
    "stepIndex" => $_POST["stepIndex"],
    "newStepIndex" => $_POST["newStepIndex"]
]);

if(count($data) < 2)
	exit();

$data0 = json_decode($data[0]['data']); // lower
$data1 = json_decode($data[1]['data']); // higher

$id0 = intval($data[0]['id']); // lower
$id1 = intval($data[1]['id']); // higher

$sOld = $_POST["stepIndex"];
$sNew = $_POST["newStepIndex"];

if($sOld < $sNew) // DOWN
{
	$data0->stepIndex = $sNew;
	$data1->stepIndex = $sOld;

	$connect->exec("UPDATE Explainer SET sData = :data, nStepIndex = :stepIndex WHERE nPackageID = :packageID AND nID = :id",
	[
	    "packageID" => $_POST["packageID"],
	    "stepIndex" => $sNew,
	    "id" => $id0,
	    "data" => json_encode($data0)
	]);

	$connect->exec("UPDATE Explainer SET sData = :data, nStepIndex = :stepIndex WHERE nPackageID = :packageID AND nID = :id",
	[
	    "packageID" => $_POST["packageID"],
	    "stepIndex" => $sOld,
	    "id" => $id1,
	    "data" => json_encode($data1)
	]);

	echo "DOWN";
}
else if($sOld > $sNew) // UP
{
	$data0->stepIndex = $sOld;
	$data1->stepIndex = $sNew;

	$connect->exec("UPDATE Explainer SET sData = :data, nStepIndex = :stepIndex WHERE nPackageID = :packageID AND nID = :id",
	[
	    "packageID" => $_POST["packageID"],
	    "stepIndex" => $sOld,
	    "id" => $id0,
	    "data" => json_encode($data0)
	]);

	$connect->exec("UPDATE Explainer SET sData = :data, nStepIndex = :stepIndex WHERE nPackageID = :packageID AND nID = :id",
	[
	    "packageID" => $_POST["packageID"],
	    "stepIndex" => $sNew,
	    "id" => $id1,
	    "data" => json_encode($data1)
	]);
	
	echo "UP";
}
else
{
	exit();
}

exit();