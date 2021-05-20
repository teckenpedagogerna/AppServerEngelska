
<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
if(!is_numeric($_POST['textID']) || !is_numeric($_POST['alt']) || !is_numeric($_POST['signLanguage']) || !isset($_POST['translation']))
	exit();

$connect->exec("UPDATE TextVideos SET sTranslation = :translation WHERE nTextID = :textID AND nAltID = :alt AND nSignLanguage = :signLanguage",
[
	'textID' => $_POST['textID'],
	'alt' => $_POST['alt'],
	'signLanguage' => $_POST['signLanguage'],
	'translation' => $_POST['translation']
]);

//include_once __DIR__ . '/RetranslateVideosWordText.php';
