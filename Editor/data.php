<?php

include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');

$lessons;

if(isset($_GET["dataID"]))
{
	$lessons = $connect->query("SELECT * FROM Data WHERE nID = :id ORDER BY nStepIndex",
	[
		"id" => $_GET["dataID"]
	]);

	$_GET['stepIndex'] = $lessons[0]['nStepIndex'];
	$_GET['packageID'] = $lessons[0]['nPackageID'];
	$_GET['lessonID'] = $lessons[0]['nLessonID'];
	
	$lessons = $connect->query("SELECT i.* FROM Data AS o INNER JOIN Data AS i ON o.nPackageID = i.nPackageID AND o.nLessonID = i.nLessonID WHERE o.nID = :id ORDER BY i.nStepIndex",
	[
		"id" => $_GET["dataID"]
	]);
}
else
{
	$lessons = $connect->query("SELECT * FROM Data WHERE nPackageID = :packageID AND nLessonID = :lessonID ORDER BY nStepIndex",
	[
		"packageID" => $_GET["packageID"],
		"lessonID" => $_GET["lessonID"]
	]);
}

$date = strtotime(date('Y-m-d H:i:s'));

$stepIndex = null;

if(isset($_GET['stepIndex']))
{
	$stepIndex = $_GET['stepIndex'];
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
	<style type="text/css">
		.list-data-item
		{
			cursor: pointer;
		}

		.list-data-item:hover
		{
			background-color: #aaf;
			color: #fff;
		}
	</style>
	<script type="text/javascript" src="data/functions.js?<?php echo $date; ?>"></script>

	<script type="text/javascript" src="data/MessageAnswer.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/MessageQuestion.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/SelectMatchingWord.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/IsTrueImage.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/MatchWords.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/BuildWord.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/BuildText.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/DragDropIntoText.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/SelectWord.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/SelectWordReverse.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/SelectPicture.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/SelectMatchingWordToImage.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/SelectMatchingWordToImageReverse.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/SelectPictureAndWord.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/WordNotBelong.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/PairedText.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/TextNotBelong.js?<?php echo $date; ?>"></script>
	<script type="text/javascript" src="data/SelectAnswerToImage.js?<?php echo $date; ?>"></script>

	<script type="text/javascript">
		var packageID = <?php echo $_GET["packageID"]; ?>;
		var lessonID = <?php echo $_GET["lessonID"]; ?>;

		var allLessons =
		[
			"stepLock",
			"selectPicture",
			"messageAnswer",
			"messageQuestion",
			"isTrueImage",
			"matchWords",
			"buildWord",
			"buildText",
			"dragDropIntoText",
			"selectWord",
			"selectWordReverse",
			"selectMatchingWordToImage",
			"selectMatchingWordToImageReverse",
			"selectPictureAndWord",
			"selectMatchingWord",
			"wordNotBelong",
			"pairedText",
			"textNotBelong",
			"selectAnswerToImage"
		];
	</script>
</head>

<body>
	<div class="card">
		<div class="card-body">
			<h4 class="card-title">Övningar</h4>
			<h6 class="text-muted card-subtitle mb-2">Hantera data till övningar</h6>
		</div>
	</div>
	<div class="row">
		<div class="col" style="padding: 10px;max-width: 300px;">
			<select id="selectLesson" style="margin-left: 10px;" onchange="SelectLesson();" disabled="">
				<optgroup label="V&#xE4;lj en lektion">
					<option value="stepLock" selected="">Lås steg</option>
					<option value="selectPicture">Välja bild till ord</option>
					<option value="messageAnswer">Meddelande svar</option>
					<option value="messageQuestion">Meddelande fråga</option>
					<option value="isTrueImage">Sant eller falskt</option>
					<option value="matchWords">Matcha ord</option>
					<option value="buildWord">Bygga upp ett ord</option>
					<option value="buildText">Bygga upp en mening</option>
					<option value="dragDropIntoText">Drag &amp; Släpp till text</option>
					<option value="selectWord">Välja ord</option>
					<option value="selectWordReverse">Omvänd välja ord</option>
					<option value="selectMatchingWordToImage">Välja matchade ord till bild</option>
					<option value="selectMatchingWordToImageReverse">Omvänd välja matchade ord till bild</option>
					<option value="selectPictureAndWord">Välja bild + text till ord</option>
					<option value="selectMatchingWord">Välj ett matchade ord</option>
					<option value="wordNotBelong">Ordet inte tillhör</option>
					<option value="pairedText">Parad text</option>
					<option value="textNotBelong">Textet inte tillhör</option>
					<option value="selectAnswerToImage">Välja ett svar till bild</option>
				</optgroup>
			</select>
		</div>
		<div class="col" style="padding: 10px; margin-left:25px;">
			<button onclick="NewLesson();" class="btn btn-primary" type="button">Ny lektion</button>
			<button onclick="DuplicateLesson();" class="btn btn-primary" type="button">Duplicera lektion</button>
			<button onclick="Data_Save();" id="save_lesson" class="btn btn-primary" type="button" disabled="">Spara</button>
			<button id="delete_lesson" onclick="RemoveData();" class="btn btn-primary" type="button" style="background-color: #d00;border-color: #c00;" disabled="">Ta bort</button>

			<button id="moveup_lesson" onclick="MoveUp();" class="btn btn-primary" type="button" style="background-color: #00a;border-color: #005;" disabled="">Upp</button>
			<button id="movedown_lesson" onclick="MoveDown();" class="btn btn-primary" type="button" style="background-color: #00a;border-color: #005;" disabled="">Ner</button>

			<button id="report_lesson" onclick="ReportThis();" class="btn btn-primary" type="button" style="background-color: #800; border-color: #500;">Rapportera här</button>
		</div>
	</div>
	<div class="row">
		<div class="col" style="max-width: 300px;">
			<ul class="list-group" id="lesson_list">
				<?php
				
				for ($i=0; $i < count($lessons); $i++)
				{

				?>

				<li onclick="SelectLessonList(this);" id="data_<?php echo $lessons[$i]["nStepIndex"] ?>" class="list-group-item list-data-item"><span><?php

				switch (json_decode($lessons[$i]["sData"])->quizType)
				{
					case 'stepLock':
						echo "Lås steg";
						break;
					case 'selectPicture':
						echo "Välja bild till ord";
						break;
					case 'messageAnswer':
						echo "Meddelande svar";
						break;
					case 'messageQuestion':
						echo "Meddelande fråga";
						break;
					case 'isTrueImage':
						echo "Sant eller falskt";
						break;
					case 'matchWords':
						echo "Matcha ord";
						break;
					case 'buildWord':
						echo "Bygga upp ett ord";
						break;
					case 'buildText':
						echo "Bygga upp en mening";
						break;
					case 'dragDropIntoText':
						echo "Drag &amp; Släpp till text";
						break;
					case 'selectWord':
						echo "Välja ord";
						break;
					case 'selectWordReverse':
						echo "Omvänd välja ord";
						break;
					case 'selectPictureAndWord':
						echo "Välja bild + text till ord";
						break;
					case 'selectMatchingWordToImage':
						echo "Välja matchade ord till bild";
						break;
					case 'selectMatchingWordToImageReverse':
						echo "Omvänd välja matchade ord till bild";
						break;
					case 'wordNotBelong':
						echo "Ordet inte tillhör";
						break;
					case 'selectMatchingWord':
						echo "Välj ett matchade ord";
						break;
					case 'pairedText':
						echo "Parad text";
						break;
					case 'textNotBelong':
						echo "Textet inte tillhör";
						break;
					case 'selectAnswerToImage':
						echo "Välja ett svar till bild";
						break;
				}

				?></span></li>

				<?php
				
				}
				
				?>
			</ul>
		</div>

		<div class="col" id="stepLock" style="padding: 25px;display: none;border: solid #ddd 1px;border-radius: 5px;">
			<h1>Lås steg</h1>
			<p>För att ta nästa steg, alla tidigare övningar måste klaras. <br><br><br> Och nej, man behöver inte ha det här som sista steget, bara mellan område i det här övningsgrupp. :)</p>
		</div>

		<div class="col" id="selectAnswerToImage" style="padding: 25px;display: none;border: solid #ddd 1px;border-radius: 5px;">
			<h1>Välja ett svar till bild</h1>
			<p><b onclick="ShowExampleImage(15, 'Välja ett svar till bild');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>

			<p><br>Bild</p>

			<div id="SelectAnswerToImage_Image" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="SelectAnswerToImage_ImageUpload(this);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 256px; height: 256px; border-radius: 5px; background-size: cover;">
				<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 256px; height: 256px; cursor: pointer; border-radius: 5px;" />
			</div>

			<p><br>Svar <b style="color:#f00;">(Engelska)</b></p>
			<input id="SelectAnswerToImage_Title"   onkeyup="SelectAnswerToImage_Title(this);" type="text" style="width: 300px;" placeholder="Question">
			<button class="btn btn-primary" onclick="SelectVideoWord(0, 'SelectAnswerToImage_Title', 1);">Välj video</button>
			<p><br>Svar <b style="color:#f00;">(Engelska)</b></p>
			<input id="SelectAnswerToImage_Radio_0" onclick="SelectAnswerToImage_CorrectIndex(0);" type="radio" name="SelectAnswerToImage">
			<input id="SelectAnswerToImage_GuessWord_0" onkeyup="SelectAnswerToImage_GuessWord(this, 0);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Answer 1">

			<button class="btn btn-primary" onclick="SelectVideoWord(1, 'SelectAnswerToImage_GuessWord_0', 2);">Välj video</button>
			<br>
			<input id="SelectAnswerToImage_Radio_1" onclick="SelectAnswerToImage_CorrectIndex(1);" type="radio" name="SelectAnswerToImage">
			<input id="SelectAnswerToImage_GuessWord_1" onkeyup="SelectAnswerToImage_GuessWord(this, 1);"type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Answer 2">

			<button class="btn btn-primary" onclick="SelectVideoWord(2, 'SelectAnswerToImage_GuessWord_1', 2);">Välj video</button>
			<br>
			<input id="SelectAnswerToImage_Radio_2" onclick="SelectAnswerToImage_CorrectIndex(2);" type="radio" name="SelectAnswerToImage">
			<input id="SelectAnswerToImage_GuessWord_2" onkeyup="SelectAnswerToImage_GuessWord(this, 2);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Answer 3">

			<button class="btn btn-primary" onclick="SelectVideoWord(3, 'SelectAnswerToImage_GuessWord_2', 2);">Välj video</button>
			<br>
			<input id="SelectAnswerToImage_Radio_3" onclick="SelectAnswerToImage_CorrectIndex(3);" type="radio" name="SelectAnswerToImage">
			<input id="SelectAnswerToImage_GuessWord_3" onkeyup="SelectAnswerToImage_GuessWord(this, 3);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Answer 4">

			<button class="btn btn-primary" onclick="SelectVideoWord(4, 'SelectAnswerToImage_GuessWord_3', 2);">Välj video</button>
			<br>
		</div>

		<div class="col" id="selectMatchingWordToImage" style="padding: 25px;display: none;border: solid #ddd 1px;border-radius: 5px;">
			<h1>Välja matchade ord till bild</h1>
			<p><b onclick="ShowExampleImage(10, 'Välja matchade ord till bild');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>

			<p><br>Bild</p>

			<div id="SelectMatchingWordToImage_Image" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="SelectMatchingWordToImage_ImageUpload(this);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 256px; height: 256px; border-radius: 5px; background-size: cover;">
				<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 256px; height: 256px; cursor: pointer; border-radius: 5px;" />
			</div>

			<p><br>Titel <b style="color:#00f;">(Svenska)</b></p>
			<input id="SelectMatchingWordToImage_Title"   onkeyup="SelectMatchingWordToImage_Title(this);" type="text" style="width: 300px;" placeholder="Röd bil">
			<button class="btn btn-primary" onclick="SelectVideoWord(0, 'SelectMatchingWordToImage_Title', 1);">Välj video</button>
			<p><br>Svar <b style="color:#f00;">(Engelska)</b></p>
			<input id="SelectMatchingWordToImage_Radio_0" onclick="SelectMatchingWordToImage_CorrectIndex(0);" type="radio" name="SelectMatchingWordToImage">
			<input id="SelectMatchingWordToImage_GuessWord_0" onkeyup="SelectMatchingWordToImage_GuessWord(this, 0);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Red car">

			<button class="btn btn-primary" onclick="SelectVideoWord(1, 'SelectMatchingWordToImage_GuessWord_0', 2);">Välj video</button>
			<br>
			<input id="SelectMatchingWordToImage_Radio_1" onclick="SelectMatchingWordToImage_CorrectIndex(1);" type="radio" name="SelectMatchingWordToImage">
			<input id="SelectMatchingWordToImage_GuessWord_1" onkeyup="SelectMatchingWordToImage_GuessWord(this, 1);"type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Red cart">

			<button class="btn btn-primary" onclick="SelectVideoWord(2, 'SelectMatchingWordToImage_GuessWord_1', 2);">Välj video</button>
			<br>
			<input id="SelectMatchingWordToImage_Radio_2" onclick="SelectMatchingWordToImage_CorrectIndex(2);" type="radio" name="SelectMatchingWordToImage">
			<input id="SelectMatchingWordToImage_GuessWord_2" onkeyup="SelectMatchingWordToImage_GuessWord(this, 2);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Red cat">

			<button class="btn btn-primary" onclick="SelectVideoWord(3, 'SelectMatchingWordToImage_GuessWord_2', 2);">Välj video</button>
			<br>
			<input id="SelectMatchingWordToImage_Radio_3" onclick="SelectMatchingWordToImage_CorrectIndex(3);" type="radio" name="SelectMatchingWordToImage">
			<input id="SelectMatchingWordToImage_GuessWord_3" onkeyup="SelectMatchingWordToImage_GuessWord(this, 3);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Red can">

			<button class="btn btn-primary" onclick="SelectVideoWord(4, 'SelectMatchingWordToImage_GuessWord_3', 2);">Välj video</button>
			<br>
		</div>

		<div class="col" id="selectMatchingWordToImageReverse" style="padding: 25px;display: none;border: solid #ddd 1px;border-radius: 5px;">
			<h1>Omvända Välja matchade ord till bild</h1>
			<p><b onclick="ShowExampleImage(11, 'Omvända Välja matchade ord till bild');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>

			<p><br>Bild</p>

			<div id="SMWTIR_R_Image" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="SelectMatchingWordToImageReverse_ImageUpload(this);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 256px; height: 256px; border-radius: 5px; background-size: cover;">
				<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 256px; height: 256px; cursor: pointer; border-radius: 5px;" />
			</div>

			<p><br>Titel <b style="color:#f00;">(Engelska)</b></p>
			<input id="sMWTIR_Title"   onkeyup="SelectMatchingWordToImageReverse_Title(this);" type="text" style="width: 300px;" placeholder="Red car">
			<button class="btn btn-primary" onclick="SelectVideoWord(0, 'sMWTIR_Title', 2);">Välj video</button>
			<p><br>Svar <b style="color:#00f;">(Svenska)</b></p>
			<input id="SMWTIR_R_0" onclick="SelectMatchingWordToImageReverse_CorrectIndex(0);" type="radio" name="selectMatchingWordToImageReverse">
			<input id="sMWTIR_GW_0" onkeyup="SelectMatchingWordToImageReverse_GuessWord(this, 0);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Röd bil">

			<button class="btn btn-primary" onclick="SelectVideoWord(1, 'sMWTIR_GW_0', 1);">Välj video</button>
			<br>
			<input id="SMWTIR_R_1" onclick="SelectMatchingWordToImageReverse_CorrectIndex(1);" type="radio" name="selectMatchingWordToImageReverse">
			<input id="sMWTIR_GW_1" onkeyup="SelectMatchingWordToImageReverse_GuessWord(this, 1);"type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Röd vagn">

			<button class="btn btn-primary" onclick="SelectVideoWord(2, 'sMWTIR_GW_1', 1);">Välj video</button>
			<br>
			<input id="SMWTIR_R_2" onclick="SelectMatchingWordToImageReverse_CorrectIndex(2);" type="radio" name="selectMatchingWordToImageReverse">
			<input id="sMWTIR_GW_2" onkeyup="SelectMatchingWordToImageReverse_GuessWord(this, 2);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Röd katt">

			<button class="btn btn-primary" onclick="SelectVideoWord(3, 'sMWTIR_GW_2', 1);">Välj video</button>
			<br>
			<input id="SMWTIR_R_3" onclick="SelectMatchingWordToImageReverse_CorrectIndex(3);" type="radio" name="selectMatchingWordToImageReverse">
			<input id="sMWTIR_GW_3" onkeyup="SelectMatchingWordToImageReverse_GuessWord(this, 3);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Röd burk">

			<button class="btn btn-primary" onclick="SelectVideoWord(4, 'sMWTIR_GW_3', 1);">Välj video</button>
			<br>
		</div>

		<div class="col" id="pairedText" style="padding: 25px;display: none;border: solid #ddd 1px;border-radius: 5px;">
			<h1>Parad text</h1>
			<p><b onclick="ShowExampleImage(16, 'Parad text');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
			
			<p><br><b style="color:#00f;">Text</b> <b style="color:#f00;">(Engelska)</b></p>
			1A <input id="PairedText_0" onkeyup="PairedText(this, 0);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Text 1.">
			<button class="btn btn-primary" onclick="SelectVideoText(0, 'PairedText_0', 2);">Välj video</button>
			
			<br>
			1B <input id="PairedText_1" onkeyup="PairedText(this, 1);"type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Text inked to 1A.">
			<button class="btn btn-primary" onclick="SelectVideoText(1, 'PairedText_1', 2);">Välj video</button>
			
			<br>
			2A <input id="PairedText_2" onkeyup="PairedText(this, 2);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Text 2.">
			<button class="btn btn-primary" onclick="SelectVideoText(2, 'PairedText_2', 2);">Välj video</button>
			
			<br>
			2B <input id="PairedText_3" onkeyup="PairedText(this, 3);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Text linked to 2A.">
			<button class="btn btn-primary" onclick="SelectVideoText(3, 'PairedText_3', 2);">Välj video</button>
			
			<br>
		</div>

		<div class="col" id="textNotBelong" style="padding: 25px;display: none;border: solid #ddd 1px;border-radius: 5px;">
			<h1>Textet inte tillhör</h1>
			<p><b onclick="ShowExampleImage(17, 'Textet inte tillhör');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
			
			<p><br><b style="color:#00f;">Text</b> <b style="color:#f00;">(Engelska)</b></p>
			<input id="TextNotBelong_Radio_0" onclick="MessageAnswer_CorrectIndex(0);" type="radio" name="textNotBelong">
			<input id="TextNotBelong_0" onkeyup="TextNotBelong(this, 0);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Table">
			<button class="btn btn-primary" onclick="SelectVideoText(1, 'TextNotBelong_0', 2);">Välj video</button>
			
			<br>
			<input id="TextNotBelong_Radio_1" onclick="MessageAnswer_CorrectIndex(1);" type="radio" name="textNotBelong">
			<input id="TextNotBelong_1" onkeyup="TextNotBelong(this, 1);"type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Dog">
			<button class="btn btn-primary" onclick="SelectVideoText(2, 'TextNotBelong_1', 2);">Välj video</button>
			
			<br>
			<input id="TextNotBelong_Radio_2" onclick="MessageAnswer_CorrectIndex(2);" type="radio" name="textNotBelong">
			<input id="TextNotBelong_2" onkeyup="TextNotBelong(this, 2);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Cat">
			<button class="btn btn-primary" onclick="SelectVideoText(3, 'TextNotBelong_2', 2);">Välj video</button>
			
			<br>
			<input id="TextNotBelong_Radio_3" onclick="MessageAnswer_CorrectIndex(3);" type="radio" name="textNotBelong">
			<input id="TextNotBelong_3" onkeyup="TextNotBelong(this, 3);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Mouse">
			<button class="btn btn-primary" onclick="SelectVideoText(4, 'TextNotBelong_3', 2);">Välj video</button>
			
			<br>
		</div>

		<div class="col" id="messageAnswer" style="padding: 25px;display: none;border: solid #ddd 1px;border-radius: 5px;">
			<h1>Meddelande svar</h1>
			<p><b onclick="ShowExampleImage(2, 'Meddelande svar');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
			<p><b style="color:#00f;">Fråga</b> <b style="color:#f00;">(Engelska)</b></p>
			<input id="MessageAnswer_Question" onkeyup="MessageAnswer_Question(this);" type="text" style="width: 300px;" placeholder="How are you?">
			<button class="btn btn-primary" onclick="SelectVideoText(0, 'MessageAnswer_Question', 2);">Välj video</button>
			

			<p><br><b style="color:#00f;">Svar</b> <b style="color:#f00;">(Engelska)</b></p>
			<input id="MessageAnswer_Radio_0" onclick="MessageAnswer_CorrectIndex(0);" type="radio" name="messageAnswer">
			<input id="MessageAnswer_Answer_0" onkeyup="MessageAnswer_Answer(this, 0);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="I'm good.">
			<button class="btn btn-primary" onclick="SelectVideoText(1, 'MessageAnswer_Answer_0', 2);">Välj video</button>
			
			<br>
			<input id="MessageAnswer_Radio_1" onclick="MessageAnswer_CorrectIndex(1);" type="radio" name="messageAnswer">
			<input id="MessageAnswer_Answer_1" onkeyup="MessageAnswer_Answer(this, 1);"type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="My name is Bo.">
			<button class="btn btn-primary" onclick="SelectVideoText(2, 'MessageAnswer_Answer_1', 2);">Välj video</button>
			
			<br>
			<input id="MessageAnswer_Radio_2" onclick="MessageAnswer_CorrectIndex(2);" type="radio" name="messageAnswer">
			<input id="MessageAnswer_Answer_2" onkeyup="MessageAnswer_Answer(this, 2);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="The weather is nice.">
			<button class="btn btn-primary" onclick="SelectVideoText(3, 'MessageAnswer_Answer_2', 2);">Välj video</button>
			
			<br>
			<input id="MessageAnswer_Radio_3" onclick="MessageAnswer_CorrectIndex(3);" type="radio" name="messageAnswer">
			<input id="MessageAnswer_Answer_3" onkeyup="MessageAnswer_Answer(this, 3);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="You are awesome too.">
			<button class="btn btn-primary" onclick="SelectVideoText(4, 'MessageAnswer_Answer_3', 2);">Välj video</button>
			
			<br>
		</div>

		<div class="col" id="messageQuestion" style="padding: 25px;display: none;border: solid #ddd 1px;">
			<h1>Meddelande fråga</h1>
			<p><b onclick="ShowExampleImage(1, 'Meddelande fråga');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
			<p><b style="color:#00f;">Svar</b> <b style="color:#f00;">(Engelska)</b></p>
			<input id="MessageQuestion_Answer" onkeyup="MessageQuestion_Answer(this);" type="text" style="width: 300px;" placeholder="I'm good.">
			<button class="btn btn-primary" onclick="SelectVideoText(0, 'MessageQuestion_Answer', 2);">Välj video</button>
			
			<p><br><b style="color:#00f;">Frågor</b> <b style="color:#f00;">(Engelska)</b></p>
			<input id="MessageQuestion_Radio_0" onclick="MessageQuestion_CorrectIndex(0);" type="radio" name="messageQuestion">
			<input id="MessageQuestion_Question_0" onkeyup="MessageQuestion_Question(this, 0);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="How are you?">
			<button class="btn btn-primary" onclick="SelectVideoText(1, 'MessageQuestion_Question_0', 2);">Välj video</button>
			
			<br>
			<input id="MessageQuestion_Radio_1" onclick="MessageQuestion_CorrectIndex(1);" type="radio" name="messageQuestion">
			<input id="MessageQuestion_Question_1" type="text" onkeyup="MessageQuestion_Question(this, 1);" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="What is your name?">
			<button class="btn btn-primary" onclick="SelectVideoText(2, 'MessageQuestion_Question_1', 2);">Välj video</button>
			
			<br>
			<input id="MessageQuestion_Radio_2" onclick="MessageQuestion_CorrectIndex(2);" type="radio" name="messageQuestion">
			<input id="MessageQuestion_Question_2" onkeyup="MessageQuestion_Question(this, 2);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="How is the weather?">
			<button class="btn btn-primary" onclick="SelectVideoText(3, 'MessageQuestion_Question_2', 2);">Välj video</button>
			
			<br>
			<input id="MessageQuestion_Radio_3" onclick="MessageQuestion_CorrectIndex(3);" type="radio" name="messageQuestion">
			<input id="MessageQuestion_Question_3" onkeyup="MessageQuestion_Question(this, 3);" type="text" style="width: 300px;margin-top: 10px;margin-left: 10px;" placeholder="Are you awesome?">
			<button class="btn btn-primary" onclick="SelectVideoText(4, 'MessageQuestion_Question_3', 2);">Välj video</button>
			
			<br>
		</div>
	<div class="col" id="isTrueImage" style="display: none; border: solid #ddd 1px; padding: 25px;">
		<h1>Sant eller falskt</h1>
		<p><b onclick="ShowExampleImage(3, 'Sant eller falskt');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
		<div class="row">
			<div class="col" style="height: 350px;padding: 25px;">
				<div id="IsTrueImage_Image" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="IsTrueImage_ImageUpload(this);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 256px; height: 256px; border-radius: 5px; margin: auto; background-size: cover;">
					<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 256px; height: 256px; cursor: pointer; border-radius: 5px;" />
				</div>
			</div>
			<div class="col" style="padding: 25px;">
				<input onkeyup="IsTrueImage_Title(this);" id="IsTrueImage_title" type="text" style="width: 48%;" placeholder="Yellow car">
				<p>Fråga bildtitel <b style="color:#f00;">(Engelska)</b></p>
				<button class="btn btn-primary" onclick="SelectVideoWord(0, 'IsTrueImage_title', 2);">Välj video</button>
				<br>
				<br>
				<input onkeyup="IsTrueImage_AnswerTitle(this);" id="IsTrueImage_answerTitle" type="text" style="width: 97%;" placeholder="Yellow car">
				<p>Svar titel <b style="color:#f00;">(Engelska)</b></p>
				<input onkeyup="IsTrueImage_AnswerTitle_Translation(this);" id="IsTrueImage_answerTitle_translation" type="text" style="width: 97%;" placeholder="Gul bil">
				<p>Svar titel <b style="color:#00f;">(Svenska)</b></p>
				<div class="form-check">
					<input onclick="IsTrueImage_CorrectIndex(1);" id="IsTrueImage_radio_0" name="IsTrueImage_radio" class="form-check-input" type="radio">
					<label class="form-check-label" for="formCheck-5">Sant</label>
				</div>
				<div class="form-check">
					<input onclick="IsTrueImage_CorrectIndex(0);" id="IsTrueImage_radio_1" name="IsTrueImage_radio" class="form-check-input" type="radio">
					<label class="form-check-label" for="formCheck-6">Falskt</label>
				</div>
			</div>
		</div>
		</div>
		<div class="col" id="matchWords" style="display: none;padding: 25px;border: solid #ddd 1px;">
			<h1>Matcha ord</h1>
			<p><b onclick="ShowExampleImage(4, 'Matcha ord');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
			<p>Max 5 ord</p>
			<input id="matchWords_A" onkeyup="MatchWords_A(this);" type="text" style="width: 100%;" placeholder="Hello_And_Car_House_Other">
			<p>Ord A <b style="color:#f00;">(Engelska)</b>. Använd "_" för att lägga in flera ord. Varje ord på sin ordning matchar&nbsp;</p>
			<input id="matchWords_B" onkeyup="MatchWords_B(this);" type="text" style="width: 100%;" placeholder="Hej_Och_Bil_Hus_Annat">
			<p>Ord B <b style="color:#00f;">(Svenska)</b>. Använd "_" för att lägga in flera ord.&nbsp;Varje ord på sin ordning matchar</p>
		</div>
		<div class="col" id="buildWord" style="display: none;padding: 25px;border: solid #ddd 1px;">
			<h1>Bygga upp ett ord</h1>
			<p><b onclick="ShowExampleImage(5, 'Bygga upp ett ord');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
			<input id="BuildWord_Original" onkeyup="BuildWord_Original(this);" type="text" style="width: 212px;" placeholder="Ord">
			<p>Original ord <b style="color:#00f;">(Svenska)</b></p>
			<input id="BuildWord_Build" onkeyup="BuildWord_Build(this);" type="text" style="width: 212px;" placeholder="Word">
			<p>Bygga ord <b style="color:#f00;">(Engelska)</b></p>
			<button class="btn btn-primary" onclick="SelectVideoWord(0, 'BuildWord_Build', 2);">Välj video</button>
		</div>
		<div class="col" id="buildText" style="display: none;padding: 25px;border: solid #ddd 1px;">
			<h1>Bygga upp en mening</h1>
			<p><b onclick="ShowExampleImage(6, 'Bygga upp en mening');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
			<input id="BuildText_Original" onkeyup="BuildText_Original(this);" type="text" style="width: 425px;" placeholder="Hej, hur mår du?">
			<p>Mening <b style="color:#00f;">(Svenska)</b></p>
			<input id="BuildText_Build" onkeyup="BuildText_Build(this);" type="text" style="width: 425px;" placeholder="Hello how are you">
			<p>Bygga mening <b style="color:#f00;">(Engelska)</b></p>
			<input id="BuildText_Useless" onkeyup="BuildText_Useless(this);" type="text" style="width: 425px;" placeholder="Goodbye is am what">
			<p>Onödiga ord <b style="color:#f00;">(Engelska)</b></p>
			<button class="btn btn-primary" onclick="SelectVideoText(0, 'BuildText_Original', 1);">Välj video</button>
		</div>
		<div class="col" id="dragDropIntoText" style="display: none;padding: 25px;border: solid #ddd 1px;">
			<h1>Drag &amp; Släpp till text</h1>
			<p><b onclick="ShowExampleImage(7, 'Drag &amp; Släpp till text');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
			<input id="DragDropIntoText_ContentText" onkeyup="DragDropIntoText_ContentText(this);" type="text" style="width: 350px;" placeholder="You awesome">
			<p>Halvfullständig text <b style="color:#f00;">(Engelska)</b></p>
			<input id="DragDropIntoText_FinalContentText" onkeyup="DragDropIntoText_FinalContentText(this);" type="text" style="width: 350px;" placeholder="You are awesome">
			<p>Fullständig text <b style="color:#f00;">(Engelska)</b></p>
			<input id="DragDropIntoText_DragWords" onkeyup="DragDropIntoText_DragWords(this);" type="text" style="width: 250px;" placeholder="are_is_am">
			<p>Drag & släpp ord och meningar <b style="color:#f00;">(Engelska)</b>. Använd "_" mellan olika ord.</p>
			<button class="btn btn-primary" onclick="SelectVideoText(0, 'DragDropIntoText_FinalContentText', 2);">Välj video</button>
		</div>

		<div class="col" id="selectWord" style="display: none;border: solid #ddd 1px;">
			<div class="row">
				<div class="col" style="padding: 25px;">
					<h1>Välja ord</h1>
					<p><b onclick="ShowExampleImage(8, 'Välja ord');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
					<input id="SelectWord_Word" onkeyup="SelectWord_Word(this);" type="text" style="width: 212px;" placeholder="Ord">
					<p>Ord <b style="color:#00f;">(Svenska)</b></p>
				<button class="btn btn-primary" onclick="SelectVideoWord(0, 'SelectWord_Word', 1);">Välj video</button>

				</div>
			</div>
			<div class="row">
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectWord_Radio_0" onclick="SelectWord_CorrectIndex(0);" class="form-check-input" type="radio" name="selectPictureWord">
						<label class="form-check-label">Ord <b style="color:#f00;">(Engelska)</b> & Video 1</label>
					</div>
					<br>
					<input id="SelectWord_GuesssWord_0" onkeyup="SelectWord_GuesssWords(this, 0);" type="text" style="width: 200px;margin: auto;" placeholder="Word 1">
				<button class="btn btn-primary" onclick="SelectVideoWord(1, 'SelectWord_GuesssWord_0', 2);">Välj video</button>
				</div>
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectWord_Radio_1" onclick="SelectWord_CorrectIndex(1);" class="form-check-input" type="radio" name="selectPictureWord">
						<label class="form-check-label">Ord <b style="color:#f00;">(Engelska)</b> & Video 2</label>
					</div>
					<br>
					<input id="SelectWord_GuesssWord_1" onkeyup="SelectWord_GuesssWords(this, 1);" type="text" style="width: 200px;margin: auto;" placeholder="Word 2">
					<button class="btn btn-primary" onclick="SelectVideoWord(2, 'SelectWord_GuesssWord_1', 2);">Välj video</button>

				</div>
			</div>
			<div class="row">
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectWord_Radio_2" onclick="SelectWord_CorrectIndex(2);" class="form-check-input" type="radio" name="selectPictureWord">
						<label class="form-check-label">Ord <b style="color:#f00;">(Engelska)</b> & Video 3</label>
					</div>
					<br>
					<input id="SelectWord_GuesssWord_2" onkeyup="SelectWord_GuesssWords(this, 2);" type="text" style="width: 200px;margin: auto;" placeholder="Word 3">

					<button class="btn btn-primary" onclick="SelectVideoWord(3, 'SelectWord_GuesssWord_2', 2);">Välj video</button>
				</div>
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectWord_Radio_3" onclick="SelectWord_CorrectIndex(3);" class="form-check-input" type="radio" name="selectPictureWord">
						<label class="form-check-label">Ord <b style="color:#f00;">(Engelska)</b> & Video 4</label>
					</div>
					<br>
					<input id="SelectWord_GuesssWord_3" onkeyup="SelectWord_GuesssWords(this, 3);" type="text" style="width: 200px;margin: auto;" placeholder="Word 4">

					<button class="btn btn-primary" onclick="SelectVideoWord(4, 'SelectWord_GuesssWord_3', 2);">Välj video</button>
				</div>
			</div>
		</div>

		<div class="col" id="selectWordReverse" style="display: none;border: solid #ddd 1px;">
			<div class="row">
				<div class="col" style="padding: 25px;">
					<h1>Omvänd välja ord</h1>
					<p><b onclick="ShowExampleImage(9, 'Omvänd välja ord');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
					<input id="SelectWordReverse_Word" onkeyup="SelectWordReverse_Word(this);" type="text" style="width: 212px;" placeholder="Word">
					<p>Ord <b style="color:#f00;">(Engelska)</b></p>
				<button class="btn btn-primary" onclick="SelectVideoWord(0, 'SelectWordReverse_Word', 2);">Välj video</button>

				</div>
			</div>
			<div class="row">
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectWordReverse_Radio_0" onclick="SelectWordReverse_CorrectIndex(0);" class="form-check-input" type="radio" name="selectPictureWordReverse">
						<label class="form-check-label">Ord <b style="color:#00f;">(Svenska)</b> & Video 1</label>
					</div>
					<br>
					<input id="SelectWordReverse_GuesssWord_0" onkeyup="SelectWordReverse_GuesssWords(this, 0);" type="text" style="width: 200px;margin: auto;" placeholder="Ord 1">
				<button class="btn btn-primary" onclick="SelectVideoWord(1, 'SelectWordReverse_GuesssWord_0', 1);">Välj video</button>
				</div>
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectWordReverse_Radio_1" onclick="SelectWordReverse_CorrectIndex(1);" class="form-check-input" type="radio" name="selectPictureWordReverse">
						<label class="form-check-label">Ord <b style="color:#00f;">(Svenska)</b> & Video 2</label>
					</div>
					<br>
					<input id="SelectWordReverse_GuesssWord_1" onkeyup="SelectWordReverse_GuesssWords(this, 1);" type="text" style="width: 200px;margin: auto;" placeholder="Ord 2">
					<button class="btn btn-primary" onclick="SelectVideoWord(2, 'SelectWordReverse_GuesssWord_1', 1);">Välj video</button>

				</div>
			</div>
			<div class="row">
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectWordReverse_Radio_2" onclick="SelectWordReverse_CorrectIndex(2);" class="form-check-input" type="radio" name="selectPictureWordReverse">
						<label class="form-check-label">Ord <b style="color:#00f;">(Svenska)</b> & Video 3</label>
					</div>
					<br>
					<input id="SelectWordReverse_GuesssWord_2" onkeyup="SelectWordReverse_GuesssWords(this, 2);" type="text" style="width: 200px;margin: auto;" placeholder="Ord 3">
					<button class="btn btn-primary" onclick="SelectVideoWord(3, 'SelectWordReverse_GuesssWord_2', 1);">Välj video</button>
				</div>
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectWordReverse_Radio_3" onclick="SelectWordReverse_CorrectIndex(3);" class="form-check-input" type="radio" name="selectPictureWordReverse">
						<label class="form-check-label">Ord <b style="color:#00f;">(Svenska)</b> & Video 4</label>
					</div>
					<br>
					<input id="SelectWordReverse_GuesssWord_3" onkeyup="SelectWordReverse_GuesssWords(this, 3);" type="text" style="width: 200px;margin: auto;" placeholder="Ord 4">
					<button class="btn btn-primary" onclick="SelectVideoWord(4, 'SelectWordReverse_GuesssWord_3', 1);">Välj video</button>
				</div>
			</div>
		</div>

		<div class="col" id="selectPicture" style="display: none;border: solid #ddd 1px;">
			<div class="row">
				<div class="col" style="padding: 25px;">
					<h1>Välja bild till ord</h1>
					<p><b onclick="ShowExampleImage(0, 'Välja bild till ord');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
					<input id="SelectPicture_Text" onkeyup="SelectPicture_Text(this);" type="text" style="width: 212px;" placeholder="English word" placeholder="Word">
					<p>Ord <b style="color:#f00;">(Engelska)</b></p>
				
					<button class="btn btn-primary" onclick="SelectVideoWord(0, 'SelectPicture_Text', 2);">Välj video</button>
				</div>
			</div>
			<div class="row">
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectPicture_Radio_0" onclick="SelectPicture_CorrectIndex(0);" name="SelectPicture_Radio" class="form-check-input" type="radio" name="selectPictureWord">
						<label class="form-check-label">Bild 1 <b style="color:#f00;">(Engelska)</b></label>
					</div>

					<div id="SelectPicture_Image_0" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="SelectPicture_ImageUpload(this, 0);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 200px; height: 200px; border-radius: 5px; margin: auto; background-size: cover;">
						<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 200px; height: 200px; cursor: pointer; border-radius: 5px;" />
					</div>
					<input onkeyup="SelectPicture_GuessWords(this, 0);" id="SelectPicture_GuessWord_0" type="text" style="width: 200px; margin: auto;display: block;" placeholder="English guess word 1">

				</div>
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectPicture_Radio_1" onclick="SelectPicture_CorrectIndex(1);" name="SelectPicture_Radio" class="form-check-input" type="radio" name="selectPictureWord">
						<label class="form-check-label">Bild 2 <b style="color:#f00;">(Engelska)</b></label>
					</div>

					<div id="SelectPicture_Image_1" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="SelectPicture_ImageUpload(this, 1);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 200px; height: 200px; border-radius: 5px; margin: auto; background-size: cover;">
						<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 200px; height: 200px; cursor: pointer; border-radius: 5px;" />
					</div>
					<input onkeyup="SelectPicture_GuessWords(this, 1);" id="SelectPicture_GuessWord_1" type="text" style="width: 200px;margin: auto;display: block;" placeholder="English guess word 2">
				</div>
			</div>
			<div class="row">
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectPicture_Radio_2" onclick="SelectPicture_CorrectIndex(2);" name="SelectPicture_Radio" class="form-check-input" type="radio" name="selectPictureWord">
						<label class="form-check-label">Bild 3 <b style="color:#f00;">(Engelska)</b></label>
					</div>

					<div id="SelectPicture_Image_2" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="SelectPicture_ImageUpload(this, 2);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 200px; height: 200px; border-radius: 5px; margin: auto; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 200px; height: 200px; cursor: pointer; border-radius: 5px;" />
					</div>
					<input onkeyup="SelectPicture_GuessWords(this, 2);" id="SelectPicture_GuessWord_2" type="text" style="width: 200px;margin: auto;display: block;" placeholder="English guess word 3">
				</div>
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectPicture_Radio_3" onclick="SelectPicture_CorrectIndex(3);" name="SelectPicture_Radio" class="form-check-input" type="radio" name="selectPictureWord">
						<label class="form-check-label">Bild 4 <b style="color:#f00;">(Engelska)</b></label>
					</div>

					<div id="SelectPicture_Image_3" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="SelectPicture_ImageUpload(this, 3);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 200px; height: 200px; border-radius: 5px; margin: auto; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 200px; height: 200px; cursor: pointer; border-radius: 5px;" />
					</div>
					<input onkeyup="SelectPicture_GuessWords(this, 3);" id="SelectPicture_GuessWord_3" type="text" style="width: 200px;margin: auto;display: block;" placeholder="English guess word 4">
				</div>
			</div>
		</div>
		<div class="col" id="selectPictureAndWord" style="display: none;border: solid #ddd 1px;">
			<div class="row">
				<div class="col" style="padding: 25px;">
					<h1>Välja bild + text till ord</h1>
					<p><b onclick="ShowExampleImage(12, 'Välja bild + text till ord');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
					<input id="SelectPictureAndWord_Word" onkeyup="SelectPictureAndWord_Word(this);" type="text" style="width: 212px;" placeholder="Ord">
					<p>Ord <b style="color:#00f;">(Svenska)</b></p>
					<button class="btn btn-primary" onclick="SelectVideoWord(0, 'SelectPictureAndWord_Word', 1);">Välj video</button>
				</div>
			</div>
			<div class="row">
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectPictureAndWord_Radio_0" onclick="SelectPictureAndWord_CorrectIndex(0);" class="form-check-input" type="radio" name="SelectPictureAndWord">
						<label class="form-check-label">Bild 1 <b style="color:#f00;">(Engelska)</b></label>
					</div>
					<div id="SelectPictureAndWord_Image_0" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="SelectPictureAndWord_ImageUpload(this, 0);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 200px; height: 200px; border-radius: 5px; margin: auto; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 200px; height: 200px; cursor: pointer; border-radius: 5px;" />
					</div>
					<br>
					<input id="SelectPictureAndWord_GuessWord_0" onkeyup="SelectPictureAndWord_GuessWords(this, 0);" type="text" style="width: 200px;margin: auto;display: block;" placeholder="Word 1">
				</div>
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectPictureAndWord_Radio_1" onclick="SelectPictureAndWord_CorrectIndex(1);" class="form-check-input" type="radio" name="SelectPictureAndWord">
						<label class="form-check-label">Bild 2 <b style="color:#f00;">(Engelska)</b></label>
					</div>
					<div id="SelectPictureAndWord_Image_1" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="SelectPictureAndWord_ImageUpload(this, 1);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 200px; height: 200px; border-radius: 5px; margin: auto; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 200px; height: 200px; cursor: pointer; border-radius: 5px;" />
					</div>
					<br>
					<input id="SelectPictureAndWord_GuessWord_1" onkeyup="SelectPictureAndWord_GuessWords(this, 1);" type="text" style="width: 200px;margin: auto;display: block;" placeholder="Word 2">
				</div>
			</div>
			<div class="row">
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectPictureAndWord_Radio_2" onclick="SelectPictureAndWord_CorrectIndex(2);" class="form-check-input" type="radio" name="SelectPictureAndWord">
						<label class="form-check-label">Bild 3 <b style="color:#f00;">(Engelska)</b></label>
					</div>
					<div id="SelectPictureAndWord_Image_2" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="SelectPictureAndWord_ImageUpload(this, 2);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 200px; height: 200px; border-radius: 5px; margin: auto; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 200px; height: 200px; cursor: pointer; border-radius: 5px;" />
					</div>
					<br>
					<input id="SelectPictureAndWord_GuessWord_2" onkeyup="SelectPictureAndWord_GuessWords(this, 2);" type="text" style="width: 200px;margin: auto;display: block;" placeholder="Word 3">
				</div>
				<div class="col" style="padding: 25px;">
					<div class="form-check">
						<input id="SelectPictureAndWord_Radio_3" onclick="SelectPictureAndWord_CorrectIndex(3);" class="form-check-input" type="radio" name="SelectPictureAndWord">
						<label class="form-check-label">Bild 4 <b style="color:#f00;">(Engelska)</b></label>
					</div>
					<div id="SelectPictureAndWord_Image_3" ondragenter="_ImageDragEnter(this);" ondragover="return false;" ondragexit="_ImageDragLeave(this);" ondrop="_ImageDrop(this);" onchange="SelectPictureAndWord_ImageUpload(this, 3);" style="background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); display: block; width: 200px; height: 200px; border-radius: 5px; margin: auto; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 200px; height: 200px; cursor: pointer; border-radius: 5px;" />
					</div>
					<br>
					<input id="SelectPictureAndWord_GuessWord_3" onkeyup="SelectPictureAndWord_GuessWords(this, 3);" type="text" style="width: 200px;margin: auto;display: block;" placeholder="Word 4">
				</div>
			</div>
		</div>
		<div class="col" id="selectMatchingWord" style="padding: 25px;display: none;border: solid #ddd 1px;">
			<h1>Välj ett matchade ord</h1>
			<p><b onclick="ShowExampleImage(13, 'Välj ett matchade ord');" style="color: #00f; cursor: pointer; text-decoration: underline;">Exempel</b></p>
			<input type="text" id="SelectMatchingWord_title" onkeyup="SelectMatchingWord_Title(this);" style="width: 300px;" placeholder="Hello, {0} are you?">
			<button class="btn btn-primary" onclick="SelectVideoText(0, 'SelectMatchingWord_title', 2);">Välj video</button>
			<p>Skriv {0} i en mening för att länka till ett ord. <b style="color:#f00;">(Engelska)</b></p>
			<p>Matchade ord <b style="color:#f00;">(Engelska)</b></p>
			<input type="radio" id="SelectMatchingWord_radio_0" name="SelectMatchingWord" onclick="SelectMatchingWord_CorrectIndex(0);">
			<input type="text" id="SelectMatchingWord_input_0" onkeyup="SelectMatchingWord_Input(this, 0);" style="margin-left: 10px;margin-top: 5px;width: 300px;" placeholder="how">
			<button class="btn btn-primary" onclick="SelectVideoWord(1, 'SelectMatchingWord_input_0', 2);">Välj video</button>
			<br>
			<input type="radio" id="SelectMatchingWord_radio_1" name="SelectMatchingWord" onclick="SelectMatchingWord_CorrectIndex(1);">
			<input type="text" id="SelectMatchingWord_input_1" onkeyup="SelectMatchingWord_Input(this, 1);" style="margin-left: 10px;margin-top: 5px;width: 300px;" placeholder="name">
			<button class="btn btn-primary" onclick="SelectVideoWord(2, 'SelectMatchingWord_input_1', 2);">Välj video</button>
			<br>
			<input type="radio" id="SelectMatchingWord_radio_2" name="SelectMatchingWord" onclick="SelectMatchingWord_CorrectIndex(2);">
			<input type="text" id="SelectMatchingWord_input_2" onkeyup="SelectMatchingWord_Input(this, 2);" style="margin-left: 10px;margin-top: 5px;width: 300px;" placeholder="is">
			<button class="btn btn-primary" onclick="SelectVideoWord(3, 'SelectMatchingWord_input_2', 2);">Välj video</button>
			<br>
			<input type="radio" id="SelectMatchingWord_radio_3" name="SelectMatchingWord" onclick="SelectMatchingWord_CorrectIndex(3);">
			<input type="text" id="SelectMatchingWord_input_3" onkeyup="SelectMatchingWord_Input(this, 3);" style="margin-left: 10px;margin-top: 5px;width: 300px;" placeholder="cat">
			<button class="btn btn-primary" onclick="SelectVideoWord(4, 'SelectMatchingWord_input_3', 2);">Välj video</button>
		</div>
		<div class="col" id="wordNotBelong" style="padding: 25px;display: none;border: solid #ddd 1px;">
			<h1>Ordet inte tillhör</h1>
			<p><b onclick="ShowExampleImage(14, 'Ordet inte tillhör');" style="color: #00f; text-decoration: underline; cursor: pointer; text-decoration: underline;">Exempel</b></p>
			<input id="WordNotBelong_Text" onkeyup="WordNotBelong_Text(this);" type="text" style="width: 212px;" placeholder="You is are awesome">
			<p>Välja ett ord som tillhör inte mening. <b style="color:#f00;">(Engelska)</b></p>
			<div id="WordNotBelong_Buttons">
				<button id="WordNotBelong_Button_0" onclick="" class="btn btn-primary" type="button" style="margin-left: 10px;">Button</button>
			</div>
			<br>
			<button class="btn btn-primary" onclick="SelectVideoText(0, 'WordNotBelong_Text', 2);">Välj video</button>

		</div>
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

	<!-- Video Modal -->
	<div class="modal fade" id="modal_video">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal_video_title">Modal title</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="modal_video_content">
					<video controls autoplay loop style="width: 100%;">
						<source id="previewVideo" src="">
					</video>
					<div style="width: 100%;">
						<div id="alt_1" style="width: 50%; float: left;">
						</div>
						<div id="alt_2" style="width: 50%; float: right;">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button id="modal_video_close" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button id="modal_video_ok" type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Example Modal -->
	<div class="modal fade" id="modal_example">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal_example_title">Example</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="modal_modal_example_title_content">
					<img style="height:500px; border: solid #aaf 2px; border-radius: 25px;" id="modal_example_image" src="" />
				</div>
				<div class="modal-footer">
					<button id="modal_video_close" type="button" class="btn btn-secondary" data-dismiss="modal">Stäng</button>
				</div>
			</div>
		</div>
	</div>

	<script src="../assets/js/jquery.min.js"></script>
	<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
	<script src="../assets/js/jquery-ui.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.js"></script>
	<script src="../assets/js/theme.js"></script>

	<script type="text/javascript">
		<?php
		if($stepIndex != null)
		{
			echo "SelectLessonID(" . $stepIndex . ");";
		}
		?>
	</script>
</body>
</html>