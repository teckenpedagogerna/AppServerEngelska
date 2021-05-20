<?php

include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

if(!is_numeric($_POST["packageID"]) || !is_numeric($_POST["lessonID"]) || !is_numeric($_POST["stepIndex"]))
	exit();

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$lesson = $connect->query("SELECT sData AS data, nID AS id FROM Data WHERE nPackageID = :packageID AND nLessonID = :lessonID AND nStepIndex = :stepIndex",
[
    "packageID" => $_POST["packageID"],
    "lessonID" => $_POST["lessonID"],
    "stepIndex" => $_POST["stepIndex"]
]);

echo json_encode($lesson[0]);