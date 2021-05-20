<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

if(!is_numeric($_POST["packageID"]) || !is_numeric($_POST["lessonID"]) || !is_numeric($_POST["stepIndex"]) || !is_numeric($_POST["index"]) || !is_numeric($_POST["language"]) || !isset($_POST["word"]))
	exit();

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$video;
$target;

$_POST["word"] = str_replace('?', '', $_POST["word"]);
$_POST["word"] = str_replace('.', '', $_POST["word"]);
$_POST["word"] = str_replace(',', '', $_POST["word"]);

$_POST["word"] = strtolower($_POST["word"]);

if($_POST["language"] == 1)
{
	$target = $connect->query("SELECT t.nID, w.sWord FROM Words AS w INNER JOIN Translations AS t ON t.nSwedishID = w.nID WHERE w.sWord = :word",
	[
	    "word" => $_POST["word"]
	]);
}
else if($_POST["language"] == 2)
{
	$target = $connect->query("SELECT t.nID, w.sWord FROM Words AS w INNER JOIN Translations AS t ON t.nEnglishID = w.nID WHERE w.sWord = :word",
	[
	    "word" => $_POST["word"]
	]);
}

$managedVideos = [];

for($i = 0; $i < count($target); $i++)
{
	if($_POST["language"] == 1)
	{
		$video = $connect->query("SELECT wv.nAltID, wv.nSignLanguage, wv.sTranslation FROM Words AS w INNER JOIN Translations AS t ON t.nSwedishID = w.nID INNER JOIN WordVideos AS wv ON wv.nWordID = t.nID WHERE w.sWord = :word AND w.nLanguageID = 1 AND t.nID = :id",
		[
			'id' => intval($target[$i]['nID']),
		    "word" => $_POST["word"]
		]);
	}
	else if($_POST["language"] == 2)
	{
		$video = $connect->query("SELECT wv.nAltID, wv.nSignLanguage, wv.sTranslation FROM Words AS w INNER JOIN Translations AS t ON t.nEnglishID = w.nID INNER JOIN WordVideos AS wv ON wv.nWordID = t.nID WHERE w.sWord = :word AND w.nLanguageID = 2 AND t.nID = :id",
		[
			'id' => intval($target[$i]['nID']),
		    "word" => $_POST["word"]
		]);
	}


	$managedVideo = 
	[
		'id' => (int)$target[$i]['nID'],
		'video' => [[], [], []],
		'translation' => [[], [], []]
	];

	$videoData = $connect->query("SELECT sData FROM WordLinkedVideos WHERE sIndex = :index",
	[
		"index" => $_POST["packageID"] . '-' . $_POST["lessonID"] . '-' . $_POST["stepIndex"] . '-' . $_POST["index"]
	]);

	for ($x = 0; $x < count($video); $x++)
	{
		$managedVideo['video'][intval($video[$x]['nSignLanguage'])][] = $video[$x]['nAltID'] . ':0';
		$managedVideo['translation'][intval($video[$x]['nSignLanguage'])][] = $video[$x]['sTranslation'];
	}

	if(count($videoData) != 0)
	{
		$data = json_decode($videoData[0]['sData']);

		for ($x = 1; $x < count($data->video); $x++)
		{
			for ($a = 0; $a < count($data->video[$x]); $a++)
			{
				for ($b = 0; $b < count($managedVideo['video'][$x]); $b++)
				{
					if(explode(":", $data->video[$x][$a])[0] == explode(":", $managedVideo['video'][$x][$b])[0])
					{
						$managedVideo['video'][$x][$b] = $data->video[$x][$a];
					}
				}
			}
		}
	}

	$managedVideos[$i] = $managedVideo;
}

echo json_encode($managedVideos);
exit();
