<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$data = explode(';', $_POST['data']);

for ($i = 0; $i < count($data); $i++)
{
	$data[$i] = explode('.', $data[$i]);

	$title = $connect->query("SELECT Data.nStepIndex AS step, Zone.sTitle AS zone, Lesson.sTitle AS lesson, Package.sTitle AS package FROM Data LEFT JOIN Lesson ON Data.nLessonID = Lesson.nID LEFT JOIN Package ON Package.nID = Data.nPackageID AND Lesson.nPackageID = Package.nID LEFT JOIN Zone ON Package.nZoneID = Zone.nID WHERE Data.nStepIndex = :stepIndex AND Lesson.nID = :lesson AND Package.nID = :package AND Zone.nID = :zone",
	[
		'stepIndex' => $data[$i][3],
		'lesson' => $data[$i][2],
		'package' => $data[$i][1],
		'zone' => $data[$i][0]
	]);
?>

<a href="https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Editor/data.php?lessonID=<?php echo $data[$i][2]; ?>&packageID=<?php echo $data[$i][1]; ?>&stepIndex=<?php echo $data[$i][3]; ?>" target="_blank">
	<?php 
	echo $title[0]['zone'] . ' / ' . $title[0]['package'] . ' / ' . $title[0]['lesson'] . ' / Steg ' . ($title[0]['step'] + 1);
	?>
</a>
<br>

<?php
}
?>