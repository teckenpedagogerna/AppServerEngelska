<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

if(!isset($_POST['id']) || !isset($_POST['packageID']) || !isset($_POST['newPackageID']))
{
	exit();	
}

if($_POST['packageID'] == $_POST['newPackageID'])
{
	echo "SAME ID";
	exit();
}

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$lessonTarget = $connect->query("SELECT MAX(nID), MAX(nstepIndex) FROM Lesson WHERE nPackageID = :packageID",
	[
		"packageID" => $_POST["newPackageID"]
	]
);

if(empty($lessonTarget))
{
	echo "ID NOT FOUND";
	exit();
}

$newID = 1;
$newStep = 0;

if(!isset($lessonTarget[0]['MAX(nID)']) || !isset($lessonTarget[0]['MAX(nstepIndex)']))
{
	$havePackageID = $connect->query("SELECT nID FROM Package WHERE nID = :id",
		[
			"id" => $_POST["newPackageID"]
		]
	);

	if(empty($havePackageID))
	{
		echo "ID NOT FOUND";
		exit();
	}
}
else
{
	$newID = (int)$lessonTarget[0]['MAX(nID)'] + 1;
	$newStep = (int)$lessonTarget[0]['MAX(nstepIndex)'] + 1;
}

$connect->exec("UPDATE Lesson SET nstepIndex = :step, nPackageID = :newPackageID, nID = :newId WHERE nID = :id AND nPackageID = :packageID",
	[
		"id" => $_POST['id'],
		"newId" => $newID,
		"packageID" => $_POST['packageID'],
		"newPackageID" => $_POST['newPackageID'],
		"step" => $newStep
	]
);

$connect->exec("UPDATE Data SET nLessonID = :newLessonID, nPackageID = :newPackageID WHERE nPackageID = :packageID AND nLessonID = :lessonID",
	[
		"packageID" => $_POST['packageID'],
		"newPackageID" => $_POST['newPackageID'],
		"newLessonID" => $newID,
		"lessonID" => $_POST['id']
	]
);

echo "MOVED";
exit();