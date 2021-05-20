<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

ignore_user_abort(true);
set_time_limit(0);
ini_set("memory_limit", "-1");

// Create downloadable database
if(file_exists(__DIR__ . '/database.sqlite'))
	unlink(__DIR__ . '/database.sqlite');
$database = new SQLite3(__DIR__ . '/database.sqlite');

// Connect to database
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

// Get Explain data
$explainer = $connect->query("SELECT nID, nZoneID, nPackageID, nStepIndex, sData FROM Explainer");
$explainerLink = $connect->query("SELECT nPackageID, sTitle, sRecognition FROM ExplainerLink");

// Relink videos
$relinked = $connect->query("
	SELECT p.nID AS PackageID, l.nID AS LessonID, d.nStepIndex AS StepIndex, d.sData AS Data FROM Zone AS z 
	INNER JOIN Package p ON z.nID = p.nZoneID 
	INNER JOIN Lesson l ON l.nPackageID = p.nID
	INNER JOIN Data d ON l.nPackageID = d.nPackageID AND d.nLessonID = l.nID
	WHERE z.bActivated = 1
");

$wordList = $connect->query("SELECT t.nID, w.sWord AS sEnglishWord, ws.sWord AS sSwedishWord, t.nSwedishID, t.nEnglishID FROM Translations AS t INNER JOIN Words AS w ON w.nID = t.nEnglishID AND w.nLanguageID = 2 LEFT JOIN Words ws ON t.nSwedishID = ws.nID AND ws.nLanguageID = 1 WHERE t.bIsText = 0");

function Autocorrect($string)
{
	$string = ltrim($string, " ");
	$string = rtrim($string, " ");

	while(strpos($string, "  "))
	{
		$string = str_replace("  ", " ", $string);
	}

	return $string;
}

// only words
for ($i=0; $i < count($relinked); $i++)
{
	$d = json_decode($relinked[$i]["Data"]);

	// --- autocorrect ---
	/*
	if(isset($d->question) && !is_numeric($d->question))
		$d->question = Autocorrect($d->question);

	if(isset($d->content) && !is_numeric($d->content))
		$d->content = Autocorrect($d->content);

	if(isset($d->answer) && !is_numeric($d->answer))
		$d->answer = Autocorrect($d->answer);
	*/

	// --- end autocorrect ---

	$w = [];
	$s = [];

	// get word data
	switch ($d->quizType)
	{
		case 'selectPicture':
			// select picture
			{
				array_push($w, Autocorrect($d->question));
			}
			break;
		case 'isTrueImage':
			// is image true
			{
				array_push($w, Autocorrect($d->question));
			}
			break;
		case 'buildWord':
			{
				array_push($s, Autocorrect($d->question));
			}
			break;
		case 'selectWord':
			{
				array_push($s, Autocorrect($d->question));

				array_push($s, null);
				array_push($s, null);
				array_push($s, null);
				array_push($s, null);

				array_push($w, null);

				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					array_push($w, Autocorrect($content[$c]));
			}
			break;
		case 'selectWordReverse':
			{
				array_push($w, Autocorrect($d->question));

				array_push($w, null);
				array_push($w, null);
				array_push($w, null);
				array_push($w, null);

				array_push($s, null);

				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					array_push($s, Autocorrect($content[$c]));
			}
			break;
		case 'selectMatchingWordToImage':
			{
				array_push($s, Autocorrect($d->question));

				array_push($s, null);
				array_push($s, null);
				array_push($s, null);
				array_push($s, null);

				array_push($w, null);

				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					array_push($w, Autocorrect($content[$c]));
			}
			break;
		case 'selectMatchingWordToImageReverse':
			{
				array_push($w, Autocorrect($d->question));

				array_push($w, null);
				array_push($w, null);
				array_push($w, null);
				array_push($w, null);

				array_push($s, null);

				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					array_push($s, Autocorrect($content[$c]));
			}
			break;
		case 'selectPictureAndWord':
			{
				array_push($s, Autocorrect($d->question));

				array_push($s, null);
				array_push($s, null);
				array_push($s, null);
				array_push($s, null);

				array_push($w, null);

				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					array_push($w, Autocorrect($content[$c]));
			}
			break;
		case 'selectMatchingWord':
			{
				$content = explode('_', $d->content);
				array_push($w, null);
				for ($c = 0; $c < count($content); $c++)
				{ 
					array_push($w, Autocorrect($content[$c]));
				}
			}
			break;
		default:
			break;
	}

	$tw = [];
	$ts = [];
	// get text data
	//error fix this
	
	switch ($d->quizType)
	{
		case 'messageAnswer':
			// message answer
			{
				$content = explode('_', $d->content);
				array_push($tw, Autocorrect($d->question));
				for ($c = 0; $c < count($content); $c++)
				{ 
					array_push($tw, Autocorrect($content[$c]));
				}
			}
			break;
		case 'messageQuestion':
			// message question
			{
				$content = explode('_', $d->content);
				array_push($tw, Autocorrect($d->question));
				for ($c = 0; $c < count($content); $c++)
				{ 
					array_push($tw, Autocorrect($content[$c]));
				}
			}
			break;
		case 'buildText':
			{
				$ts = [Autocorrect($d->question)];
			}
			break;
		case 'dragDropIntoText':
			{
				$tw = [$d->answer];
			}
			break;
		case 'selectMatchingWord':
			{
				$tw = [str_replace('{0}', explode('_', $d->content)[intval($d->answer)], Autocorrect($d->question))];
			}
			break;
		case 'wordNotBelong':
			{
				$content = explode(' ', Autocorrect($d->question));
				$t = "";
				for ($c = 0; $c < count($content); $c++)
					if($d->answer != $c) 
						$t = $t . ' ' . Autocorrect($content[$c]);
				$tw = [substr($t, 1)];
			}
			break;
		default:
			break;
	}

	// only words
	// ENGLISH
	for ($q = 0; $q < count($w); $q++)
	{
		$w[$q] = str_replace(',', '', $w[$q]);

		if(!empty($w[$q]))
		{
			$query = $connect->query("SELECT t.nID, v.nAltID, v.nSignLanguage, w.sWord FROM Words AS w INNER JOIN Translations AS t ON w.nID = t.nEnglishID INNER JOIN WordVideos AS v ON v.nWordID = t.nID WHERE w.sWord = :word",
			[
				"word" => strtolower($w[$q])
			]);

			if(count($query) != 0)
			{
				$index = $relinked[$i]['PackageID'] . '-' . $relinked[$i]['LessonID'] . '-' . $relinked[$i]['StepIndex'] . '-' . (string)$q;

				$linkedVideo = $connect->query("SELECT lv.* FROM WordLinkedVideos AS lv WHERE lv.sIndex = :index",
				[
					"index" => $index
				]);

				if(count($linkedVideo) == 0)
				{
					$alts1 = '';
					$alts2 = '';

					for ($a = 0; $a < count($query); $a++)
					{
						if(intval($query[$a]['nSignLanguage']) == 1)
							$alts1 = $alts1 . ',"' . $query[$a]['nAltID'] . ':1"';
						else
							$alts2 = $alts2 . ',"' . $query[$a]['nAltID'] . ':1"';
					}

					if(!empty($alts1))
						$alts1 = substr($alts1, 1);
					if(!empty($alts2))
						$alts2 = substr($alts2, 1);

					$json = '{"id":' . $query[0]['nID'] . ',"video":[[],['
						. $alts1 .
					'],[' 
						. $alts2 .
					']]}';

					$connect->exec("INSERT INTO WordLinkedVideos (sIndex, sData, nTranslationID, bUpdated) VALUES (:index, :data, :translationID, 1)",
					[
						"index" => $index,
						"data" => $json,
						"translationID" => $query[0]['nID']
					]);
				}
				/*
				else
				{
					$connect->exec("UPDATE WordLinkedVideos SET bUpdated = 1 WHERE sIndex = :index",
					[
						"index" => $index
					]);
				}
				*/
			}
		}
	}

	// SWEDISH
	for ($q = 0; $q < count($s); $q++)
	{
		$s[$q] = str_replace(',', '', $s[$q]);

		if(!empty($s[$q]))
		{
			$query = $connect->query("SELECT t.nID, v.nAltID, v.nSignLanguage, w.sWord FROM Words AS w INNER JOIN Translations AS t ON w.nID = t.nSwedishID INNER JOIN WordVideos AS v ON v.nWordID = t.nID WHERE w.sWord = :word",
			[
				"word" => strtolower($s[$q])
			]);

			if(count($query) != 0)
			{
				$index = $relinked[$i]['PackageID'] . '-' . $relinked[$i]['LessonID'] . '-' . $relinked[$i]['StepIndex'] . '-' . (string)$q;

				$linkedVideo = $connect->query("SELECT lv.* FROM WordLinkedVideos AS lv WHERE lv.sIndex = :index",
				[
					"index" => $index
				]);

				if(count($linkedVideo) == 0)
				{
					$alts1 = '';
					$alts2 = '';
					
					for ($a = 0; $a < count($query); $a++)
					{
						if($query[$a]['nSignLanguage'] == 1)
							$alts1 = $alts1 . ',"' . $query[$a]['nAltID'] . ':1"';
						else
							$alts2 = $alts2 . ',"' . $query[$a]['nAltID'] . ':1"';
					}

					if(!empty($alts1))
						$alts1 = substr($alts1, 1);
					if(!empty($alts2))
						$alts2 = substr($alts2, 1);

					$json = '{"id":' . $query[0]['nID'] . ',"video":[[],['
						. $alts1 .
					'],[' 
						. $alts2 .
					']]}';

					$connect->exec("INSERT INTO WordLinkedVideos (sIndex, sData, nTranslationID, bUpdated) VALUES (:index, :data, :translationID, 1)",
					[
						"index" => $index,
						"data" => $json,
						"translationID" => $query[0]['nID']
					]);
				}
				/*
				else
				{
					$connect->exec("UPDATE WordLinkedVideos SET bUpdated = 1 WHERE sIndex = :index",
					[
						"index" => $index
					]);
				}
				*/
			}
		}
	}

	// only texts
	// ENGLISH
	for ($q = 0; $q < count($tw); $q++)
	{
		$tw[$q] = str_replace(',', '', $tw[$q]);

		if(!empty($tw[$q]))
		{
			$index = $relinked[$i]['PackageID'] . '-' . $relinked[$i]['LessonID'] . '-' . $relinked[$i]['StepIndex'] . '-' . (string)$q;

			$query = $connect->query("SELECT t.nID, v.nAltID, v.nSignLanguage, w.sText FROM Texts AS w INNER JOIN Translations AS t ON w.nID = t.nEnglishID INNER JOIN TextVideos AS v ON v.nTextID = t.nID WHERE w.sText = :text",
			[
				"text" => strtolower($tw[$q])
			]);

			if(count($query) != 0)
			{
				$linkedVideo = $connect->query("SELECT lv.* FROM TextLinkedVideos AS lv WHERE lv.sIndex = :index",
				[
					"index" => $index
				]);

				if(count($linkedVideo) == 0)
				{
					$alts1 = '';
					$alts2 = '';
					
					for ($a = 0; $a < count($query); $a++)
					{
						if($query[$a]['nSignLanguage'] == 1)
							$alts1 = $alts1 . ',"' . $query[$a]['nAltID'] . ':1"';
						else
							$alts2 = $alts2 . ',"' . $query[$a]['nAltID'] . ':1"';
					}

					if(!empty($alts1))
						$alts1 = substr($alts1, 1);
					if(!empty($alts2))
						$alts2 = substr($alts2, 1);

					$json = '{"id":' . $query[0]['nID'] . ',"video":[[],['
						. $alts1 .
					'],[' 
						. $alts2 .
					']]}';

					$connect->exec("INSERT INTO TextLinkedVideos (sIndex, sData, nTranslationID, bUpdated) VALUES (:index, :data, :translationID, 1)",
					[
						"index" => $index,
						"data" => $json,
						"translationID" => $query[0]['nID']
					]);

				}
				/*
				else
				{
					$connect->exec("UPDATE TextLinkedVideos SET bUpdated = 1 WHERE sIndex = :index",
					[
						"index" => $index
					]);
				}
				*/
			}
		}
	}

	// SWEDISH
	for ($q = 0; $q < count($ts); $q++)
	{
		$ts[$q] = str_replace(',', '', $ts[$q]);

		if(!empty($ts[$q]))
		{
			$index = $relinked[$i]['PackageID'] . '-' . $relinked[$i]['LessonID'] . '-' . $relinked[$i]['StepIndex'] . '-' . (string)$q;


			$query = $connect->query("SELECT t.nID, v.nAltID, v.nSignLanguage, w.sText FROM Texts AS w INNER JOIN Translations AS t ON w.nID = t.nSwedishID INNER JOIN TextVideos AS v ON v.nTextID = t.nID WHERE w.sText = :text",
			[
				"text" => strtolower($ts[$q])
			]);

			if(count($query) != 0)
			{
				$linkedVideo = $connect->query("SELECT lv.* FROM TextLinkedVideos AS lv WHERE lv.sIndex = :index",
				[
					"index" => $index
				]);

				if(count($linkedVideo) == 0)
				{

					$alts1 = '';
					$alts2 = '';
					
					for ($a = 0; $a < count($query); $a++)
					{
						if($query[$a]['nSignLanguage'] == 1)
							$alts1 = $alts1 . ',"' . $query[$a]['nAltID'] . ':1"';
						else
							$alts2 = $alts2 . ',"' . $query[$a]['nAltID'] . ':1"';
					}

					if(!empty($alts1))
						$alts1 = substr($alts1, 1);
					if(!empty($alts2))
						$alts2 = substr($alts2, 1);

					$json = '{"id":' . $query[0]['nID'] . ',"video":[[],['
						. $alts1 .
					'],[' 
						. $alts2 .
					']]}';

					$connect->exec("INSERT INTO TextLinkedVideos (sIndex, sData, nTranslationID, bUpdated) VALUES (:index, :data, :translationID, 1)",
					[
						"index" => $index,
						"data" => $json,
						"translationID" => $query[0]['nID']
					]);
				}
				/*
				else
				{
					$connect->exec("UPDATE TextLinkedVideos SET bUpdated = 1 WHERE sIndex = :index",
					[
						"index" => $index
					]);
				}
				*/
			}
		}
	}
}

$videoWord = $connect->query("SELECT nWordID, nAltID, nSignLanguage FROM WordVideos");
$videoText = $connect->query("SELECT nTextID, nAltID, nSignLanguage FROM TextVideos");

// Precreate tables
$database->exec('CREATE TABLE WordList (nID INTEGER, nSwedishID INTEGER, nEnglishID INTEGER, sEnglishWord TEXT, sSwedishWord TEXT)');
$database->exec('CREATE TABLE ExplainerLink (nPackageID INTEGER, sTitle TEXT, sRecognition TEXT)');
$database->exec('CREATE TABLE Explainer (nID INTEGER, nZoneID INTEGER, nPackageID INTEGER, nStepIndex INTEGER, sData TEXT)');
$database->exec('CREATE TABLE Zone (nID INTEGER, sTitle TEXT, sDescription TEXT, bCompleted INTEGER DEFAULT 0)');
$database->exec('CREATE TABLE Package (nID INTEGER, nStepIndex INTEGER, nZoneID INTEGER, sTitle TEXT)');
$database->exec('CREATE TABLE Lesson (nID INTEGER, nPackageID INTEGER, sTitle TEXT, sRecognition TEXT, nStepIndex INTEGER, bCompleted INTEGER DEFAULT 0)');
$database->exec('CREATE TABLE Data (nID INTEGER, nLessonID INTEGER, nPackageID INTEGER, sData TEXT, nStepIndex INTEGER)');
$database->exec('CREATE TABLE Recognition (nFirstCorrectStreak INTEGER, nCorrectCount INTEGER, nWrongCount INTEGER, dLastSeen INTEGER, sWord TEXT, sLinkData TEXT)');
$database->exec('CREATE TABLE VideoWord (sIndex TEXT, sData TEXT)');
$database->exec('CREATE TABLE VideoText (sIndex TEXT, sData TEXT)');
$database->exec('CREATE TABLE RawVideoText (nID INTEGER, nAltID INTEGER, nSignLanguage INTEGER)');
$database->exec('CREATE TABLE RawVideoWord (nID INTEGER, nAltID INTEGER, nSignLanguage INTEGER)');
$database->exec('CREATE TABLE ScoreHistory (nID INTEGER, nScore INTEGER, nMaxScore INTEGER)');

// Start inserting
// Wordlist
for($w = 0; $w < count($wordList); $w++)
{
	$database->exec('INSERT INTO WordList (nID, nSwedishID, nEnglishID, sEnglishWord, sSwedishWord) VALUES (' . $wordList[$w]['nID'] . ', ' . $wordList[$w]['nSwedishID'] . ', ' . $wordList[$w]['nEnglishID'] . ', \'' . SQLite3::escapeString($wordList[$w]['sEnglishWord']) . '\', \'' . SQLite3::escapeString($wordList[$w]['sSwedishWord'])  . '\')');
}

for($v = 0; $v < count($videoWord); $v++)
{
	$database->exec('INSERT INTO RawVideoWord (nID, nAltID, nSignLanguage) VALUES (' . $videoWord[$v]['nWordID'] . ', ' . $videoWord[$v]['nAltID'] . ', ' . $videoWord[$v]['nSignLanguage'] . ')');
}

for($v = 0; $v < count($videoText); $v++)
{
	$database->exec('INSERT INTO RawVideoText (nID, nAltID, nSignLanguage) VALUES (' . $videoText[$v]['nTextID'] . ', ' . $videoText[$v]['nAltID'] . ', ' . $videoText[$v]['nSignLanguage'] . ')');
}

// Explainer
for($e = 0; $e < count($explainer); $e++)
{
	$database->exec('INSERT INTO Explainer (nID, nZoneID, nPackageID, nStepIndex, sData) VALUES (' . $explainer[$e]['nID'] . ', ' . $explainer[$e]['nZoneID'] . ', ' . $explainer[$e]['nPackageID'] . ', ' . $explainer[$e]['nStepIndex'] . ', \'' . SQLite3::escapeString($explainer[$e]['sData'])  . '\')');
}

// ExplainerLink
for($e = 0; $e < count($explainerLink); $e++)
{
	$database->exec('INSERT INTO ExplainerLink (nPackageID, sTitle, sRecognition) VALUES (' . $explainerLink[$e]['nPackageID'] . ', \'' . SQLite3::escapeString($explainerLink[$e]['sTitle']) . '\', \'' . SQLite3::escapeString($explainerLink[$e]['sRecognition'])  . '\')');
}

// Zone
$zones = $connect->query("SELECT nID, sTitle, sDescription FROM Zone WHERE bActivated = 1");
for($z = 0; $z < count($zones); $z++)
{
	$database->exec('INSERT INTO Zone (nID, sTitle, sDescription) VALUES (' . $zones[$z]['nID'] . ', "' . SQLite3::escapeString($zones[$z]['sTitle']) . '", "' . SQLite3::escapeString($zones[$z]['sDescription'])  . '")');

	// Package
	$packages = $connect->query("SELECT nID, nZoneID, sTitle, nStepIndex FROM Package WHERE nZoneID = " . $zones[$z]['nID']);
	for($p = 0; $p < count($packages); $p++)
	{
		$database->exec('INSERT INTO Package (nID, nZoneID, nStepIndex, sTitle) VALUES (' . $packages[$p]['nID'] . ', ' . $packages[$p]['nZoneID'] . ', ' . $packages[$p]['nStepIndex'] . ', "' . SQLite3::escapeString($packages[$p]['sTitle'])  . '")');

		// Lesson
		$lessons = $connect->query("SELECT nID, nPackageID, sTitle, sRecognition, nStepIndex FROM Lesson WHERE nPackageID = " . $packages[$p]['nID']);
		for($l = 0; $l < count($lessons); $l++)
		{
			$database->exec('INSERT INTO Lesson (nID, nPackageID, sTitle, sRecognition, nStepIndex) 
				VALUES (' . $lessons[$l]['nID'] . ', ' . $lessons[$l]['nPackageID'] . ', "' . SQLite3::escapeString($lessons[$l]['sTitle']) . '", "' . SQLite3::escapeString($lessons[$l]['sRecognition']) . '", ' . $lessons[$l]['nStepIndex']  . ')');

			// Data
			$data = $connect->query("SELECT nID, nLessonID, nPackageID, sData, nStepIndex FROM Data WHERE nLessonID = " . $lessons[$l]['nID'] . " AND nPackageID = " . $lessons[$l]['nPackageID']);
			for($d = 0; $d < count($data); $d++)
			{
				$database->exec('INSERT INTO Data (nID, nLessonID, nPackageID, sData, nStepIndex) 
					VALUES (' . $data[$d]['nID'] . ', ' . $data[$d]['nLessonID'] . ', ' . $data[$d]['nPackageID'] . ", '" . SQLite3::escapeString($data[$d]['sData'])  . "', " . $data[$d]['nStepIndex'] . ')');
			}
		}
	}
}

// === INSERT VIDEOS ===
$videosWord = $connect->query("SELECT sIndex, sData FROM WordLinkedVideos");
for ($i = 0; $i < count($videosWord); $i++)
	$database->exec("INSERT INTO VideoWord (sIndex, sData) VALUES ('" . $videosWord[$i]['sIndex'] . "', '" . SQLite3::escapeString($videosWord[$i]['sData']) . "')");
$videosText = $connect->query("SELECT sIndex, sData FROM TextLinkedVideos");
for ($i = 0; $i < count($videosText); $i++)
	$database->exec("INSERT INTO VideoText (sIndex, sData) VALUES ('" . $videosText[$i]['sIndex'] . "', '" . SQLite3::escapeString($videosText[$i]['sData']) . "')");


$version = strtotime(date('Y-m-d H:i:s'));

file_put_contents(__DIR__ . '/package_version.php', '<?php echo "' . $version . '";');
echo $version;