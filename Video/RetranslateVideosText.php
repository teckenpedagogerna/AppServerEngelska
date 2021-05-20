<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

// === Retranslate videos ===
$textID = $connect->query("SELECT Texts.nID FROM Texts");
$connect->exec("UPDATE TextLinkedVideos SET bUpdated = 0");

for ($w = 0; $w < count($textID); $w++)
{
	$video = $connect->query("SELECT TextVideos.nAltID, TextVideos.nSignLanguage, TextVideos.sTranslation FROM Texts LEFT JOIN TextVideos ON TextVideos.nTextID = Texts.nID WHERE Texts.nID = :textID",
	[
	    "textID" => $textID[$w]['nID']
	]);

	$text = $connect->query("SELECT Texts.nID, Texts.sText FROM Texts WHERE Texts.nID = :textID",
	[
	    "textID" => $textID[$w]['nID']
	]);

	$videoData = $connect->query("SELECT sData, sIndex FROM TextLinkedVideos");

	if(count($videoData) != 0)
	{
		for ($i = 0; $i < count($text); $i++)
		{
			for ($v = 0; $v < count($videoData); $v++)
			{
				$data = json_decode($videoData[$v]['sData']);

				if($data->textID == $text[$i]['nID'])
				{
					$managedVideo = 
					[
						'id' => (int)$text[$i]['nID'],
						//'text' => $text[$i]['sText'],
						'video' => [[], [], []]
					];

					for ($x = 0; $x < count($video); $x++)
						$managedVideo['video'][intval($video[$x]['nSignLanguage'])][] = [$video[$x]['nAltID'] . ':0', $video[$x]['sTranslation']];

					if(count($managedVideo['video']) > 1)
					{
						for ($x = 1; $x < count($data->video); $x++)
						{
							for ($a = 0; $a < count($data->video[$x]); $a++)
							{
								for ($b = 0; $b < count($managedVideo['video'][$x]); $b++)
								{
									if(explode(":", $data->video[$x][$a])[0] == explode(":", $managedVideo['video'][$x][$b])[0])
									{
										$managedVideo['video'][$x][$b] = explode(":", $managedVideo['video'][$x][$b])[0] . ':' . explode(":", $data->video[$x][$a])[1];
									}
								}
							}
						}
						$connect->exec("UPDATE TextLinkedVideos SET sData = :data, bUpdated = 1 WHERE sIndex = :index",
						[
							"index" => $videoData[$v]['sIndex'],
							"data" => json_encode($managedVideo)
						]);
					}
				}
			}
		}
	}
}
$connect->exec("DELETE FROM TextLinkedVideos WHERE bUpdated = 0");
$connect->close();