<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//include_once __DIR__ . '/../package/update.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("UPDATE Data SET sData = :data WHERE nLessonID = :lessonID AND nPackageID = :packageID AND nStepIndex = :stepIndex",
	[
		"lessonID" => $_POST["lessonID"],
		"packageID" => $_POST["packageID"],
		"stepIndex" => $_POST["stepIndex"],
		"data" => $_POST["data"]
	]
);

$data = $connect->query("SELECT nID FROM Data WHERE nLessonID = :lessonID AND nPackageID = :packageID AND nStepIndex = :stepIndex",
	[
		"lessonID" => $_POST["lessonID"],
		"packageID" => $_POST["packageID"],
		"stepIndex" => $_POST["stepIndex"],
	]
);

for($i = 0; $i < count($data); $i++)
{
	$connect->exec("DELETE FROM Report WHERE nDataID = :id",
		[
			"id" => $data[$i]['nID']
		]
	);
}

echo "UPDATED";
exit();