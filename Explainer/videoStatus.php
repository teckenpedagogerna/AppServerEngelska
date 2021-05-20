<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
if (!isset($_POST['id']))
	exit();

$id = intval($_POST['id']);

// === DATABASE ===
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$queueQuery = $connect->query("SELECT * FROM VideoQueue WHERE sName = :name",
[
	'name' => 'explain_' . (string)$id . '.mp4'
]);


if(!empty($queueQuery))
{
	echo (string)$queueQuery[0]['nStage'] . ',' . $queueQuery[0]['sDB'];
	exit();
}
else
{
	/*
	if(empty($connect->query("SELECT * FROM WordVideos WHERE nWordID = :id AND nSignLanguage = :signLanguage AND nAltID = :alt  AND bUploaded = 1",
	[
		'id' => $id,
		'signLanguage' => $signLanguage,
		'alt' => $alt
	])))
	{
		echo "1";
		exit();
	}
	else
	{
		echo "2";
		exit();
	}
	*/
}

echo "-1";
exit();