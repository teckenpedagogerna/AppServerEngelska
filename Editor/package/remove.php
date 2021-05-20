<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//include_once __DIR__ . '/update.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$connect->exec("DELETE FROM Zone WHERE nID = :id",
	[
		"id" => $_POST["id"]
	]
);

$pid = $connect->query('SELECT nID FROM Package WHERE nZoneID = :id',
	[
		'id' => $_POST["id"]
	]
);

$connect->exec("DELETE FROM Package WHERE nZoneID = :id",
	[
		"id" => $_POST["id"]
	]
);

for ($i = 0; $i < count($pid); $i++)
{
	if(file_exists("/var/www/public/AppServer/Engelska/Download/Images/Package/" . $pid[$i]['nID'] . ".jpg"))
		unlink("/var/www/public/AppServer/Engelska/Download/Images/Package/" . $pid[$i]['nID'] . ".jpg");

	$lid = $connect->query('SELECT nID FROM Lesson WHERE nPackageID = :packageID',
		[
			'packageID' => intval($pid[$i]['nID'])
		]
	);

	$connect->exec("DELETE FROM Lesson WHERE nPackageID = :packageID",
		[
			"packageID" => intval($pid[$i]['nID'])
		]
	);

	for ($x = 0; $x < count($lid); $x++)
	{ 
		$connect->exec("DELETE FROM Data WHERE nPackageID = :packageID AND nLessonID = :lessonID",
			[
				"lessonID" => intval($lid[$x]['nID']),
				"packageID" => intval($pid[$i]['nID'])
			]
		);
	}
}

echo "DELETED";
exit();