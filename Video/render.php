<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

if(!isset($_POST['video']))
	exit();

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec('UPDATE VideoQueue SET nStage = 1, sDB = :db WHERE sName = :name',
[
	'name' => $_POST['video'] . '.mp4',
	'db' => str_replace(',', '.', $_POST['depth']) . ':' . str_replace(',', '.', $_POST['blend'])
]);

if(strpos($_POST['video'], "_text"))
{
	$connect->exec("UPDATE TextVideos SET bUploaded = 0 WHERE nTextID = :id AND nSignLanguage = :signLanguage AND nAltID = :alt",
	[
		'id' => $_POST['id'],
		'signLanguage' => $_POST['signLanguage'],
		'alt' => $_POST['alt']
	]);
}
else
{
	$connect->exec("UPDATE WordVideos SET bUploaded = 0 WHERE nWordID = :id AND nSignLanguage = :signLanguage AND nAltID = :alt",
	[
		'id' => $_POST['id'],
		'signLanguage' => $_POST['signLanguage'],
		'alt' => $_POST['alt']
	]);
}

$connect->exec('UPDATE VideoColor SET nDepth = :depth, nBlend = :blend WHERE sColor = :color',
[
	'color' => $_POST['colorTarget'],
	'depth' => $_POST['depth'],
	'blend' => $_POST['blend']
]);

$temp_dir = $_SERVER['DATA']  . '/Videos';

if(file_exists($temp_dir . '/' . str_replace('/', '', str_replace('.', '', $_POST['video'])) . '.jpg'))
	unlink($temp_dir . '/' . str_replace('/', '', str_replace('.', '', $_POST['video'])) . '.jpg');
echo "OK";

// === GET IP ===
$dataserver_ip = file_get_contents($_SERVER['DATA'] . '/Text/DataServerIP.txt');

// === CLOSE CONNECTION ===
ignore_user_abort(true);
session_write_close();
fastcgi_finish_request();

// === START UPLOAD VIDEO TO CONVERTER ===
sleep(1);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $dataserver_ip . '/english_ffmpeg/convertVideo.php');
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, []);
$response = curl_exec($curl);
curl_close ($curl);

exit();