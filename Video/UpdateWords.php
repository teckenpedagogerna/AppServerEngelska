<?php

//include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$data = $connect->query("SELECT Data.*, Zone.nID AS nZoneID FROM Data LEFT JOIN Package ON Package.nID = Data.nPackageID LEFT JOIN Zone ON Package.nZoneID = Zone.nID ORDER BY Data.nStepIndex",
[

]);

$connect->exec("UPDATE Words SET bUpdated = 0, nUsage = 0, sFoundAt = ''");


//$words = [];
for ($i=0; $i < count($data); $i++)
{
	$w = [];
	$s = [];
	$d = json_decode($data[$i]['sData']);

	switch ($d->quizType)
	{
		case 'selectPicture':
			// select picture
			{
				$content = explode('_', $d->content);
				array_push($w, $d->question);
				for ($c = 0; $c < count($content); $c++)
				{ 
					array_push($w, $content[$c]);
				}
			}
			break;
		case 'isTrueImage':
			// is image true
			{
				if(!strpos($d->question, " "))
				{
					array_push($w, $d->question);
					array_push($w, explode('_', $d->answer)[1]);
					array_push($s, $d->content);
				}
			}
			break;
		case 'matchWords':
			// match words
			{
				$content = explode('_', $d->question);
				for ($c = 0; $c < count($content); $c++)
					array_push($w, $content[$c]);

				$content = explode('_', $d->answer);
				for ($c = 0; $c < count($content); $c++)
					array_push($s, $content[$c]);
			}
			break;
		case 'buildWord':
			{
				array_push($s, $d->question);
				array_push($w, $d->content);
			}
			break;
		case 'buildText':
			{
				$content = explode(' ', $d->question);
				for ($c = 0; $c < count($content); $c++)
					array_push($s, $content[$c]);
			}
			break;
		case 'selectWord':
			{
				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					array_push($w, $content[$c]);

				array_push($s, $d->question);
			}
			break;
		case 'selectWordReverse':
			{
				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					array_push($s, $content[$c]);

				array_push($w, $d->question);
			}
			break;
		case 'selectMatchingWordToImage':
			{
				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					array_push($w, $content[$c]);

				array_push($s, $d->question);
			}
			break;
		case 'selectMatchingWordToImageReverse':
			{
				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					array_push($s, $content[$c]);

				array_push($w, $d->question);
			}
			break;
		case 'selectPictureAndWord':
			{
				array_push($s, $d->question);

				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					array_push($w, $content[$c]);
			}
			break;
		case 'selectMatchingWord':
			{
				$content = explode('_', $d->content);
				//array_push($w, $d->question);
				for ($c = 0; $c < count($content); $c++)
				{ 
					array_push($w, $content[$c]);
				}
			}
			break;
		default:
			break;
	}

	for ($q = 0; $q < count($w); $q++)
	{
		$w[$q] = str_replace(',', '', $w[$q]);
		$w[$q] = str_replace('.', '', $w[$q]);
		$w[$q] = str_replace('?', '', $w[$q]);

		if(!empty($w[$q]))
		{
			$query = $connect->query("SELECT * FROM Words WHERE sWord = :word AND nLanguageID = 2",
			[
				"word" => strtolower($w[$q])
			]);

			if(count($query) == 0)
			{
				$connect->exec("INSERT INTO Words (sWord, bUpdated, nUsage, sFoundAt, nLanguageID) VALUES (:word, 1, 1, :foundAt, 2)",
				[
					"word" => strtolower($w[$q]),
					'foundAt' => $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
				]);
			}
			else
			{
				$comma = $query[0]['sFoundAt'] != '' ? ';' : '';
				$connect->exec("UPDATE Words SET bUpdated = 1, nUsage = nUsage + 1, sFoundAt = :foundAt WHERE sWord = :word AND nLanguageID = 2",
				[
					"word" => strtolower($w[$q]),
					'foundAt' => $query[0]['sFoundAt'] . $comma . $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
				]);
			}
		}
	}

	/*---------------------------
		Här skripet går långsam
	---------------------------*/
	for ($q = 0; $q < count($s); $q++)
	{
		$s[$q] = str_replace(',', '', $s[$q]);
		$s[$q] = str_replace('.', '', $s[$q]);
		$s[$q] = str_replace('?', '', $s[$q]);

		if(!empty($s[$q]))
		{
			/*---------------------------
				Databasen lefter efter ett ord varje gånger den här loppar...
			---------------------------*/
			$query = $connect->query("SELECT * FROM Words WHERE sWord = :word AND nLanguageID = 1",
			[
				"word" => strtolower($s[$q])
			]);

			if(count($query) == 0)
			{
				$connect->exec("INSERT INTO Words (sWord, bUpdated, nUsage, sFoundAt, nLanguageID) VALUES (:word, 1, 1, :foundAt, 1)",
				[
					"word" => strtolower($s[$q]),
					'foundAt' => $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
				]);
			}
			else
			{
				$comma = $query[0]['sFoundAt'] != '' ? ';' : '';
				$connect->exec("UPDATE Words SET bUpdated = 1, nUsage = nUsage + 1, sFoundAt = :foundAt WHERE sWord = :word AND nLanguageID = 1",
				[
					"word" => strtolower($s[$q]),
					'foundAt' => $query[0]['sFoundAt'] . $comma . $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
				]);
			}
		}
	}
}

$connect->exec("UPDATE Words SET sWord = LOWER(sWord)");
$connect->exec("DELETE FROM Words WHERE bUpdated = 0");
$connect->close();
include_once __DIR__ . '/RetranslateVideosWord.php';
