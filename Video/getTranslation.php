<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
if(!is_numeric($_POST['wordID']) || !is_numeric($_POST['alt']) || !is_numeric($_POST['signLanguage']))
	exit();

$translation = $connect->query("SELECT sTranslation FROM WordVideos WHERE nWordID = :wordID AND nAltID = :alt AND nSignLanguage = :signLanguage",
[
	'wordID' => $_POST['wordID'],
	'alt' => $_POST['alt'],
	'signLanguage' => $_POST['signLanguage']
]);

echo $translation[0]['sTranslation'];
exit();