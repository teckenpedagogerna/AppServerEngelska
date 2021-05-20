<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';

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

$new_nanme = $_SERVER["DATA"] . "/Images/Cover/" . str_replace("/", "", $_POST["id"]) . ".jpg";

$im = new Imagick($_FILES["image"]["tmp_name"]);
$im->setImageFormat('jpg');
$im->scaleImage(1040, 980);
$im->writeImage($new_nanme);

// === MOVE FILE TO S3 ===
$source = $_SERVER['DATA'] . "/Images/Cover/";
$dest = 's3://media-teckenpedagogerna/English/Images/Cover/';

$manager = new Aws\S3\Transfer($s3, $source, $dest);
$manager->transfer();
unlink($new_nanme);

exit();