<?php

if(!isset($_POST['id']) || !isset($_POST['code']) || !isset($_POST['reportText']) || !isset($_POST['location']))
	exit();

$code = hash("sha512", 'tpepf_00' . $_POST['id'] . ' and pass code.');
$code2 = hash("sha512", 'tpepf_00' . $_POST['id'] . ' and pass code. ' . (string)gmdate('z'));

if($code2 != strtolower($_POST['code']))
{
	echo "Invaild code";
	exit();
}

include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$lessons = $connect->exec("INSERT INTO Report (sCode, sText, nDataID) VALUES (:code, :text, :id)",
[
	'code' => $code,
	'text' => $_POST['reportText'],
	'id' => $_POST['location']
]);

echo 'OK';
exit();