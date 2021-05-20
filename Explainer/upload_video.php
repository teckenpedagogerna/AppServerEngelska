<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';

if(!isset($_FILES['video']) || !is_numeric($_POST['stepIndex']) || !is_numeric($_POST['packageID']) || !isset($_POST['data']))
	exit();

// === GET IP ===
$dataserver_ip = file_get_contents($_SERVER['DATA'] . '/Text/DataServerIP.txt');

// === GET ALT NUMBER ===
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$connect->exec("UPDATE Explainer SET sData = :data, bUploaded = 2 WHERE nPackageID = :packageID AND nStepIndex = :stepIndex",
[
	'data' => $_POST['data'],
	'packageID' => $_POST['packageID'],
	'stepIndex' => $_POST['stepIndex']
]);

$id = $connect->query("SELECT nID FROM Explainer WHERE nPackageID = :packageID AND nStepIndex = :stepIndex",
[
	'packageID' => $_POST['packageID'],
	'stepIndex' => $_POST['stepIndex']
]);

$depthblend = $connect->query("SELECT sDB FROM Admin WHERE username = :user",
[
	'user' => $_SESSION["USER"]
]);

$connect->exec('INSERT INTO VideoQueue (sName, sDB) VALUES (:name, :depthblend)',
[
	'name' => 'explain_' . $id[0]['nID'] . '.mp4',
	'depthblend' => $depthblend[0]['sDB']
]);

// === UPLOAD VIDEO TO CONVERTER ===
$filename  = $_FILES['video']['tmp_name'];
$handle    = fopen($filename, "r");
$data      = fread($handle, filesize($filename));
fclose($handle);
$POST_DATA =
[
	'video' => base64_encode($data),
	'queue' => 'explain_' . $id[0]['nID'] . '.mp4'
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $dataserver_ip . '/english_ffmpeg/uploadVideo.php');
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
$response = curl_exec($curl);
curl_close ($curl);

echo $id[0]['nID'];

exit();