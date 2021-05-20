<?php
require_once $_SERVER['LIBRARY'] . '/AWS/vendor/autoload.php';
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$id = $connect->query("SELECT nID FROM Explainer WHERE nStepIndex = :stepIndex AND nPackageID = :packageID",
	[
		"stepIndex" => $_POST["stepIndex"],
		"packageID" => $_POST["packageID"]
	]
);

$connect->exec("DELETE FROM Explainer WHERE nStepIndex = :stepIndex AND nPackageID = :packageID",
	[
		"stepIndex" => $_POST["stepIndex"],
		"packageID" => $_POST["packageID"]
	]
);

$connect->exec("DELETE FROM VideoQueue WHERE sName = :name",
	[
		"name" => 'explain_' . $id[0]['nID'] . '.mp4'
	]
);

// === SETUP S3 CLIENT ===
use Aws\S3\S3Client;  
use Aws\Exception\AwsException;
use Aws\Credentials\Credentials;
use Aws\S3\Transfer;

$s3 = new S3Client([
    'region' => 'eu-north-1',
    'version' => 'latest',
    'credentials' => new Credentials('AKIAWDRY6ALUWS3JXDWK', 'ABRveLjxOivdCCzQNDZ6AsqYmfuSIVqgLN3eVvw+')
]);

$result = $s3->deleteObject(array(
    'Bucket' => 'media-teckenpedagogerna',
    'Key'    => 'English/Videos/explain_' . $_POST['packageID'] . '-' . $_POST['stepIndex'] . '.mp4'
));

echo "DELETED";

// === GET IP ===
$dataserver_ip = file_get_contents($_SERVER['DATA'] . '/Text/DataServerIP.txt');

// === CLOSE CONNECTION ===
ignore_user_abort(true);
session_write_close();
fastcgi_finish_request();

// === START UPLOAD VIDEO TO CONVERTER ===
sleep(1);

$POST_DATA =
[
	'video' => 'explain_' . $_POST['id']
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $dataserver_ip . '/english_ffmpeg/deleteVideo.php');
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
$response = curl_exec($curl);
curl_close ($curl);
exit();