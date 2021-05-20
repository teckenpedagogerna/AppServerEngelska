<?php

include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$lessons = $connect->query("SELECT * FROM Lesson WHERE nPackageID = :id ORDER BY nStepIndex",
[
	"id" => $_GET["id"]
]);

$zoneID = $connect->query("SELECT nZoneID FROM Package WHERE nID = :id",
[
	"id" => $_GET["id"]
]);

$explainerLink = $connect->query("SELECT * FROM ExplainerLink WHERE nPackageID = :packageID",
[
	"packageID" => $_GET["id"]
]);

$date = strtotime(date('Y-m-d H:i:s'));

function CheckLesson($lessonID)
{
	$POST_DATA =
	[
		'lessonID' => $lessonID,
		'packageID' => $_GET["id"]
	];

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Editor/lesson/CheckLesson.php');
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
	$response = curl_exec($curl);
	curl_close ($curl);

	return $response;
	//var_dump($response);
}

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
	<script type="text/javascript" src="lesson/functions.js?<?php echo $date; ?>"></script>
	<script type="text/javascript">
		var packageID = <?php echo $_GET["id"]; ?>;
		var zoneID = <?php echo (string)$zoneID[0]['nZoneID']; ?>;

		//var thisID = '<?php echo $zoneID[0]['nZoneID'] . '.' . $_GET["id"]; ?>';

		var lessons = [<?php

			for($i = 0; $i < count($lessons) - 1; $i++)
			{
				echo $lessons[$i]["nID"] . ', ';
			}

			if(count($lessons) - 1 >= 0)
				echo $lessons[count($lessons) - 1]["nID"];
			
			?>];
	</script>
</head>

<body>
	<div class="card">
		<div class="card-body">
			<h4 class="card-title">Redigera grupper</h4>
			<h6 class="text-muted card-subtitle mb-2">Titel och undertitel</h6>
			<p class="card-text">ID: <?php echo $_GET["id"]; ?></p>
		</div>
	</div>

	<?php if(count($explainerLink) > 0) { ?>

	<div name="tips" id="lesson_tips" class="row" style="display: flex; border-bottom: solid 1px #eee;">
		<div class="col">
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 25px; color:#04f;"><small>Förklaringstitel</small></div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 50px;">
					<input id="explainTitle" name="title" onchange="UpdateTitleExplain(this);" type="text" style="width: 300px;" value="<?php echo htmlspecialchars($explainerLink[0]["sTitle"]); ?>">
					<button onclick="UploadTitleExplain(this);" type="button" class="btn" style="width: 115px; margin-left: 25px; background-color: #aaf; color: #000;">Uppdatera</button>
				</div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 25px; color:#04f;"><small>Ord som tas upp</small></div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 50px;">
					<input id="explainRecognition" onchange="UpdateRecognitionExplain(this);" type="text" style="width: 300px;" value="<?php echo htmlspecialchars($explainerLink[0]["sRecognition"]); ?>">
					<button onclick="UploadRecognitionExplain(this);" type="button" class="btn" style="width: 115px; margin-left: 25px; background-color: #aaf; color: #000;">Uppdatera</button>
				</div>
			</div>
		</div>
		<div class="col" style="border-left: solid #eee 1px; padding: 25px;margin: auto;max-width: 175px;">
			<button onclick="EditExplain(this);" type="button" class="btn btn-primary" style="width: 115px;">Redigera</button><br/><br/>
			<button onclick="RemoveExplain(this);" type="button" class="btn btn-danger" style="width: 115px;">Ta bort</button>
		</div>
	</div>

	<div name="tips" id="lesson_tips_add" class="row" style="display: none; border-bottom: solid 1px #eee;">
		<div class="col" style="border-left: solid #eee 1px; padding: 25px;margin: auto;">
			<button onclick="AddExplain(this);" type="button" class="btn btn-primary" style="width: 200px;">Lägga in förklaringssida</button>
		</div>
	</div>

	<?php } else { ?>

	<div name="tips" id="lesson_tips" class="row" style="display: none; border-bottom: solid 1px #eee;">
		<div class="col">
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 25px; color:#04f;"><small>Förklaringstitel</small></div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 50px;">
					<input id="explainTitle" name="title" onchange="UpdateTitleExplain(this);" type="text" style="width: 300px;" value="">
					<button onclick="UploadTitleExplain(this);" type="button" class="btn" style="width: 115px; margin-left: 25px; background-color: #aaf; color: #000;">Uppdatera</button>
				</div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 25px; color:#04f;"><small>Ord som tas upp</small></div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 50px;">
					<input id="explainRecognition" onchange="UpdateRecognitionExplain(this);" type="text" style="width: 300px;" value="">
					<button onclick="UploadRecognitionExplain(this);" type="button" class="btn" style="width: 115px; margin-left: 25px; background-color: #aaf; color: #000;">Uppdatera</button>
				</div>
			</div>
		</div>
		<div class="col" style="border-left: solid #eee 1px; padding: 25px;margin: auto;max-width: 175px;">
			<button onclick="EditExplain(this);" type="button" class="btn btn-primary" style="width: 115px;">Redigera</button><br/><br/>
			<button onclick="RemoveExplain(this);" type="button" class="btn btn-danger" style="width: 115px;">Ta bort</button>
		</div>
	</div>

	<div name="tips" id="lesson_tips_add" class="row" style="display: flex; border-bottom: solid 1px #eee;">
		<div class="col" style="border-left: solid #eee 1px; padding: 25px;margin: auto;">
			<button onclick="AddExplain(this);" type="button" class="btn btn-primary" style="width: 200px;">Lägga in förklaringssida</button>
		</div>
	</div>

	<?php
	}

	for($i = 0; $i < count($lessons); $i++)
	{
		$backgroundColor = 'background-color:#' . (CheckLesson($lessons[$i]["nID"]) == 1 ? '' : 'fdd') . ';';
	?>
	<div name="lesson" id="lesson_<?php echo $lessons[$i]["nID"]; ?>" class="row" style="display: flex; border-bottom: solid 1px #eee;">
		<div class="col" style="<?php echo $backgroundColor; ?>">
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 25px;"><small>Lektionstitel</small></div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 50px;">
					<input name="title" onchange="UpdateTitle(this);" type="text" style="width: 300px;" value="<?php echo htmlspecialchars($lessons[$i]["sTitle"]); ?>">
					<button onclick="UploadTitle(this);" type="button" class="btn" style="width: 115px; margin-left: 25px; background-color: #afa; color: #000;">Uppdatera</button>
				</div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 25px;"><small>Nya ord</small></div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 50px;">
					<input onchange="UpdateRecognition(this);" type="text" style="width: 300px;" value="<?php echo htmlspecialchars($lessons[$i]["sRecognition"]); ?>">
					<button onclick="UploadRecognition(this);" type="button" class="btn" style="width: 115px; margin-left: 25px; background-color: #afa; color: #000;">Uppdatera</button>
				</div>
			</div>
		</div>
		<div class="col" style="border-left: solid #eee 1px; padding: 25px;margin: auto;max-width: 175px;">
			<button onclick="MoveLessonOther(this, <?php echo $lessons[$i]["nID"]; ?>);" type="button" class="btn btn-primary" style="width: 115px;">Flytta till en kategori</button><br/><br/>
			<button onclick="MoveLesson(this, <?php echo $lessons[$i]["nID"]; ?>, 'UP');" type="button" class="btn btn-secondary" style="width: 115px; display: <?php echo $i > 0 ? 'block' : 'none';?>;">Flytta upp</button><br/>
			<button onclick="MoveLesson(this, <?php echo $lessons[$i]["nID"]; ?>, 'DOWN');" type="button" class="btn btn-secondary" style="width: 115px; display: <?php echo $i < count($lessons) - 1 ? 'block' : 'none'?>;">Flytta ner</button>
		</div>
		<div class="col" style="border-left: solid #eee 1px; padding: 25px;margin: auto;max-width: 175px; <?php echo $backgroundColor; ?>">
			<button onclick="EditLesson(this);" type="button" class="btn btn-primary" style="width: 115px;">Redigera</button><br/><br/>
			<button onclick="RemoveLesson(this);" type="button" class="btn btn-danger" style="width: 115px;">Ta bort</button>
		</div>
	</div>

	<?php
	
	}
	
	?>

	<div name="lesson" id="lesson_clone" class="row" style="display: none; border-bottom: solid 1px #eee;">
		<div class="col">
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 25px;"><small>Lektionstitel</small></div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 50px;">
					<input onclick="EditLesson(this);" name="title" onchange="UpdateTitle(this);" type="text" style="width: 300px;" value="">
					<button onclick="UploadTitle(this);" type="button" class="btn" style="width: 115px; margin-left: 25px; background-color: #afa; color: #000;">Uppdatera</button>
				</div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 25px;"><small>Nya ord</small></div>
			</div>
			<div class="row" style="margin-left: 25px;">
				<div class="col" style="height: 50px;">
					<input onchange="UpdateRecognition(this);" type="text" style="width: 300px;" value="">
					<button onclick="UploadRecognition(this);" type="button" class="btn" style="width: 115px; margin-left: 25px; background-color: #afa; color: #000;">Uppdatera</button>
				</div>
			</div>
		</div>
		<div class="col" style="border-left: solid #eee 1px; padding: 25px;margin: auto;max-width: 175px;">
			<button onclick="EditLesson(this);" type="button" class="btn btn-primary" style="width: 115px;">Redigera</button><br/><br/>
			<button onclick="RemoveLesson(this);" type="button" class="btn btn-danger" style="width: 115px;">Ta bort</button>
		</div>
	</div>


	<div id="AddLesson" class="row">
		<div class="col" style="padding: 25px;border: solid #eee 1px;"><img onclick="AddLesson();" style="margin: auto;display: block; border-radius: 50px; cursor: pointer; height: 100px;" src="../assets/img/add_2.png"></div>
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

	<script src="../assets/js/jquery.min.js"></script>
	<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
	<script src="../assets/js/jquery-ui.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
	<script src="../assets/js/theme.js"></script>
</body>

</html>