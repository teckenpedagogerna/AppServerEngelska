<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

if(!is_numeric($_POST["packageID"]) || !is_numeric($_POST["lessonID"]) || !is_numeric($_POST["stepIndex"]) || !is_numeric($_POST["newStepIndex"]))
	exit();
if(!isset($_POST["packageID"]) || !isset($_POST["lessonID"]) || !isset($_POST["stepIndex"]) || !isset($_POST["newStepIndex"]))
	exit();

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

// data
$data = $connect->query("SELECT sData AS data FROM Data WHERE nPackageID = :packageID AND nLessonID = :lessonID AND (nStepIndex = :stepIndex OR nStepIndex = :newStepIndex) ORDER BY nStepIndex",
[
    "packageID" => $_POST["packageID"],
    "lessonID" => $_POST["lessonID"],
    "stepIndex" => $_POST["stepIndex"],
    "newStepIndex" => $_POST["newStepIndex"]
]);

if(count($data) < 2)
	exit();

$data0 = json_decode($data[0]['data']); // lower
$data1 = json_decode($data[1]['data']); // higher
$sOld = $_POST["stepIndex"];
$sNew = $_POST["newStepIndex"];

$indexOld = intval($_POST["packageID"]) . '-' . intval($_POST["lessonID"]) . '-' . intval($_POST["stepIndex"]) . '-';
$indexNew = intval($_POST["packageID"]) . '-' . intval($_POST["lessonID"]) . '-' . intval($_POST["newStepIndex"]) . '-';

// word video
$videoWordOld = $connect->query("SELECT sData AS data, sIndex, nTranslationID AS id FROM WordLinkedVideos WHERE sIndex LIKE \"" . $indexOld . "%\"");
$videoWordNew = $connect->query("SELECT sData AS data, sIndex, nTranslationID AS id FROM WordLinkedVideos WHERE sIndex LIKE \"" . $indexNew . "%\"");

// text video
$videoTextOld = $connect->query("SELECT sData AS data, sIndex, nTranslationID AS id FROM TextLinkedVideos WHERE sIndex LIKE \"" . $indexOld . "%\"");
$videoTextNew = $connect->query("SELECT sData AS data, sIndex, nTranslationID AS id FROM TextLinkedVideos WHERE sIndex LIKE \"" . $indexNew . "%\"");

// SWAP VIDEOS
$connect->exec("DELETE FROM WordLinkedVideos WHERE INSTR(sIndex, \"$indexOld\") OR INSTR(sIndex, \"$indexNew\")");
$connect->exec("DELETE FROM TextLinkedVideos WHERE INSTR(sIndex, \"$indexOld\") OR INSTR(sIndex, \"$indexNew\")");

// words
for($i = 0; $i < count($videoWordOld); $i++)
{
	$step = explode('-', $videoWordOld[$i]['sIndex']);

	var_dump($step[0] . '-' . $step[1] . '-' . $sNew . '-' . $step[3]);

	$connect->exec("INSERT INTO WordLinkedVideos (sIndex, sData, nTranslationID) VALUES (:index, :data, :id)",
	[
		"index" => $step[0] . '-' . $step[1] . '-' . $sNew . '-' . $step[3],
		"data" => $videoWordOld[$i]['data'],
		"id" => $videoWordOld[$i]['id']
	]);
}

for($i = 0; $i < count($videoWordNew); $i++)
{
	$step = explode('-', $videoWordNew[$i]['sIndex']);

	$connect->exec("INSERT INTO WordLinkedVideos (sIndex, sData, nTranslationID) VALUES (:index, :data, :id)",
	[
		"index" => $step[0] . '-' . $step[1] . '-' . $sOld . '-' . $step[3],
		"data" => $videoWordNew[$i]['data'],
		"id" => $videoWordNew[$i]['id']
	]);
}

// texts
for($i = 0; $i < count($videoTextOld); $i++)
{
	$step = explode('-', $videoTextOld[$i]['sIndex']);

	$connect->exec("INSERT INTO TextLinkedVideos (sIndex, sData, nTranslationID) VALUES (:index, :data, :id)",
	[
		"index" => $step[0] . '-' . $step[1] . '-' . $sNew . '-' . $step[3],
		"data" => $videoTextOld[$i]['data'],
		"id" => $videoTextOld[$i]['id']
	]);
}

for($i = 0; $i < count($videoTextNew); $i++)
{
	$step = explode('-', $videoTextNew[$i]['sIndex']);

	$connect->exec("INSERT INTO TextLinkedVideos (sIndex, sData, nTranslationID) VALUES (:index, :data, :id)",
	[
		"index" => $step[0] . '-' . $step[1] . '-' . $sOld . '-' . $step[3],
		"data" => $videoTextNew[$i]['data'],
		"id" => $videoTextNew[$i]['id']
	]);
}


if($sOld < $sNew) // DOWN
{
	$data0->stepIndex = $sNew;
	$data1->stepIndex = $sOld;

	$connect->exec("UPDATE Data SET sData = :data WHERE nPackageID = :packageID AND nLessonID = :lessonID AND nStepIndex = :stepIndex",
	[
	    "packageID" => $_POST["packageID"],
	    "lessonID" => $_POST["lessonID"],
	    "stepIndex" => $sNew,
	    "data" => json_encode($data0)
	]);

	$connect->exec("UPDATE Data SET sData = :data WHERE nPackageID = :packageID AND nLessonID = :lessonID AND nStepIndex = :stepIndex",
	[
	    "packageID" => $_POST["packageID"],
	    "lessonID" => $_POST["lessonID"],
	    "stepIndex" => $sOld,
	    "data" => json_encode($data1)
	]);

	echo "DOWN";
}
else if($sOld > $sNew) // UP
{
	$data0->stepIndex = $sOld;
	$data1->stepIndex = $sNew;

	$connect->exec("UPDATE Data SET sData = :data WHERE nPackageID = :packageID AND nLessonID = :lessonID AND nStepIndex = :stepIndex",
	[
	    "packageID" => $_POST["packageID"],
	    "lessonID" => $_POST["lessonID"],
	    "stepIndex" => $sOld,
	    "data" => json_encode($data0)
	]);

	$connect->exec("UPDATE Data SET sData = :data WHERE nPackageID = :packageID AND nLessonID = :lessonID AND nStepIndex = :stepIndex",
	[
	    "packageID" => $_POST["packageID"],
	    "lessonID" => $_POST["lessonID"],
	    "stepIndex" => $sNew,
	    "data" => json_encode($data1)
	]);
	
	echo "UP";
}
else
{
	exit();
}

exit();