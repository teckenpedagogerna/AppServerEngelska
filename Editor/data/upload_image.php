<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

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

$connect = new SDB('tp', 'dVAh8sbBSoa7yNzhnPFT', 'app_english');

// === GET ID NUMBER ===
$new_name = $_SERVER["DATA"] . "/Images/Data/" . 
	str_replace("/", "", $_POST["packageID"]) . "-"
	. str_replace("/", "", $_POST["lessonID"]) . "-"
	. str_replace("/", "", $_POST["stepIndex"]) . "-"
	. str_replace("/", "", $_POST["index"])
	. ".jpg";

$im = new Imagick($_FILES["image"]["tmp_name"]);
$im->setImageFormat('jpg');
$im->scaleImage(512, 512);
$im->writeImage($new_name);

// === MOVE FILE TO S3 ===
$source = $_SERVER['DATA'] . "/Images/Data/";
$dest = 's3://media-teckenpedagogerna/English/Images/Data/';

$manager = new Aws\S3\Transfer($s3, $source, $dest);
$manager->transfer();
unlink($new_name);

exit();