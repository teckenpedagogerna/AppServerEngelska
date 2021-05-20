<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

if(!is_numeric($_POST["packageID"]) || !is_numeric($_POST["lessonID"]) || !is_numeric($_POST["stepIndex"]) || !is_numeric($_POST["index"]) || !is_numeric($_POST["language"]) || !isset($_POST["text"]))
	exit();

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$video;
$target;

$_POST["text"] = str_replace('?', '', $_POST["text"]);
$_POST["text"] = str_replace('.', '', $_POST["text"]);
$_POST["text"] = str_replace(',', '', $_POST["text"]);

$_POST["text"] = strtolower($_POST["text"]);

if($_POST["language"] == 1)
{
	$target = $connect->query("SELECT t.nID, w.sText FROM Texts AS w INNER JOIN Translations AS t ON t.nSwedishID = w.nID WHERE w.sText = :text",
	[
	    "text" => $_POST["text"]
	]);
}
else if($_POST["language"] == 2)
{
	$target = $connect->query("SELECT t.nID, w.sText FROM Texts AS w INNER JOIN Translations AS t ON t.nEnglishID = w.nID WHERE w.sText = :text",
	[
	    "text" => $_POST["text"]
	]);
}

$managedVideos = [];

for($i = 0; $i < count($target); $i++)
{
	if($_POST["language"] == 1)
	{
		$video = $connect->query("SELECT wv.nAltID, wv.nSignLanguage, wv.sTranslation FROM Texts AS w INNER JOIN Translations AS t ON t.nSwedishID = w.nID INNER JOIN TextVideos AS wv ON wv.nTextID = t.nID WHERE w.sText = :text AND w.nLanguageID = 1 AND t.nID = :id",
		[
			'id' => intval($target[$i]['nID']),
		    "text" => $_POST["text"]
		]);
	}
	else if($_POST["language"] == 2)
	{
		$video = $connect->query("SELECT wv.nAltID, wv.nSignLanguage, wv.sTranslation FROM Texts AS w INNER JOIN Translations AS t ON t.nEnglishID = w.nID INNER JOIN TextVideos AS wv ON wv.nTextID = t.nID WHERE w.sText = :text AND w.nLanguageID = 2 AND t.nID = :id",
		[
			'id' => intval($target[$i]['nID']),
		    "text" => $_POST["text"]
		]);
	}


	$managedVideo = 
	[
		'id' => (int)$target[$i]['nID'],
		'video' => [[], [], []]
	];

	$videoData = $connect->query("SELECT sData FROM TextLinkedVideos WHERE sIndex = :index",
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
						$managedVideo['video'][$x][$b] = $data->video[$x][$a]; //explode(":", $managedVideo['video'][$x][$b])[0] . ':' . explode(":", $data->video[$x][$a])[1];
					}
				}
			}
		}
	}

	$managedVideos[$i] = $managedVideo;
}

echo json_encode($managedVideos);
exit();
