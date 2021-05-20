<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';

if(!isset($_POST['id']))
	exit();

include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$lessons = $connect->exec("INSERT INTO Report (sCode, sText, nDataID) VALUES (:code, :text, :id)",
[
	'code' => hash("sha512", 'tpepf_00localhost and pass code.'),
	'text' => 'LOCAL REPORT: ' . date("Y-m-d H:i"),
	'id' => $_POST['id']
]);

echo 'Rapporterat!';
exit();