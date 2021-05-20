<?php

if(!is_numeric($_POST['id']))
{
	exit();
}

require_once $_SERVER['LIBRARY'] . '/AWS/vendor/autoload.php';
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
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
    'Key'    => 'English/Videos/explain_' . $_POST['id'] . '.mp4'
));

$connect->exec("UPDATE Explainer SET bUploaded = 0 WHERE nID = :id",
[
	'id' => $_POST['id']
]);

$connect->exec("DELETE FROM VideoQueue WHERE sName = :name",
[
	'name' => 'explain_' . $_POST['id'] . '.mp4'
]);

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

include_once __DIR__ . '/UpdateWords.php';
include_once __DIR__ . '/RetranslateVideosWord.php';
exit();