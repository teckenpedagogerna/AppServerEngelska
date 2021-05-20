<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

// === Retranslate videos ===
$wordID = $connect->query("SELECT Words.nID FROM Words");
$connect->exec("UPDATE WordLinkedVideos SET bUpdated = 0");

for ($w = 0; $w < count($wordID); $w++)
{
	$video = $connect->query("SELECT WordVideos.nAltID, WordVideos.nSignLanguage, WordVideos.sTranslation FROM Words LEFT JOIN WordVideos ON WordVideos.nWordID = Words.nID WHERE Words.nID = :wordID",
	[
	    "wordID" => $wordID[$w]['nID']
	]);

	$word = $connect->query("SELECT Words.nID, Words.sWord FROM Words WHERE Words.nID = :wordID",
	[
	    "wordID" => $wordID[$w]['nID']
	]);

	$videoData = $connect->query("SELECT sData, sIndex FROM WordLinkedVideos");

	if(count($videoData) != 0)
	{
		for ($i = 0; $i < count($word); $i++)
		{
			for ($v = 0; $v < count($videoData); $v++)
			{
				$data = json_decode($videoData[$v]['sData']);

				if($data->wordID == $word[$i]['nID'])
				{
					$managedVideo = 
					[
						'id' => (int)$word[$i]['nID'],
						//'word' => $word[$i]['sWord'],
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
						$connect->exec("UPDATE WordLinkedVideos SET sData = :data, bUpdated = 1 WHERE sIndex = :index",
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
$connect->exec("DELETE FROM WordLinkedVideos WHERE bUpdated = 0");
$connect->close();