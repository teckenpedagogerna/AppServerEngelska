<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

if(!isset($_POST['video']))
	exit();

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec('UPDATE VideoQueue SET nStage = 1 WHERE sName = :name',
[
	'name' => $_POST['video'] . '.mp4'
]);

$connect->exec('UPDATE Explainer SET bUploaded = 3 WHERE nID = :id',
[
	'id' => intval(explode('_', $_POST['video'])[1])
]);

echo intval(explode('_', $_POST['video'])[1]);

$temp_dir = $_SERVER['DATA']  . '/Videos';
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