<?php

if(!isset($_GET['zoneID']) || !is_numeric($_GET['zoneID'])
|| !isset($_GET['packageID']) || !is_numeric($_GET['packageID'])
)
{
	exit();
}

include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';

$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$explainer = $connect->query("SELECT * FROM Explainer WHERE nPackageID = :packageID AND nZoneID = :zoneID ORDER BY nStepIndex",
[
	"packageID" => $_GET["packageID"],
	"zoneID" => $_GET["zoneID"]
]);
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

		.table-header
		{
			width:100%; 
			background-color:#006A71;
		}

		.table-header-input
		{
			width:100%;
			color:#fff;
			background-color:#005960;
			border:5px solid #005960;
		}

		.table-input
		{
			width:100%;
		}
	</style>
	<script type="text/javascript" src="js/function.js?<?php echo $date; ?>"></script>

	<script type="text/javascript">
		var packageID = <?php echo $_GET["packageID"]; ?>;
		var zoneID = <?php echo $_GET["zoneID"]; ?>;

		var allTypes =
		[
			"bigTitle",
			"title",
			"text",
			"textHighlight",
			"table",
			"video"
		];
	</script>
</head>

<body>
	<div class="card">
		<div class="card-body">
			<h4 class="card-title">Förklaringar</h4>
			<h6 class="text-muted card-subtitle mb-2">Hantera data till förklaringar</h6>
		</div>
	</div>
	<div class="row">
		<div class="col" style="padding: 10px;max-width: 300px;">
			<select id="selectData" style="margin-left: 10px;">
				<optgroup label="V&#xE4;lj en datatyp">
					<option value="bigTitle" selected>Stor titel</option>
					<option value="title">Titel</option>
					<option value="text">Text</option>
					<option value="textHighlight">Text med bakgrundsfärg</option>
					<option value="table">Tabell</option>
					<option value="video">Video</option>
				</optgroup>
			</select>
		</div>
		<div class="col" style="padding: 10px; margin-left:25px;">
			<button onclick="NewData();" class="btn btn-primary" type="button">Ny data</button>
			<div id="uploadProgressNumber"></div>
		</div>
	</div>

	<div class="row">
		<div id="data_list" class="col" style="margin-left:50%;">
				<?php                
				for ($i=0; $i < count($explainer); $i++)
				{
				?>
<br>
<div name="eData" stepIndex="<?php echo $explainer[$i]['nStepIndex']; ?>" style="width: 430px; margin-left:-215px; border-color: #c00; border: solid #aaa 1px;border-radius: 5px; padding: 5px;">
<div style="height:50px;">
<div style="float: left;">
<p style="color:#04f;">
<?php
switch (json_decode($explainer[$i]["sData"])->dataType)
{
	case 'bigTitle':
		echo "Stor titel";
		break;
	case 'title':
		echo "Titel";
		break;
	case 'text':
		echo "Text";
		break;
	case 'textHighlight':
		echo "Text med bakgrundsfärg";
		break;
	case 'table':
		echo "Tabell";
		break;
	case 'video':
		echo "Video";
		break;
}
?>
</p>
</div>
<div name="right" style="float: right;">

	<button name="remove" id="remove_<?php echo $explainer[$i]['nID']; ?>" onclick="RemoveData(this);" class="btn btn-primary" type="button" style="background-color: #d00;border-color: #c00; <?php echo $explainer[$i]['bUploaded'] < 2 ? '' : 'display:none;'; ?>">Ta bort</button>
	
	<button onclick="MoveUp(this);" class="btn btn-primary" type="button" style="background-color: #00a;border-color: #005;">Upp</button>
	<button onclick="MoveDown(this);" class="btn btn-primary" type="button" style="background-color: #00a;border-color: #005;">Ner</button>
</div>
</div>

<?php
switch (json_decode($explainer[$i]["sData"])->dataType)
{
	case 'bigTitle':
?>

<div class="col" style="padding: 25px;display: block; border: solid #ddd 1px;border-radius: 5px;">
	<input onchange="ChangeText(this);" style="width:200px;" type="text" value="<?php echo htmlspecialchars(json_decode($explainer[$i]['sData'])->content); ?>" />
	<br><br>
	<button name='title' onclick="UpdateText(this, 'bigTitle');" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera</button>
</div>

<?php
		break;
	case 'title':
?>

<div class="col" style="padding: 25px;display: block; border: solid #ddd 1px;border-radius: 5px;">
	<input onchange="ChangeText(this);" style="width:320px;" type="text" value="<?php echo htmlspecialchars(json_decode($explainer[$i]['sData'])->content); ?>" />
	<br><br>
	<button name='title' onclick="UpdateText(this, 'title');" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera</button>
</div>

<?php
		break;
	case 'text':
?>

<div class="col" style="padding: 25px;display: block; border: solid #ddd 1px;border-radius: 5px;">
	<textarea onchange="ChangeText(this);" style="width:370px;" type="textarea"><?php echo htmlspecialchars(json_decode($explainer[$i]['sData'])->content); ?></textarea>
	<br><br>
	<button name='title' onclick="UpdateText(this, 'text');" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera</button>
</div>

<?php
		break;
	case 'textHighlight':
?>

<div class="col" style="padding: 25px;display: block; border: solid #ddd 1px;border-radius: 5px;">
	<textarea onchange="ChangeText(this);" style="width:370px; color:#fff; background-color:#04f;" type="textarea"><?php echo htmlspecialchars(json_decode($explainer[$i]['sData'])->content); ?></textarea>
	<br><br>
	<button name='title' onclick="UpdateText(this, 'textHighlight');" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera</button>
</div>

<?php
		break;
	case 'table':
?>

<div class="col" style="padding: 25px;display: block; border: solid #ddd 1px;border-radius: 5px;">
	<button name='alter' onclick="TableAlterRow(this, 1);" class="btn btn-primary" type="button" style="width:49%; float: left; background-color: #000; border-color: #000;">+1 Rad</button>
	<button name='alter' onclick="TableAlterCell(this, 1);" class="btn btn-primary" type="button" style="width:49%; float: right; background-color: #000; border-color: #000;">+1 Cell</button>

	<br>
	<br>

	<button name='alter' onclick="TableAlterRow(this, -1);" class="btn btn-primary" type="button" style="width:49%; float: left; background-color: #900; border-color: #000;">-1 Rad</button>
	<button name='alter' onclick="TableAlterCell(this, -1);" class="btn btn-primary" type="button" style="width:49%; float: right; background-color: #900; border-color: #000;">-1 Cell</button>

	<br>
	<br>

	<table class="table table-bordered">
<?php

$table = json_decode(json_decode($explainer[$i]['sData'])->content);

if($table != null && count($table->Items) > 0)
{
	$W = count($table->Items);
	$H = count($table->Items[0]->y);

?>

<tr class="table-header">

<?php
for($x = 0; $x < $W; $x++)
{

?>

<th><input placeholder=". . ." onchange="ChangeTextTable(this);" value="<?php echo htmlspecialchars($table->Items[$x]->y[0]); ?>" type="text" class="table-header-input" /></th>

<?php

}
?>

</tr>

<?php

	for($y = 1; $y < $H; $y++)
	{
?>
<tr>
<?php
		for($x = 0; $x < $W; $x++)
		{
?>

<th><input placeholder=". . ." onchange="ChangeTextTable(this);" value="<?php echo htmlspecialchars($table->Items[$x]->y[$y]); ?>" type="text" class="table-input" /></th>
<?php } ?>
</tr>
<?php } } ?>

	</table>

	<br><br>
	<button name='title' onclick="UpdateTable(this);" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera</button>
</div>

<?php
		break;
	case 'video':
?>

<div class="col" style="padding: 25px;display: block; border: solid #ddd 1px;border-radius: 5px;">
	<input onchange="ChangeVideoTitle(this);" style="width:320px;" type="text" value="<?php echo htmlspecialchars(json_decode($explainer[$i]['sData'])->content); ?>" />
	<br>
	<br>
	<button name='title' onclick="UpdateVideoTitle(this);" class="btn btn-primary" type="button" style="width:45%; background-color: #00a; border-color: #005;">Uppdatera titel</button>
	<br>
	<br>
	<video width="370" controls autoplay loop style="display:<?php echo $explainer[$i]['bUploaded'] == 1 ? "block" : "none"; ?>;">
		<source src="https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Videos/explain_<?php echo $explainer[$i]['nID'] . '.mp4'; ?>" type="video/mp4">
	</video>

	<div name="upload_div" id="vupload_<?php echo $explainer[$i]['nID']; ?>" style="display: <?php 
	
	echo $explainer[$i]['bUploaded'] == 0 ? 'block' : 'none'; 

	?>">
		<input onchange="UpdateVideo(this);" type="file" accept="video/mp4" style="width:45%;" />
		<button onclick="UploadVideo(this);" class="btn btn-primary" type="button" style="width:45%; background-color: #00a; border-color: #005;">Ladda upp</button>
		<div name="progress"></div>
	</div>

	<div name="update_div" id="vupdate_<?php echo $explainer[$i]['nID']; ?>" style="display: <?php 
	
	echo $explainer[$i]['bUploaded'] == 2 ? 'block' : 'none'; 

	?>">
		<button onclick="ShowVideo(<?php echo $explainer[$i]['nID']; ?>);" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera video</button>
		<div name="progress"></div>
	</div>

	<div name="render_div" id="vrender_<?php echo $explainer[$i]['nID']; ?>" style="display: <?php 
	
	echo $explainer[$i]['bUploaded'] == 3 ? 'block' : 'none'; 

	?>">
		Video renderar i bakgrunden.
	</div>

</div>

<?php
		break;
}
?>

</div>
				<?php
				}
				?>
		</div>
	</div>




<div name="eData" id="clone_data" stepIndex="-1" style="width: 430px; margin-left:-215px; display:none;">
	<br>
<div style="height:50px;">
<div style="float: left;">
<p style="color:#04f;">
TITEL
</p>
</div>
<div style="float: right;">
	<button onclick="RemoveData(this);" class="btn btn-primary" type="button" style="background-color: #d00;border-color: #c00;">Ta bort</button>
	<button onclick="MoveUp(this);" class="btn btn-primary" type="button" style="background-color: #00a;border-color: #005;">Upp</button>
	<button onclick="MoveDown(this);" class="btn btn-primary" type="button" style="background-color: #00a;border-color: #005;">Ner</button>
</div>
</div>

<!-- Big title -->
<div name="bigTitle" class="col" style="padding: 25px;display: none; border: solid #ddd 1px;border-radius: 5px;">
	<input onchange="ChangeText(this);" style="width:200px;" type="text" value="" />
	<br><br>
	<button name='title' onclick="UpdateText(this, 'bigTitle');" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera</button>
</div>

<!-- Title -->
<div name="title" class="col" style="padding: 25px;display: none; border: solid #ddd 1px;border-radius: 5px;">
	<input onchange="ChangeText(this);" style="width:320px;" type="text" value="" />
	<br><br>
	<button name='title' onclick="UpdateText(this, 'title');" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera</button>
</div>

<!-- Text -->
<div name="text" class="col" style="padding: 25px;display: none; border: solid #ddd 1px;border-radius: 5px;">
	<textarea onchange="ChangeText(this);" style="width:370px;" type="textarea"></textarea>
	<br><br>
	<button name='title' onclick="UpdateText(this, 'text');" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera</button>
</div>

<!-- TextHighlight -->
<div name="textHighlight" class="col" style="padding: 25px;display: none; border: solid #ddd 1px;border-radius: 5px;">
	<textarea onchange="ChangeText(this);" style="width:370px; color:#fff; background-color:#04f;" type="textarea"></textarea>
	<br><br>
	<button name='title' onclick="UpdateText(this, 'textHighlight');" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera</button>
</div>

<!-- Table -->

<div name="table" class="col" style="padding: 25px;display: none; border: solid #ddd 1px;border-radius: 5px;">
	<button name='alter' onclick="TableAlterRow(this, 1);" class="btn btn-primary" type="button" style="width:49%; float: left; background-color: #000; border-color: #000;">+1 Rad</button>
	<button name='alter' onclick="TableAlterCell(this, 1);" class="btn btn-primary" type="button" style="width:49%; float: right; background-color: #000; border-color: #000;">+1 Cell</button>

	<br>
	<br>

	<button name='alter' onclick="TableAlterRow(this, -1);" class="btn btn-primary" type="button" style="width:49%; float: left; background-color: #900; border-color: #000;">-1 Rad</button>
	<button name='alter' onclick="TableAlterCell(this, -1);" class="btn btn-primary" type="button" style="width:49%; float: right; background-color: #900; border-color: #000;">-1 Cell</button>

	<br>
	<br>

	<table class="table table-bordered">
	</table>

	<br><br>
	<button name='title' onclick="UpdateTable(this);" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera</button>
</div>

<!-- Video -->
<div name="video" class="col" style="padding: 25px;display: none; border: solid #ddd 1px;border-radius: 5px;">
	<input onchange="ChangeVideoTitle(this);" style="width:320px;" type="text" value="" />
	<br>
	<br>
	<button name='title' onclick="UpdateVideoTitle(this);" class="btn btn-primary" type="button" style="width:45%; background-color: #00a; border-color: #005;">Uppdatera titel</button>
	<br>
	<br>
	<video width="370" controls autoplay loop style="display: none">
		<source src="" type="video/mp4">
	</video>

	<div name="upload_div" id="vupload_id" style="display: none;">
		<input onchange="UpdateVideo(this);" type="file" accept="video/mp4" style="width:45%;" />
		<button onclick="UploadVideo(this);" class="btn btn-primary" type="button" style="width:45%; background-color: #00a; border-color: #005;">Ladda upp</button>
		<div name="progress"></div>
	</div>

	<div name="update_div" id="vupdate_id" style="display: none;">
		<button onclick="ShowVideo('id');" class="btn btn-primary" type="button" style="width:100%; background-color: #00a; border-color: #005;">Uppdatera video</button>
		<div name="progress"></div>
	</div>

	<div name="render_div" id="vrender_id" style="display: none;">
		Video renderar i bakgrunden.
	</div>

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
					
					<video style="width: 100%; display: none;" controls loop autoplay>
						<source id="modal_show_video_preview_video" src="">
					</video>
				</div>
				<div class="modal-footer">
					<button id="modal_show_video_close" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button id="modal_show_video_ok" type="button" class="btn btn-danger">Delete</button>
					<button id="modal_show_video_render" onclick="RenderVideo();" type="button" class="btn btn-primary">Rendera Video</button>
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