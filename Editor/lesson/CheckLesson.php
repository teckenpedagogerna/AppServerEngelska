<?php
function getRealIpAddr()
{
	$ip = "-1";

	if ( !empty($_SERVER['HTTP_CLIENT_IP']) )
	{
		// Check IP from internet.
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) )
	{
		// Check IP is passed from proxy.
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else
	{
		// Get IP address from remote address.
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

//echo getRealIpAddr();
if(getRealIpAddr() != '13.48.127.171')
	exit();
//echo getRealIpAddr();
//include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$data = $connect->query("SELECT sData FROM Data WHERE nLessonID = :lessonID AND nPackageID = :packageID",
	[
		"lessonID" => $_POST["lessonID"],
		"packageID" => $_POST["packageID"]
	]
);

function Autocorrect($string)
{
	$string = ltrim($string, " ");
	$string = rtrim($string, " ");

	while(strpos($string, "  "))
	{
		$string = str_replace("  ", " ", $string);
	}

	if($string == "undefined")
		return '';

	return $string;
}

function CheckEmpty($string)
{
	if((string)$string == "0")
		$string = "1";
	
	if(empty(Autocorrect($string)))
	{
		echo "0";
		exit();
	}
}

for ($i = 0; $i < count($data); $i++)
{
	$d = json_decode($data[$i]['sData']);

	switch ($d->quizType)
	{
		case 'stepLock':
			break;
		case 'messageAnswer':
			// message answer
			{
				$content = explode('_', $d->content);
				CheckEmpty($d->answer);
				CheckEmpty($d->question);
				for ($c = 0; $c < count($content); $c++)
				{ 
					CheckEmpty($content[$c]);
				}
			}
			break;
		case 'messageQuestion':
			// message question
			{
				$content = explode('_', $d->content);
				CheckEmpty($d->answer);
				CheckEmpty($d->question);
				for ($c = 0; $c < count($content); $c++)
				{ 
					CheckEmpty($content[$c]);
				}
			}
			break;
		case 'buildText':
			{
				CheckEmpty($d->question);
				CheckEmpty($d->content);
			}
			break;
		case 'dragDropIntoText':
			{
				CheckEmpty($d->answer);
				CheckEmpty($d->content);
				CheckEmpty($d->question);
			}
			break;
		case 'selectMatchingWord':
			{
				CheckEmpty(str_replace('{0}', explode('_', $d->content)[intval($d->answer)], $d->question));
				CheckEmpty($d->answer);

				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
				{ 
					CheckEmpty($content[$c]);
				}
			}
			break;
		case 'wordNotBelong':
			{
				CheckEmpty($d->answer);
				$content = explode(' ', $d->question);
				$t = "";
				for ($c = 0; $c < count($content); $c++)
					if($d->answer != $c) 
						$t = $t . ' ' . $content[$c];

				if(count($t) == 0)
				{
					echo "0";
					exit();
				}

				CheckEmpty(substr($t, 1));
				//$w = substr($t, 1);
			}
			break;
		case 'selectPicture':
			// select picture
			{
				CheckEmpty($d->answer);
				$content = explode('_', $d->content);
				CheckEmpty($d->question);
				for ($c = 0; $c < count($content); $c++)
				{ 
					CheckEmpty($content[$c]);
				}

				$pictures = explode(',', $d->pictures);
				for ($p = 0; $p < count($pictures); $p++)
				{ 
					CheckEmpty($pictures[$p]);
				}
			}
			break;
		case 'isTrueImage':
			// is image true
			{
				CheckEmpty($d->answer);
				CheckEmpty($d->question);
				CheckEmpty($d->content);
				CheckEmpty(explode('_', $d->answer)[1]);
				CheckEmpty($d->pictures);
			}
			break;
		case 'matchWords':
			{
				$a = count(explode('_', $d->question));
				$b = count(explode('_', $d->answer));
				if($a > 5 || $b > 5 || $a != $b || $a == 0 || $b == 0)
				{
					echo "0";
					exit();
				}
			}
			break;
		case 'buildWord':
			{
				CheckEmpty($d->question);
				CheckEmpty($d->content);
			}
			break;
		case 'selectWord':
			{
				CheckEmpty($d->answer);
				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					CheckEmpty($content[$c]);

				CheckEmpty($d->question);
			}
			break;
		case 'selectWordReverse':
			{
				CheckEmpty($d->answer);
				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					CheckEmpty($content[$c]);

				CheckEmpty($d->question);
			}
			break;
		case 'selectMatchingWordToImage':
			{
				CheckEmpty($d->answer);
				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					CheckEmpty($content[$c]);

				CheckEmpty($d->question);
				CheckEmpty($d->pictures);
			}
			break;
		case 'selectMatchingWordToImageReverse':
			{
				CheckEmpty($d->answer);
				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					CheckEmpty($content[$c]);

				CheckEmpty($d->question);
				CheckEmpty($d->pictures);
			}
			break;
		case 'selectPictureAndWord':
			{
				CheckEmpty($d->answer);
				CheckEmpty($d->question);

				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					CheckEmpty($content[$c]);

				$pictures = explode(',', $d->pictures);
				for ($p = 0; $p < count($pictures); $p++)
				{ 
					CheckEmpty($pictures[$p]);
				}
			}
			break;
		case 'pairedText':
			CheckEmpty($d->content);
			break;
		case 'textNotBelong':
				CheckEmpty($d->answer);

				$content = explode('_', $d->content);
				for ($c = 0; $c < count($content); $c++)
					CheckEmpty($content[$c]);
			break;
		case 'selectAnswerToImage':
		{
			CheckEmpty($d->answer);
			$content = explode('_', $d->content);
			for ($c = 0; $c < count($content); $c++)
				CheckEmpty($content[$c]);

			CheckEmpty($d->question);
			CheckEmpty($d->pictures);
		}
		break;
		default:
			echo $d->quizType . " error";
			exit();
			break;
	} 
}

echo "1";
exit();