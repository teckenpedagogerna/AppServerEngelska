<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

// === Retranslate videos ===

//$wordID = $connect->query("SELECT Words.nID FROM Words");
//$textID = $connect->query("SELECT Texts.nID FROM Texts");
//$data = $connect->query("SELECT sData FROM Data");

$relinked = $connect->query("
	SELECT p.nID AS PackageID, l.nID AS LessonID, d.nStepIndex AS StepIndex, d.sData AS Data FROM Zone AS z 
	INNER JOIN Package AS p ON z.nID = p.nZoneID 
	INNER JOIN Lesson AS l ON l.nPackageID = p.nID
	INNER JOIN Data AS d ON l.nPackageID = d.nPackageID AND d.nLessonID = l.nID
");

try {
		
	$connect->exec("UPDATE Translations SET bUpdated = 0");

	// === AUTO LINK ===

	// only words
	for ($i=0; $i < count($relinked); $i++)
	{
		$d = json_decode($relinked[$i]["Data"]);
		$w = "";
		$s = "";

		// get word data
		switch ($d->quizType)
		{
			case 'isTrueImage':
				{
					if(!strpos($d->answer, " "))
					{
						$a = explode('_', $d->answer);

						$s = $d->content;
						$w = $a[1];
					}
				}
				break;
			case 'matchWords':
				// match words
				{
					$w = [];
					$s = [];
					
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
					$w = $d->content;
					$s = $d->question;
				}
				break;
			case 'selectWord':
				{
					$w = explode('_', $d->content)[$d->answer];
					$s = $d->question;
				}
				break;
			case 'selectWordReverse':
				{
					$s = explode('_', $d->content)[$d->answer];
					$w = $d->question;
				}
				break;
			case 'selectMatchingWordToImage':
				{
					$s = $d->question;
					$w = explode('_', $d->content)[$d->answer];
				}
				break;
			case 'selectMatchingWordToImageReverse':
				{
					$w = $d->question;
					$s = explode('_', $d->content)[$d->answer];
				}
				break;
			case 'selectPictureAndWord':
				{
					$s = $d->question;
					$w = explode('_', $d->content)[$d->answer];
				}
				break;
			default:
				break;
		}

		$tw = '';
		$ts = '';

		// get text data	
		switch ($d->quizType)
		{
			case 'isTrueImage':
				{
					if(strpos($d->answer, " "))
					{
						$a = explode('_', $d->answer);

						$ts = $d->content;
						$tw = $a[1];
					}
				}
				break;
			case 'buildText':
				{
					$ts = $d->question;
					$tw = $d->content;
				}
				break;
			default:
				break;
		}

		// only words
		if(is_array($s) && is_array($w))
		{
			if(count($s) == count($w))
			{
				for ($q = 0; $q < count($w); $q++)
				{
					$w[$q] = str_replace('?', '', $w[$q]);
					$w[$q] = str_replace('.', '', $w[$q]);
					$w[$q] = str_replace(',', '', $w[$q]);

					$s[$q] = str_replace('?', '', $s[$q]);
					$s[$q] = str_replace('.', '', $s[$q]);
					$s[$q] = str_replace(',', '', $s[$q]);

					$english = $connect->query("SELECT w.nID, w.sFoundAt FROM Words AS w WHERE w.sWord = :word AND w.nLanguageID = 2",
					[
						"word" => strtolower($w[$q])
					]);

					$swedish = $connect->query("SELECT w.nID, w.sFoundAt FROM Words AS w WHERE w.sWord = :word AND w.nLanguageID = 1",
					[
						"word" => strtolower($s[$q])
					]);

					if(!empty($english) && !empty($swedish))
					{
						$translation = $connect->query("SELECT nID, bUpdated FROM Translations WHERE nEnglishID = :eID AND nSwedishID = :sID AND bIsText = 0",
						[
							"eID" => intval($english[0]['nID']),
							"sID" => intval($swedish[0]['nID'])
						]);

						if(count($translation) > 0)
						{
							if($translation[0]['bUpdated'] == 0)
							{
								$connect->exec("UPDATE Translations SET bUpdated = 1, sFoundAt = :foundAt WHERE bIsText = 0 AND nSwedishID = :sID AND nEnglishID = :eID",
									[
										"eID" => intval($english[0]['nID']),
										"sID" => intval($swedish[0]['nID']),
										"foundAt" => $swedish[0]['sFoundAt'] . ';' . $english[0]['sFoundAt']
									]
								);
							}
						}
						else if(count($translation) == 0)
						{
							$connect->exec("INSERT INTO Translations 
								(nSwedishID, nEnglishID, bIsText, bUpdated, sFoundAt) 
									VALUES 
								(:sID, :eID, 0, 1, :foundAt)",
								[
									"eID" => intval($english[0]['nID']),
									"sID" => intval($swedish[0]['nID']),
									"foundAt" => $swedish[0]['sFoundAt'] . ';' . $english[0]['sFoundAt']
								]
							);
						}
					}
				}
			}
		}
		else if(!empty($w) && !empty($s))
		{
			$w = str_replace('?', '', $w);
			$w = str_replace('.', '', $w);
			$w = str_replace(',', '', $w);

			$s = str_replace('?', '', $s);
			$s = str_replace('.', '', $s);
			$s = str_replace(',', '', $s);

			$english = $connect->query("SELECT w.nID, w.sFoundAt FROM Words AS w WHERE w.sWord = :word AND w.nLanguageID = 2",
			[
				"word" => strtolower($w)
			]);

			$swedish = $connect->query("SELECT w.nID, w.sFoundAt FROM Words AS w WHERE w.sWord = :word AND w.nLanguageID = 1",
			[
				"word" => strtolower($s)
			]);

			if(!empty($english) && !empty($swedish))
			{
				$translation = $connect->query("SELECT nID, bUpdated FROM Translations WHERE nEnglishID = :eID AND nSwedishID = :sID AND bIsText = 0",
				[
					"eID" => intval($english[0]['nID']),
					"sID" => intval($swedish[0]['nID'])
				]);

				if(count($translation) > 0)
				{
					if($translation[0]['bUpdated'] == 0)
					{
						$connect->exec("UPDATE Translations SET bUpdated = 1, sFoundAt = :foundAt WHERE bIsText = 0 AND nSwedishID = :sID AND nEnglishID = :eID",
							[
								"eID" => intval($english[0]['nID']),
								"sID" => intval($swedish[0]['nID']),
								"foundAt" => $swedish[0]['sFoundAt'] . ';' . $english[0]['sFoundAt']
							]
						);
					}
				}
				else if(count($translation) == 0)
				{
					$connect->exec("INSERT INTO Translations 
						(nSwedishID, nEnglishID, bIsText, bUpdated, sFoundAt) 
							VALUES 
						(:sID, :eID, 0, 1, :foundAt)",
						[
							"eID" => intval($english[0]['nID']),
							"sID" => intval($swedish[0]['nID']),
							"foundAt" => $swedish[0]['sFoundAt'] . ';' . $english[0]['sFoundAt']
						]
					);
				}
			}
		}

		// only texts
		if(!empty($tw) && !empty($ts))
		{
			$tw = str_replace('?', '', $tw);
			$tw = str_replace('.', '', $tw);
			$tw = str_replace(',', '', $tw);

			$ts = str_replace('?', '', $ts);
			$ts = str_replace('.', '', $ts);
			$ts = str_replace(',', '', $ts);

			$english = $connect->query("SELECT t.nID, t.sFoundAt FROM Texts AS t WHERE t.sText = :text AND t.nLanguageID = 2",
			[
				"text" => strtolower($tw)
			]);

			$swedish = $connect->query("SELECT t.nID, t.sFoundAt FROM Texts AS t WHERE t.sText = :text AND t.nLanguageID = 1",
			[
				"text" => strtolower($ts)
			]);

			if(!empty($english) && !empty($swedish))
			{
				$translation = $connect->query("SELECT nID, bUpdated FROM Translations WHERE nEnglishID = :eID AND nSwedishID = :sID AND bIsText = 1",
				[
					"eID" => intval($english[0]['nID']),
					"sID" => intval($swedish[0]['nID'])
				]);

				if(count($translation) > 0)
				{
					if($translation[0]['bUpdated'] == 0)
					{
						$connect->exec("UPDATE Translations SET bUpdated = 1, sFoundAt = :foundAt WHERE bIsText = 1 AND nSwedishID = :sID AND nEnglishID = :eID",
							[
								"eID" => intval($english[0]['nID']),
								"sID" => intval($swedish[0]['nID']),
								"foundAt" => $swedish[0]['sFoundAt'] . ';' . $english[0]['sFoundAt']
							]
						);
					}

				}
				else if(count($translation) == 0)
				{
					$connect->exec("INSERT INTO Translations 
						(nSwedishID, nEnglishID, bIsText, bUpdated, sFoundAt) 
							VALUES 
						(:sID, :eID, 1, 1, :foundAt)",
						[
							"eID" => intval($english[0]['nID']),
							"sID" => intval($swedish[0]['nID']),
							"foundAt" => $swedish[0]['sFoundAt'] . ';' . $english[0]['sFoundAt']
						]
					);
				}
			}
		}
	}
	// === END AUTO LINK ===

	// === START LINK ===
	for ($i=0; $i < count($relinked); $i++)
	{
		$d = json_decode($relinked[$i]["Data"]);
		$w = [];
		$s = [];
		$tw = [];
		$ts = [];

		switch ($d->quizType)
		{
			case 'selectPicture':
				// select picture
				{
					array_push($w, $d->question);

					$content = explode('_', $d->content);
					for ($c = 0; $c < count($content); $c++)
						array_push($w, $content[$c]);
				}
				break;
			case 'isTrueImage':
				// is image true
				{
					if(!strpos($d->answer, " "))
					{
						$a = explode('_', $d->answer);
						array_push($s, $d->content);
						array_push($w, $a[1]);
					}
				}
				break;
			case 'selectWord':
				{
					$content = explode('_', $d->content);
					for ($c = 0; $c < count($content); $c++)
						if(intval($d->answer) != $c)
							array_push($w, $content[$c]);
				}
				break;
			case 'selectWordReverse':
				{
					$content = explode('_', $d->content);
					for ($c = 0; $c < count($content); $c++)
						if(intval($d->answer) != $c)
							array_push($s, $content[$c]);
				}
				break;
			case 'selectMatchingWordToImage':
				{
					$content = explode('_', $d->content);
					for ($c = 0; $c < count($content); $c++)
						if(intval($d->answer) != $c)
							array_push($w, $content[$c]);
				}
				break;
			case 'selectMatchingWordToImageReverse':
				{
					$content = explode('_', $d->content);
					for ($c = 0; $c < count($content); $c++)
						if(intval($d->answer) != $c)
							array_push($s, $content[$c]);
				}
				break;
			case 'selectPictureAndWord':
				{
					$content = explode('_', $d->content);
					for ($c = 0; $c < count($content); $c++)
						if(intval($d->answer) != $c)
							array_push($w, $content[$c]);
				}
				break;
			case 'selectMatchingWord':
				{
					$content = explode('_', $d->content);
					for ($c = 0; $c < count($content); $c++)
						if(intval($d->answer) != $c)
							array_push($w, $content[$c]);
				}
				break;
			default:
				break;
		}

		switch ($d->quizType)
		{
			case 'isTrueImage':
				// is image true
				{
					if(strpos($d->answer, " "))
					{
						$a = explode('_', $d->answer);
						array_push($ts, $d->content);
						array_push($tw, $a[1]);
					}
				}
				break;
			case 'messageAnswer':
				// message answer
				{
					$content = explode('_', $d->content);
					array_push($tw, $d->question);
					for ($c = 0; $c < count($content); $c++)
					{ 
						array_push($tw, $content[$c]);
					}
				}
				break;
			case 'messageQuestion':
				// message question
				{
					$content = explode('_', $d->content);
					array_push($tw, $d->question);
					for ($c = 0; $c < count($content); $c++)
					{ 
						array_push($tw, $content[$c]);
					}
				}
				break;
			case 'selectAnswerToImage':
				// message question
				{
					$content = explode('_', $d->content);
					array_push($tw, $d->question);
					for ($c = 0; $c < count($content); $c++)
					{ 
						array_push($tw, $content[$c]);
					}
				}
				break;
			case 'buildText':
				{
					$ts = [$d->question];
				}
				break;
			case 'dragDropIntoText':
				{
					$tw = [$d->answer];
				}
				break;
			case 'selectMatchingWord':
				{
					$tw = [str_replace('{0}', explode('_', $d->content)[intval($d->answer)], $d->question)];
				}
				break;
			case 'wordNotBelong':
				{
					$content = explode(' ', $d->question);
					$t = "";
					for ($c = 0; $c < count($content); $c++)
						if($d->answer != $c) 
							$t = $t . ' ' . $content[$c];
					$tw = [substr($t, 1)];
				}
				break;
			case 'selectMatchingWord':
				{
					$tw [
						str_replace(
							"{0}", 
							explode('_', $d->content)[$d->answer],
							$d->question
						)
					];
				}
				break;
			default:
				break;
		}


		// words
		// ENGLISH
		for ($q = 0; $q < count($w); $q++)
		{
			$w[$q] = str_replace(',', '', $w[$q]);
			$w[$q] = str_replace('?', '', $w[$q]);
			$w[$q] = str_replace('.', '', $w[$q]);

			if(!empty($w[$q]))
			{
				$english = $connect->query("SELECT w.nID, w.sFoundAt, w.sWord FROM Words AS w WHERE w.sWord = :word AND w.nLanguageID = 2",
				[
					"word" => strtolower($w[$q])
				]);

				if(!empty($english))
				{
					$translation = $connect->query("SELECT nID, bUpdated FROM Translations WHERE nEnglishID = :eID AND nSwedishID != 0 AND bIsText = 0",
					[
						"eID" => intval($english[0]['nID'])
					]);

					if(count($translation) > 0)
					{
						if($translation[0]['bUpdated'] == 0)
						{
							$connect->exec("UPDATE Translations SET bUpdated = 1, sFoundAt = :foundAt, nSwedishID = 0 WHERE bIsText = 0 AND nEnglishID = :eID",
								[
									"eID" => intval($english[0]['nID']),
									"foundAt" => $english[0]['sFoundAt']
								]
							);
						}
					}
					else if(count($translation) == 0)
					{
						$translation = $connect->query("SELECT nID, bUpdated FROM Translations WHERE nEnglishID = :eID AND nSwedishID = 0 AND bIsText = 0",
						[
							"eID" => intval($english[0]['nID'])
						]);

						if(count($translation) > 0)
						{
							if($translation[0]['bUpdated'] == 0)
							{
								$connect->exec("UPDATE Translations SET bUpdated = 1, sFoundAt = :foundAt, nSwedishID = 0 WHERE bIsText = 0 AND nEnglishID = :eID",
									[
										"eID" => intval($english[0]['nID']),
										"foundAt" => $english[0]['sFoundAt']
									]
								);
							}
						}
						if(count($translation) == 0)
						{
							$connect->exec("INSERT INTO Translations 
								(nSwedishID, nEnglishID, bIsText, bUpdated, sFoundAt) 
									VALUES 
								(0, :eID, 0, 1, :foundAt)",
								[
									"eID" => intval($english[0]['nID']),
									"foundAt" => $english[0]['sFoundAt']
								]
							);
						}
					}
				}
			}
		}

		// SWEDISH
		for ($q = 0; $q < count($s); $q++)
		{
			$s[$q] = str_replace(',', '', $s[$q]);
			$s[$q] = str_replace('.', '', $s[$q]);
			$s[$q] = str_replace('?', '', $s[$q]);

			if(!empty($s[$q]))
			{
				$swedish = $connect->query("SELECT w.nID, w.sFoundAt FROM Words AS w WHERE w.sWord = :word AND w.nLanguageID = 1",
				[
					"word" => strtolower($s[$q])
				]);

				if(!empty($swedish))
				{
					$translation = $connect->query("SELECT nID, bUpdated FROM Translations WHERE nSwedishID = :sID AND nEnglishID != 0 AND bIsText = 0",
					[
						"sID" => intval($swedish[0]['nID'])
					]);

					if(count($translation) > 0)
					{
						if($translation[0]['bUpdated'] == 0)
						{
							$connect->exec("UPDATE Translations SET bUpdated = 1, sFoundAt = :foundAt, nEnglishID = 0 WHERE bIsText = 0 AND nSwedishID = :sID",
								[
									"sID" => intval($swedish[0]['nID']),
									"foundAt" => $swedish[0]['sFoundAt']
								]
							);
						}
					}
					else if(count($translation) == 0)
					{
						$translation = $connect->query("SELECT nID, bUpdated FROM Translations WHERE nSwedishID = :sID AND nEnglishID = 0 AND bIsText = 0",
						[
							"sID" => intval($swedish[0]['nID'])
						]);

						if(count($translation) > 0)
						{
							if($translation[0]['bUpdated'] == 0)
							{
								$connect->exec("UPDATE Translations SET bUpdated = 1, sFoundAt = :foundAt, nEnglishID = 0 WHERE bIsText = 0 AND nSwedishID = :sID",
									[
										"sID" => intval($swedish[0]['nID']),
										"foundAt" => $swedish[0]['sFoundAt']
									]
								);
							}
						}
						else if(count($translation) == 0)
						{
							$connect->exec("INSERT INTO Translations 
								(nSwedishID, nEnglishID, bIsText, bUpdated, sFoundAt) 
									VALUES 
								(:sID, 0, 0, 1, :foundAt)",
								[
									"sID" => intval($swedish[0]['nID']),
									"foundAt" => $swedish[0]['sFoundAt']
								]
							);
						}
					}
				}
			}
		}

		// texts
		// ENGLISH
		for ($q = 0; $q < count($tw); $q++)
		{
			$tw[$q] = str_replace('?', '', $tw[$q]);
			$tw[$q] = str_replace('.', '', $tw[$q]);
			$tw[$q] = str_replace(',', '', $tw[$q]);

			if(!empty($tw[$q]))
			{
				$english = $connect->query("SELECT w.nID, w.sFoundAt, w.sText FROM Texts AS w WHERE w.sText = :text AND w.nLanguageID = 2",
				[
					"text" => strtolower($tw[$q])
				]);

				if(!empty($english))
				{
					$translation = $connect->query("SELECT nID, bUpdated FROM Translations WHERE nEnglishID = :eID AND nSwedishID != 0 AND bIsText = 1",
					[
						"eID" => intval($english[0]['nID'])
					]);

					if(count($translation) > 0)
					{
						if($translation[0]['bUpdated'] == 0)
						{
							$connect->exec("UPDATE Translations SET bUpdated = 1, sFoundAt = :foundAt, nSwedishID = 0 WHERE bIsText = 1 AND nEnglishID = :eID",
								[
									"eID" => intval($english[0]['nID']),
									"foundAt" => $english[0]['sFoundAt']
								]
							);
						}
					}
					else if(count($translation) == 0)
					{
						$translation = $connect->query("SELECT nID, bUpdated FROM Translations WHERE nEnglishID = :eID AND nSwedishID = 0 AND bIsText = 1",
						[
							"eID" => intval($english[0]['nID'])
						]);

						if(count($translation) > 0)
						{
							if($translation[0]['bUpdated'] == 0)
							{
								$connect->exec("UPDATE Translations SET bUpdated = 1, sFoundAt = :foundAt, nSwedishID = 0 WHERE bIsText = 1 AND nEnglishID = :eID",
									[
										"eID" => intval($english[0]['nID']),
										"foundAt" => $english[0]['sFoundAt']
									]
								);
							}
						}
						if(count($translation) == 0)
						{
							$connect->exec("INSERT INTO Translations 
								(nSwedishID, nEnglishID, bIsText, bUpdated, sFoundAt) 
									VALUES 
								(0, :eID, 1, 1, :foundAt)",
								[
									"eID" => intval($english[0]['nID']),
									"foundAt" => $english[0]['sFoundAt']
								]
							);
						}
					}
				}
			}
		}

		// SWEDISH
		for ($q = 0; $q < count($ts); $q++)
		{
			$ts[$q] = str_replace('?', '', $ts[$q]);
			$ts[$q] = str_replace('.', '', $ts[$q]);
			$ts[$q] = str_replace(',', '', $ts[$q]);

			if(!empty($ts[$q]))
			{
				$swedish = $connect->query("SELECT w.nID, w.sFoundAt FROM Texts AS w WHERE w.sText = :text AND w.nLanguageID = 1",
				[
					"text" => strtolower($ts[$q])
				]);

				if(!empty($swedish))
				{
					$translation = $connect->query("SELECT nID, bUpdated FROM Translations WHERE nSwedishID = :sID AND nEnglishID != 0 AND bIsText = 1",
					[
						"sID" => intval($swedish[0]['nID'])
					]);

					if(count($translation) > 0)
					{
						if($translation[0]['bUpdated'] == 0)
						{
							$connect->exec("UPDATE Translations SET bUpdated = 1, sFoundAt = :foundAt, nEnglishID = 0 WHERE bIsText = 1 AND nSwedishID = :sID",
								[
									"sID" => intval($swedish[0]['nID']),
									"foundAt" => $swedish[0]['sFoundAt']
								]
							);
						}
					}
					else if(count($translation) == 0)
					{
						$translation = $connect->query("SELECT nID, bUpdated FROM Translations WHERE nSwedishID = :sID AND nEnglishID = 0 AND bIsText = 1",
						[
							"sID" => intval($swedish[0]['nID'])
						]);

						if(count($translation) > 0)
						{
							if($translation[0]['bUpdated'] == 0)
							{
								$connect->exec("UPDATE Translations SET bUpdated = 1, sFoundAt = :foundAt, nEnglishID = 0 WHERE bIsText = 1 AND nSwedishID = :sID",
									[
										"sID" => intval($swedish[0]['nID']),
										"foundAt" => $swedish[0]['sFoundAt']
									]
								);
							}
						}
						else if(count($translation) == 0)
						{
							$connect->exec("INSERT INTO Translations 
								(nSwedishID, nEnglishID, bIsText, bUpdated, sFoundAt) 
									VALUES 
								(:sID, 0, 0, 1, :foundAt)",
								[
									"sID" => intval($swedish[0]['nID']),
									"foundAt" => $swedish[0]['sFoundAt']
								]
							);
						}
					}
				}
			}
		}
	}
	// === END LINK ===

	// === REMOVE DUPLICATE LINK ===



	// === ENDREMOVE DUPLICATE LINK ===

	$connect->exec("DELETE FROM Translations WHERE bUpdated = 0");
} catch (Exception $e) {
	error_log(var_dump($e->message));
}

$connect->close();
