<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

if(!is_numeric($_POST["packageID"]) || !is_numeric($_POST["lessonID"]) || !is_numeric($_POST["stepIndex"]) || !is_numeric($_POST["index"]) || !isset($_POST["data"]) || !isset($_POST["id"]))
	exit();


$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$videoData = $connect->query("SELECT sData FROM WordLinkedVideos WHERE sIndex = :index AND nTranslationID = :id",
[
	"index" => $_POST["packageID"] . '-' . $_POST["lessonID"] . '-' . $_POST["stepIndex"] . '-' . $_POST["index"],
	"id" => $_POST['id']
]);

if(count($videoData) == 0)
{
	$connect->exec("DELETE FROM WordLinkedVideos WHERE sIndex =:index",
	[
		"index" => $_POST["packageID"] . '-' . $_POST["lessonID"] . '-' . $_POST["stepIndex"] . '-' . $_POST["index"]
	]);

	$connect->exec("INSERT INTO WordLinkedVideos (sIndex, sData, nTranslationID) VALUES (:index, :data, :id)",
	[
		"index" => $_POST["packageID"] . '-' . $_POST["lessonID"] . '-' . $_POST["stepIndex"] . '-' . $_POST["index"],
		"data" => $_POST["data"],
		"id" => $_POST['id']
	]);
}
else
{
	$connect->exec("UPDATE WordLinkedVideos SET sData = :data WHERE sIndex = :index AND nTranslationID = :id",
	[
		"index" => $_POST["packageID"] . '-' . $_POST["lessonID"] . '-' . $_POST["stepIndex"] . '-' . $_POST["index"],
		"data" => $_POST["data"],
		"id" => $_POST['id']
	]);
}

exit();