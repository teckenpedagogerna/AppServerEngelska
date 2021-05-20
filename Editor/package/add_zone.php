<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//include_once __DIR__ . '/update.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec('INSERT INTO Zone (sTitle, sDescription, bActivated) VALUES (:title, :description, :activated)',
	[
		'title' => '',
		'description' => '',
		'activated' => 0
	]
);

$id = $connect->query('SELECT nID FROM Zone ORDER BY nID DESC LIMIT 1')[0]['nID'];

for ($i = 0; $i < 6; $i++)
{ 
	$connect->exec('INSERT INTO Package (nZoneID) VALUES (:id)',
		[
			'id' => intval($id)
		]
	);
}

$pid = $connect->query('SELECT nID FROM Package WHERE nZoneID = :id ORDER BY nID',
	[
		'id' => intval($id)
	]
);

array_unshift($pid, ['zoneID' => $id]);

echo json_encode($pid);
exit();