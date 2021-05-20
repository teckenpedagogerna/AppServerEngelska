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

// === GET IP ===
$dataserver_ip = file_get_contents($_SERVER['DATA'] . '/Text/DataServerIP.txt');
if (!(isset($_POST['video']) && getRealIpAddr() == $dataserver_ip && isset($_POST['name'])))
	exit();
//ignore_user_abort(true);

$index = explode("-", $_POST['name']);
$index[2] = str_replace(".mp4", "", $index[2]);
$index[2] = str_replace("_text", "", $index[2]);

$id = intval($index[0]);
$alt = intval($index[1]);
$signLanguage = intval($index[2]);

$file_name = $_SERVER['DATA'] . '/Videos/' . str_replace("/", "", $_POST['name']);

// === DATABASE ===
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
if(strpos($_POST['name'], "xplain_"))
{
	if(empty($connect->query("SELECT * FROM Explainer WHERE nID = :id",
	[
		'id' => explode('.', explode('_', $_POST['name'])[1])[0]
	])))
	{
		exit();
	}
	else
	{
		$connect->exec("UPDATE Explainer SET bUploaded = 1 WHERE nID = :id",
		[
			'id' => explode('.', explode('_', $_POST['name'])[1])[0]
		]);
	}
}
else if(strpos($_POST['name'], "_text"))
{
	if(empty($connect->query("SELECT * FROM TextVideos WHERE nTextID = :id AND nSignLanguage = :signLanguage AND nAltID = :alt",
	[
		'id' => $id,
		'signLanguage' => $signLanguage,
		'alt' => $alt
	])))
	{
		exit();
	}
	else
	{
		$connect->exec("UPDATE TextVideos SET bUploaded = 1 WHERE nTextID = :id AND nSignLanguage = :signLanguage AND nAltID = :alt",
		[
			'id' => $id,
			'signLanguage' => $signLanguage,
			'alt' => $alt
		]);
	}
}
else
{
	if(empty($connect->query("SELECT * FROM WordVideos WHERE nWordID = :id AND nSignLanguage = :signLanguage AND nAltID = :alt",
	[
		'id' => $id,
		'signLanguage' => $signLanguage,
		'alt' => $alt
	])))
	{
		exit();
	}
	else
	{
		$connect->exec("UPDATE WordVideos SET bUploaded = 1 WHERE nWordID = :id AND nSignLanguage = :signLanguage AND nAltID = :alt",
		[
			'id' => $id,
			'signLanguage' => $signLanguage,
			'alt' => $alt
		]);
	}
}

// === FILE ===
$videofile = fopen($file_name, "w");
fwrite($videofile, base64_decode($_POST['video']));
fclose($videofile);

//session_write_close();
//fastcgi_finish_request();

// === SETUP S3 CLIENT ===
require_once $_SERVER['LIBRARY'] . '/AWS/vendor/autoload.php';
use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\Credentials\Credentials;
use Aws\S3\Transfer;

$s3 = new S3Client([
    'region' => 'eu-north-1',
    'version' => 'latest',
    'credentials' => new Credentials('AKIAWDRY6ALUWS3JXDWK', 'ABRveLjxOivdCCzQNDZ6AsqYmfuSIVqgLN3eVvw+')
]);

// === MOVE FILE TO S3 ===
$source = $_SERVER['DATA'] . "/Videos/";
$dest = 's3://media-teckenpedagogerna/English/Videos/';

$manager = new Aws\S3\Transfer($s3, $source, $dest);
$manager->transfer();
unlink($file_name);

exit();