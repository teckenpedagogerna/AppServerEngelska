<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
if (!isset($_POST['id']) || !isset($_POST['alt']) || !isset($_POST['signLanguage']) || !isset($_POST['isWord']))
	exit();

$id = intval($_POST['id']);
$alt = intval($_POST['alt']);
$signLanguage = intval($_POST['signLanguage']);
$isWord = intval($_POST['isWord']);

// === DATABASE ===
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$queueQuery = $connect->query("SELECT * FROM VideoQueue WHERE sName = :name",
[
	'name' => (string)$id . '-' . (string)$alt . '-' . (string)$signLanguage . ($isWord == 0 ? '_text' : '') . '.mp4'
]);


if(!empty($queueQuery))
{
	echo (string)$queueQuery[0]['nStage'] . ',' . $queueQuery[0]['sDB'];
	exit();
}
else
{
	if($isWord == 0)
	{
		if(empty($connect->query("SELECT * FROM TextVideos WHERE nTextID = :id AND nSignLanguage = :signLanguage AND nAltID = :alt AND bUploaded = 1",
		[
			'id' => $id,
			'signLanguage' => $signLanguage,
			'alt' => $alt
		])))
		{
			/*
			$connect->exec("DELETE FROM TextVideos WHERE nTextID = :id AND nSignLanguage = :signLanguage AND nAltID = :alt AND bUploaded = 0",
			[
				'id' => $id,
				'signLanguage' => $signLanguage,
				'alt' => $alt
			]);
			*/

			echo "1";
			exit();
		}
		else
		{
			echo "2";
			exit();
		}
	}
	else
	{
		if(empty($connect->query("SELECT * FROM WordVideos WHERE nWordID = :id AND nSignLanguage = :signLanguage AND nAltID = :alt  AND bUploaded = 1",
		[
			'id' => $id,
			'signLanguage' => $signLanguage,
			'alt' => $alt
		])))
		{
			/*
			$connect->exec("DELETE FROM WordVideos WHERE nWordID = :id AND nSignLanguage = :signLanguage AND nAltID = :alt  AND bUploaded = 0",
			[
				'id' => $id,
				'signLanguage' => $signLanguage,
				'alt' => $alt
			]);
			*/

			echo "1";
			exit();
		}
		else
		{
			echo "2";
			exit();
		}
	}
}

echo "-1";
exit();