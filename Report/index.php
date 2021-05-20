<?php
include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$reports = $connect->query("SELECT * FROM Report");
?>

<!DOCTYPE html>
<html>
<head>
	<title>Reports</title>
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
		.list
		{
			background-color:#ddd;
			height: 200px;
			padding: 16px;
			border-radius:8px;
		}
		.content
		{
			background-color:#eee;
			padding:16px;
			border-radius:8px;
			height:168px;
			max-width: 400px;
			margin-top:25px;
			border: solid 2px #faa;
		}
		.image
		{
			height:200px; 
			float:left;
			border-radius:8px;
		}
	</style>
	<script type="text/javascript" src="functions.js?1"></script>
</head>
<body>
	<div class="card">
		<div class="card-body">
			<h4 class="card-title">Felrapport</h4>
			<h6 class="text-muted card-subtitle mb-2">Felrapport från användare</h6>
		</div>
	</div>

<?php
for($i = 0; $i < count($reports); $i++)
{
	?>

<div id="report_<?php echo $reports[$i]["nID"]; ?>" class="row" style="display: flex; border-bottom: solid 1px #eee;">
		<div class="col" style="padding-left:32px; padding-bottom:32px;">
			<div class="content">
					<i><?php echo (string)$reports[$i]['sText']; ?></i>
				</div>
		</div>

		<div class="col" style="border-left: solid #eee 1px; padding: 25px;margin: 0;max-width: 175px; <?php echo $backgroundColor; ?>">
			<button onclick="EditReport('<?php echo $reports[$i]['nDataID']; ?>');" type="button" class="btn btn-primary" style="width: 115px;">Redigera</button><br/><br/>
			<button onclick="RemoveReport(<?php echo $reports[$i]['nID']; ?>);" type="button" class="btn btn-danger" style="width: 115px;">Ta bort</button><br/><br/>
		</div>
	</div>
	<?php
}
?>

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
				<button id="modal_close" type="button" class="btn btn-secondary" data-dismiss="modal">Avbryta</button>
				<button id="modal_ok" type="button" class="btn btn-danger">Ta bort</button>
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


<?php
exit();