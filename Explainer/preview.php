<?php

include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';

if (!(isset($_POST['video']) && isset($_POST['blend']) && isset($_POST['depth'])))
{
	exit();
}

// === DATABASE ===
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$connect->exec("UPDATE VideoQueue SET sDB = :data WHERE sName = :name",
[
	'name' => $_POST['video'] . '.mp4',
	'data' => $_POST['depth'] . ':' . $_POST['blend']
]);

$connect->exec("UPDATE Admin SET sDB = :data WHERE username = :user",
[
	'user' => $_SESSION["USER"],
	'data' => $_POST['depth'] . ':' . $_POST['blend']
]);

$dataserver_ip = file_get_contents($_SERVER['DATA'] . '/Text/DataServerIP.txt');

$POST_DATA =
[
	'video' => $_POST['video'],
	'blend' => $_POST['blend'],
	'depth' => $_POST['depth']
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $dataserver_ip . '/english_ffmpeg/preview.php');
curl_setopt($curl, CURLOPT_TIMEOUT, 500);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
$response = curl_exec($curl);
curl_close($curl);

echo $response;
exit();