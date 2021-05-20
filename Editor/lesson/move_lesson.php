<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//include_once __DIR__ . '/../package/update.php';

if(!isset($_POST['id']) || !isset($_POST['direction']) || !isset($_POST['packageID']))
{
	exit();	
}

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$lessonA = $connect->query("SELECT nID, nstepIndex, nPackageID FROM Lesson WHERE nID = :id AND nPackageID = :packageID",
	[
		"id" => $_POST["id"],
		"packageID" => $_POST["packageID"]
	]
);
$lessonB = null;
$dir = 0;

if($_POST['direction'] == 'UP')
{
	$dir = -1;
}
else
{
	$dir = 1;
}

$lessonB = $connect->query("SELECT nID, nstepIndex, nPackageID FROM Lesson WHERE nstepIndex = :step AND nPackageID = :packageID",
	[
		'packageID' => $_POST['packageID'],
		'step' => ((int)$lessonA[0]['nstepIndex']) + $dir
	]
);

//echo $lessonB[0]['nID'];
//exit();

$connect->exec("UPDATE Lesson SET nstepIndex = :step WHERE nID = :id AND nPackageID = :packageID",
	[
		"id" => $lessonA[0]['nID'],
		"packageID" => $lessonA[0]['nPackageID'],
		"step" => $lessonB[0]['nstepIndex']
	]
);

$connect->exec("UPDATE Lesson SET nstepIndex = :step WHERE nID = :id AND nPackageID = :packageID",
	[
		"id" => $lessonB[0]['nID'],
		"packageID" => $lessonB[0]['nPackageID'],
		"step" => $lessonA[0]['nstepIndex']
	]
);

echo $lessonB[0]['nID'];
exit();