<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';

$dataserver_ip = file_get_contents($_SERVER['DATA'] . '/Text/DataServerIP.txt');

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://' . $dataserver_ip . '/size.php');
curl_setopt($curl, CURLOPT_TIMEOUT, 60);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$disk = curl_exec($curl);
curl_close ($curl);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'http://' . $dataserver_ip . '/ram.php');
curl_setopt($curl, CURLOPT_TIMEOUT, 60);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$ram = curl_exec($curl);
curl_close ($curl);

echo $disk . '_' . $ram;
exit();