<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';

if(!isset($_FILES['video']) || !isset($_POST['wordID']) || !isset($_POST['signLanguage']))
	exit();
ignore_user_abort(true);

// === GET IP ===
$dataserver_ip = file_get_contents($_SERVER['DATA'] . '/Text/DataServerIP.txt');

// === GET ALT NUMBER ===
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$wordID = intval($_POST['wordID']);
$signLanguage = intval($_POST['signLanguage']);

$alt = -1;

if(isset($_POST['alt']))
{
	$alt = intval($_POST['alt']);
}

if($alt == -1)
{
	$alts = $connect->query("SELECT MAX(nAltID) AS nAltID FROM WordVideos WHERE nWordID = :id AND nSignLanguage = :signLanguage",
	[
		'id' => $wordID,
		'signLanguage' => $signLanguage
	]);

	if(count($alts) > 0)
		$alt = intval($alts[0]['nAltID']) + 1;
	else
		$alt = '1';
}

exec("ffmpeg -y -ss 00:00:00 -i \"" . $_FILES['video']['tmp_name'] . "\" -vframes 1 -q:v 2 \"/var/www/local/data/Videos/template.png\"");
$ckey = (string)str_pad(dechex(imagecolorat(imagecreatefrompng('/var/www/local/data/Videos/template.png'), 1, 1)), 6, "0", STR_PAD_LEFT);
unlink('/var/www/local/data/Videos/template.png');

$depthblend = $connect->query("SELECT sDB FROM Admin WHERE username = :user",
[
	'user' => $_SESSION["USER"]
])[0]['sDB'];

$db = $connect->query("SELECT nDepth, nBlend FROM VideoColor WHERE sColor = :color",
[
	'color' => $ckey
]);

if(!empty($db))
{
	$depthblend = (string)$db[0]['nDepth'] . ':' . (string)$db[0]['nBlend'];
}

$connect->exec('INSERT INTO VideoQueue (sName, sDB) VALUES (:name, :depthblend)',
[
	'name' => $wordID . '-' . $alt . '-' . $signLanguage . '.mp4',
	'depthblend' => $depthblend
]);

$color = $connect->query("SELECT sColor FROM VideoColor WHERE sColor = :color",
[
	'color' => $ckey
]);

if(empty($color))
{
	$connect->exec('INSERT INTO VideoColor (sColor, nDepth, nBlend) VALUES (:color, :depth, :blend)',
	[
		'color' => $ckey,
		'depth' => explode(':', $depthblend)[0],
		'blend' => explode(':', $depthblend)[1]
	]);
}
else
{
	$connect->exec('UPDATE VideoColor SET nDepth = :depth, nBlend = :blend WHERE sColor = :color',
	[
		'color' => $ckey,
		'depth' => explode(':', $depthblend)[0],
		'blend' => explode(':', $depthblend)[1]
	]);
}

$connect->exec("INSERT INTO WordVideos (nWordID, nSignLanguage, nAltID) VALUES (:id, :signLanguage, :alt)",
[
	'id' => $wordID,
	'signLanguage' => $signLanguage,
	'alt' => $alt
]);

// === UPLOAD VIDEO TO CONVERTER ===
$filename  = $_FILES['video']['tmp_name'];
$handle    = fopen($filename, "r");
$data      = fread($handle, filesize($filename));
fclose($handle);
$POST_DATA =
[
	'video' => base64_encode($data),
	'queue' => $wordID . '-' . $alt . '-' . $signLanguage . '.mp4'
];

echo $ckey . '-' . $alt;

// === CLOSE CONNECTION ===

session_write_close();
fastcgi_finish_request();

// === START UPLOAD VIDEO TO CONVERTER ===
sleep(1);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $dataserver_ip . '/english_ffmpeg/uploadVideo.php');
curl_setopt($curl, CURLOPT_TIMEOUT, 30);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
$response = curl_exec($curl);
curl_close ($curl);

include_once __DIR__ . '/UpdateWords.php';
exit();