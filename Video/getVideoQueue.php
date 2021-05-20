<?php
function getRealIpAddr()
{
	$ip = "-1";

	if ( !empty($_SERVER['HTTP_CLIENT_IP']) )
	{
		// Check IP from internet.
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) )
	{
		// Check IP is passed from proxy.
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else
	{
		// Get IP address from remote address.
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

// === GET IP ===
$dataserver_ip = file_get_contents($_SERVER['DATA'] . '/Text/DataServerIP.txt');

if(getRealIpAddr() == $dataserver_ip)
{
	include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
	$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
	echo json_encode($connect->query('SELECT nID AS id, sName AS name, sDB AS depthblend FROM VideoQueue WHERE nStage = 1'));
	$connect->exec('DELETE FROM VideoQueue WHERE nStage = 1');
}

exit();