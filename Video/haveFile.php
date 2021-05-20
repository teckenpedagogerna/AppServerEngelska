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
if (!(getRealIpAddr() == $dataserver_ip && isset($_POST['name'])))
	exit();

$index = explode("-", $_POST['name']);
$index[2] = str_replace(".mp4", "", $index[2]);
$index[2] = str_replace("_text", "", $index[2]);

$id = intval($index[0]);
$alt = intval($index[1]);
$signLanguage = intval($index[2]);

// === DATABASE ===
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
if(strpos($_POST['name'], "xplain_"))
{

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
		echo "0";
	}
	else
	{
		echo "1";
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
		echo "0";
	}
	else
	{
		echo "1";
	}
}

exit();