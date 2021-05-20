<?php

include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
//exit();

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$texts_swe = $connect->query("
	SELECT 
		t.nID, ws.nID AS wID,
		ws.sText, ws.nUsage,
		t.sFoundAt, t.nEnglishID, t.nSwedishID
	FROM Translations AS t 
	LEFT JOIN Texts AS ws ON (t.nSwedishID = ws.nID AND ws.nLanguageID = 1) 
	WHERE t.sFoundAt IS NOT NULL AND t.bIsText = 1 AND t.nSwedishID != 0
	ORDER BY ws.sText",
	[

	]
);

$texts_eng = $connect->query("
	SELECT 
		t.nID, we.nID AS wID,
		we.sText, we.nUsage,
		t.sFoundAt, t.nEnglishID, t.nSwedishID
	FROM Translations AS t 
	LEFT JOIN Texts AS we ON (t.nEnglishID = we.nID AND we.nLanguageID = 2)
	WHERE t.sFoundAt IS NOT NULL AND t.bIsText = 1 AND t.nEnglishID != 0
	ORDER BY we.sText",
	[

	]
);

$texts = [];

for ($e = 0; $e < count($texts_eng); $e++)
{
	$texts[$texts_eng[$e]['nID']] = 
	[
		'nID' => $texts_eng[$e]['nID'],
		'english' => $texts_eng[$e]['sText'],
		'swedish' => '',
		'usage' => $texts_eng[$e]['nUsage'],
		'foundAt' => $texts_eng[$e]['sFoundAt'],
		'usage' => $texts_eng[$e]['nUsage']
	];
}

for ($s = 0; $s < count($texts_swe); $s++)
{
	$texts[$texts_swe[$s]['nID']] = 
	[
		'nID' => !empty($texts[$texts_swe[$s]['nID']]['nID']) ? $texts[$texts_swe[$s]['nID']]['nID'] : '',
		'english' => !empty($texts[$texts_swe[$s]['nID']]['english']) ? $texts[$texts_swe[$s]['nID']]['english'] : '',
		'swedish' => $texts_swe[$s]['sText'],
		'foundAt' => !empty($texts[$texts_swe[$s]['nID']]['foundAt']) ? $texts[$texts_swe[$s]['nID']]['foundAt'] : $texts_swe[$s]['sFoundAt'],
		'usage' => !empty($texts[$texts_swe[$s]['nID']]['usage']) ? $texts[$texts_swe[$s]['nID']]['usage'] : $texts_swe[$s]['nUsage']
	];
}

$date = strtotime(date('Y-m-d H:i:s'));

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
	<title>Teckenpedagogerna App Server</title>
	<link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
	<link rel="stylesheet" href="../assets/fonts/ionicons.min.css">
	<link rel="stylesheet" href="../assets/css/divider-text-middle.css">
	<link rel="stylesheet" href="../assets/css/Drag--Drop-Upload-Form.css">
	<link rel="stylesheet" href="../assets/css/Login-Form-Clean.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script type="text/javascript" src="../assets/js/jquery.min.js"></script>
	<script type="text/javascript" src="functions_text.js?<?php echo $date; ?>"></script>
	<style>
		.tablerow
		{
			transition: 0.1s background-color;
		}
		.tablerow:hover
		{
			background-color:#ddd;
		}
	</style>
</head>
<body>
	<div class="card">
		<div class="card-body">
			<button class="btn btn-primary" style="display: none;" onclick="ReUpdate();">Ladda om länkade data</button>
			<br>
			<br>
			<a href="https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/">Gå tillbaka</a>
			<br>
			<br>
			<h4 class="card-title">Textlista, videor och översättningar</h4>
			<h6 class="text-muted card-subtitle mb-2">Text och video</h6>
		</div>
	</div>
	<div class="card" style="margin: 24px;">
		<input onkeyup="Search(this);" type="text" class="form-control" placeholder="Sök" aria-describedby="basic-addon1">
		<div style="margin: 12px;">
			<input onclick="SearchRefresh();" type="checkbox" id="noVideo" name="noVideo"> <label for="noVideo"> No video</label><br>
			<?php echo 'Total meningar: ' . count($texts_eng) . ' st, översatta: ' . count($texts_swe) . ' st'; ?>
		</div>
	</div>
	<div class="card">
		<table id="table" class="table table-bordered">
			<!-- HEADER -->
			<tr style="font-size:24px;">
				<th>#</th>
				<th>Text</th>
				<th>Översätt</th>
				<th>Antal</th>
				<th>Finns på</th>
				<th>Videor (STS)</th>
				<th>Videor (ASL)</th>
			</tr>
			<!-- BODY -->
<?php 

foreach($texts as $text)
//for ($i = 0; $i < count($texts); $i++)
{
	if(empty($text['nID']))
		continue;
?>
			<tr class="tablerow">
				<th><?php echo $text['nID']; ?></th>
				<th style="width: 300px;">
					<img src="../assets/img/flags/4x3/us.svg" height="40" style="border-radius:8px; opacity:0.5;" />
					<?php echo ucfirst($text['english']); ?>
				</th>
				<th style="width: 300px;">
					<img src="../assets/img/flags/4x3/se.svg" height="40" style="border-radius:8px; opacity:0.5;" />
					<?php echo ucfirst($text['swedish']); ?>
				</th>
				<th><?php echo $text['usage']; ?></th>
				<th><button class="btn btn-primary" style="width: 100%;" onclick="Whereis('<?php echo $text['foundAt']; ?>');">Visa</button></th>
				<th>
					<img src="../assets/img/flags/4x3/se.svg" height="40" style="border-radius:8px; opacity:0.5;" />
					<select id="videos_1_<?php echo $text['nID']; ?>">
						<option value="none">None</option>
<?php

$swe_videos = $connect->query("SELECT * FROM TextVideos WHERE nTextID = :id AND nSignLanguage = 1 ORDER BY nAltID",
[
	'id' => $text['nID']
]);

for ($v = 0; $v < count($swe_videos); $v++)
{
?>
						<option value="<?php echo (string)$swe_videos[$v]['nAltID'] ?>" selected>Alternative <?php echo (string)$swe_videos[$v]['nAltID'] ?></option>
<?php
}
?>
					</select>
					<button class="btn btn-primary" onclick="UploadVideo(<?php echo "'" . htmlspecialchars(ucfirst($text['english'])) . '\', ' . $text['nID']; ?>, 1);">Ladda upp</button>
					<button class="btn btn-secondary" onclick="ShowVideo(<?php echo "'" . htmlspecialchars(ucfirst($text['english'])) . '\', ' . $text['nID']; ?>, 1)">Visa</button>
				</th>
				<th>
					<img src="../assets/img/flags/4x3/us.svg" height="40" style="border-radius:8px; opacity:0.5;" />
					<select id="videos_2_<?php echo $text['nID']; ?>">
						<option value="none">None</option>
<?php

$us_videos = $connect->query("SELECT * FROM TextVideos WHERE nTextID = :id AND nSignLanguage = 2 ORDER BY nAltID",
[
	'id' => $text['nID']
]);

for ($v = 0; $v < count($us_videos); $v++)
{
?>
						<option value="<?php echo (string)$us_videos[$v]['nAltID'] ?>" selected>Alternative <?php echo (string)$us_videos[$v]['nAltID'] ?></option>
<?php
}
?>
					</select>
					<button class="btn btn-primary" onclick="UploadVideo(<?php echo "'" . htmlspecialchars(ucfirst($text['english'])) . '\', ' . $text['nID']; ?>, 2);">Ladda upp</button>
					<button class="btn btn-secondary" onclick="ShowVideo(<?php echo "'" . htmlspecialchars(ucfirst($text['english'])) . '\', ' . $text['nID']; ?>, 2)">Visa</button>
				</th>
			</tr>
<?php
}
?>
			<!-- END OF BODY -->
		</table>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="modal">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal_title">Modal title</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="modal_content">
					...
				</div>
				<div class="modal-footer">
					<button id="modal_close" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button id="modal_ok" type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Upload Modal -->
	<div class="modal fade" id="modal_upload">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal_upload_title">Modal title</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="modal_upload_content">
					<video style="width: 100%;" controls loop autoplay>
						<source id="modal_upload_preview_video" src="">
					</video>
					<br>
					<div style="width: 100%; height: 20px; background-color: #ccc; padding: 5px; border-radius: 10px;">
						<div id="uploadProgress" style="width: 50%; height: 100%; background-color: #66f; border-radius: 5px;">
						</div>
					</div>
					<div style="text-align: center;" id="uploadProgressNumber">
						50%
					</div>
					<br>
					<input type="file" name="file" id="videofile" accept="video/mp4" onchange="VideoUploadPreview(this);" />
				</div>
				<div class="modal-footer">
					<button id="modal_upload_close" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button id="modal_upload_ok" type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Show Video Modal -->
	<div class="modal fade" id="modal_show_video">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal_show_video_title">Modal title</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="modal_show_video_content">

					<img src="" style="width: 100%; display: none;" id="modal_show_preview_img" />
					<div id="modal_show_load">
						Laddar...
					</div>
					<br>
					<div id="modal_show_input0" style="float: left; width: 48%;">
						Djup:
						<input type="text" placeholder="Depth" value="0.0" id="modal_show_depth" style="display: none; width: 100%;">
					</div>
					<div id="modal_show_input1" style="float: right; width: 48%;">
						Blandning:
						<input type="text" placeholder="Blend" value="0.0" id="modal_show_blend" style="display: none; width: 100%;">
					</div>
					<div id="modal_show_brs">
						<br>
						<br>
						<br>
					</div>

					<button id="modal_show_preview" class="btn btn-secondary" style="width: 100%;" onclick="UpdatePreview();">Uppdatera förhandsvisning</button>

					<video style="width: 100%;" controls loop autoplay>
						<source id="modal_show_video_preview_video" src="">
					</video>
					<br>
					<br>
					<input onchange="ChangeTranslate(this);" id="translation_video" type="text" class="form-control" style="width:70%; float: left;" placeholder="Alternativt namn" aria-describedby="basic-addon1" value="">
					<button class="btn btn-primary" name="transition" style="width: 28%; float: right;" onclick="UpdateTranslate();">Uppdatera</button>
				</div>
				<div class="modal-footer">
					<button id="modal_show_video_close" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button id="modal_show_video_replace" onclick="ReplaceVideo();" type="button" class="btn btn-primary" style="background-color:#333;">Byta ut</button>
					<button id="modal_show_video_ok" type="button" class="btn btn-danger">Delete</button>
					<button id="modal_show_video_render" onclick="RenderVideo();" type="button" class="btn btn-primary">Rendera Video</button>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
	
	</script>
	<script src="../assets/js/jquery.min.js"></script>
	<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
	<script src="../assets/js/jquery-ui.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
	<script src="../assets/js/theme.js"></script>

</body>
</html>