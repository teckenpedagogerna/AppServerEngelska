<?php

//include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$data = $connect->query("SELECT Data.*, Zone.nID AS nZoneID FROM Data LEFT JOIN Package ON Package.nID = Data.nPackageID LEFT JOIN Zone ON Package.nZoneID = Zone.nID ORDER BY Data.nStepIndex",
[

]);

$connect->exec("UPDATE Texts SET bUpdated = 0, nUsage = 0, sFoundAt = ''");

//$words = [];
for ($i=0; $i < count($data); $i++)
{
	$w = [];
	$s = [];
	$d = json_decode($data[$i]['sData']);

	switch ($d->quizType)
	{
		case 'isTrueImage':
			// is image true
			{
				if(strpos($d->question, " "))
				{
					array_push($w, $d->question);
					array_push($w, explode('_', $d->answer)[1]);
					array_push($s, $d->content);
				}
			}
			break;
		case 'messageAnswer':
			// message answer
			{
				$content = explode('_', $d->content);
				array_push($w, $d->question);
				for ($c = 0; $c < count($content); $c++)
				{ 
					array_push($w, $content[$c]);
				}
			}
			break;
		case 'selectAnswerToImage':
			// message answer
			{
				$content = explode('_', $d->content);
				array_push($w, $d->question);
				for ($c = 0; $c < count($content); $c++)
				{ 
					array_push($w, $content[$c]);
				}
			}
			break;
		case 'messageQuestion':
			// message question
			{
				$content = explode('_', $d->content);
				array_push($w, $d->question);
				for ($c = 0; $c < count($content); $c++)
				{ 
					array_push($w, $content[$c]);
				}
			}
			break;
		case 'buildText':
			{
				$s = $d->question;
				$w = $d->content;
			}
			break;
		case 'dragDropIntoText':
			{
				$w = $d->answer;
			}
			break;
		case 'selectMatchingWord':
			{
				$w = str_replace('{0}', explode('_', $d->content)[intval($d->answer)], $d->question);
			}
			break;
		case 'wordNotBelong':
			{
				$content = explode(' ', $d->question);
				$t = "";
				for ($c = 0; $c < count($content); $c++)
					if($d->answer != $c) 
						$t = $t . ' ' . $content[$c];
				$w = substr($t, 1);
			}
			break;
		case 'pairedText':
			// message question
			{
				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
				{ 
					array_push($w, $content[$c]);
				}
			}
			break;
		default:
			break;
	}

	// english

	if(is_array($w))
	{
		for ($q = 0; $q < count($w); $q++)
		{
			$w[$q] = str_replace('?', '', $w[$q]);
			$w[$q] = str_replace('.', '', $w[$q]);
			$w[$q] = str_replace(',', '', $w[$q]);

			$query = $connect->query("SELECT * FROM Texts WHERE sText = :text AND nLanguageID = 2",
			[
				"text" => strtolower($w[$q])
			]);

			if(count($query) == 0)
			{
				$connect->exec("INSERT INTO Texts (sText, bUpdated, nUsage, sFoundAt, nLanguageID) VALUES (:text, 1, 1, :foundAt, 2)",
				[
					"text" => strtolower($w[$q]),
					'foundAt' => $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
				]);
			}
			else
			{
				$comma = $query[0]['sFoundAt'] != '' ? ';' : '';
				$connect->exec("UPDATE Texts SET bUpdated = 1, nUsage = nUsage + 1, sFoundAt = :foundAt WHERE sText = :text AND nLanguageID = 2",
				[
					"text" => strtolower($w[$q]),
					'foundAt' => $query[0]['sFoundAt'] . $comma . $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
				]);
			}
		}
	}
	else
	{
		$w = str_replace('?', '', $w);
		$w = str_replace('.', '', $w);
		$w = str_replace(',', '', $w);

		$query = $connect->query("SELECT * FROM Texts WHERE sText = :text AND nLanguageID = 2",
		[
			"text" => strtolower($w)
		]);

		if(count($query) == 0)
		{
			$connect->exec("INSERT INTO Texts (sText, bUpdated, nUsage, sFoundAt, nLanguageID) VALUES (:text, 1, 1, :foundAt, 2)",
			[
				"text" => strtolower($w),
				'foundAt' => $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
			]);
		}
		else
		{
			$comma = $query[0]['sFoundAt'] != '' ? ';' : '';
			$connect->exec("UPDATE Texts SET bUpdated = 1, nUsage = nUsage + 1, sFoundAt = :foundAt WHERE sText = :text AND nLanguageID = 2",
			[
				"text" => strtolower($w),
				'foundAt' => $query[0]['sFoundAt'] . $comma . $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
			]);
		}
	}

	// swedish

	if(is_array($s))
	{
		for ($q = 0; $q < count($s); $q++)
		{
			$s[$q] = str_replace('?', '', $s[$q]);
			$s[$q] = str_replace('.', '', $s[$q]);
			$s[$q] = str_replace(',', '', $s[$q]);

			$query = $connect->query("SELECT * FROM Texts WHERE sText = :text AND nLanguageID = 1",
			[
				"text" => strtolower($s[$q])
			]);

			if(count($query) == 0)
			{
				$connect->exec("INSERT INTO Texts (sText, bUpdated, nUsage, sFoundAt, nLanguageID) VALUES (:text, 1, 1, :foundAt, 1)",
				[
					"text" => strtolower($s[$q]),
					'foundAt' => $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
				]);
			}
			else
			{
				$comma = $query[0]['sFoundAt'] != '' ? ';' : '';
				$connect->exec("UPDATE Texts SET bUpdated = 1, nUsage = nUsage + 1, sFoundAt = :foundAt WHERE sText = :text AND nLanguageID = 1",
				[
					"text" => strtolower($s[$q]),
					'foundAt' => $query[0]['sFoundAt'] . $comma . $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
				]);
			}
		}
	}
	else
	{
		$s = str_replace('.', '', $s);
		$s = str_replace(',', '', $s);
		$s = str_replace('?', '', $s);

		$query = $connect->query("SELECT * FROM Texts WHERE sText = :text AND nLanguageID = 1",
		[
			"text" => strtolower($s)
		]);

		if(count($query) == 0)
		{
			$connect->exec("INSERT INTO Texts (sText, bUpdated, nUsage, sFoundAt, nLanguageID) VALUES (:text, 1, 1, :foundAt, 1)",
			[
				"text" => strtolower($s),
				'foundAt' => $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
			]);
		}
		else
		{
			$comma = $query[0]['sFoundAt'] != '' ? ';' : '';
			$connect->exec("UPDATE Texts SET bUpdated = 1, nUsage = nUsage + 1, sFoundAt = :foundAt WHERE sText = :text AND nLanguageID = 1",
			[
				"text" => strtolower($s),
				'foundAt' => $query[0]['sFoundAt'] . $comma . $data[$i]['nZoneID'] . '.' . $data[$i]['nPackageID'] . '.' . $data[$i]['nLessonID'] . '.' . $data[$i]['nStepIndex']
			]);
		}
	}
}

$connect->exec("UPDATE Texts SET sText = LOWER(sText)");
$connect->exec("DELETE FROM Texts WHERE bUpdated = 0");
$connect->close();
include_once __DIR__ . '/RetranslateVideosText.php';
