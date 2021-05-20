<?php

include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once $_SERVER['LIBRARY'] . '/database.simple.class.php';
$connect = new SDB(DB_USER, DB_PASSWORD, 'app_english');
$zones = $connect->query("SELECT * FROM Zone ORDER BY nID");
$date = strtotime(date('Y-m-d H:i:s'));
//$packages = $connect->query("SELECT * FROM Zone ORDER BY nID");

$haveReport = count($connect->query("SELECT nID FROM Report")) > 0;

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
	<script type="text/javascript" src="package/functions.js?<?php echo $date; ?>"></script>
</head>

<body>
	<div class="card">
		<div class="card-body">
			<a href="https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Report">Gå till felrapport</a> <i <?php echo $haveReport ? 'class="fa fa-warning"' : ''; ?> style="color:#f00;" ></i>
			<br>
			<br>
			<a href="https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Video/index2.php">Gå till ordlista</a>
			<br>
			<a href="https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Video/text2.php">Gå till textlista</a>
			<br>
			<br>
			<button id="updateID" onclick="UpdateApp();">Update App database</button>
			<br>
			<p id="version_control">Version: <?php include __DIR__ . "/../Download/package_version.php"; ?></p>
			<h4 class="card-title">Redigera lektioner</h4>
			<h6 class="text-muted card-subtitle mb-2">Område och paket</h6>
			<p class="card-text">Varje lektion har 6 paket</p>
		</div>
	</div>
	<?php

	for($z = 0; $z < count($zones); $z++)
	{
		if($z == 0)
		{
	?>

	<div id="zone_<?php echo $zones[$z]['nID']; ?>" data-activated="<?php echo strval($zones[$z]['bActivated']); ?>" class="row" style="display: flex; border: solid #eee 1px; background-image: url('https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Images/Cover/<?php echo $zones[$z]['nID']; ?>.jpg'); background-repeat: no-repeat; background-position: center;  background-size: cover;">
		
	<?php
		}
		else
		{
	?>
	</div>
	<div id="zone_<?php echo $zones[$z]['nID']; ?>" data-activated="<?php echo strval($zones[$z]['bActivated']); ?>" class="row" style="display: flex; border: solid #eee 1px; background-image: url('https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Images/Cover/<?php echo $zones[$z]['nID']; ?>.jpg'); background-repeat: no-repeat; background-position: center;  background-size: cover;">
	<?php
		}
	?>

	<div class="col" style="padding: 25px; display: block;">
		<div style="position: absolute; left:0; right: 0; top: 0; bottom: 0; opacity: 0.9; background-color: #fff;"></div>
		
		<div class="row" style="margin: auto; width: 700px; height: 450px;">
			<div class="col">
				<div class="row" style="height: 80px;">
					<div class="col">
						<input onkeydown="UpdateZoneTitle(this);" type="text" style="height: 30px;width: 640px;" value="<?php echo $zones[$z]['sTitle']; ?>" placeholder="Title">
						<button onclick="UploadZoneTitle(this, <?php echo $zones[$z]['nID']; ?>);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
					</div>
					<div class="col">
						<input onkeydown="UpdateZoneDescription(this);" type="text" style="height: 30px;width: 640px;" value="<?php echo $zones[$z]['sDescription']; ?>" placeholder="Description">
						<button onclick="UploadZoneDescription(this, <?php echo $zones[$z]['nID']; ?>);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
					</div>
					<img onclick="RemoveZone(<?php echo $zones[$z]['nID']; ?>);" style="position: absolute; cursor: pointer; margin-left: -30px; height: 25px;width: 25px; display: block;border-radius: 50px;" src="../assets/img/remove.png">
					<button name="activate" style="width:29px; height: 25px; position: absolute; cursor: pointer; margin-left: -32px; margin-top: 40px; background-color: #<?php 
						if(strval($zones[$z]["bActivated"]) == "1") 
							echo "afa";
						else
							echo "faa";
						?>; border-radius: 13px; border: none;" onclick="ToggleActive(<?php echo $zones[$z]['nID']; ?>);"><i class="fa fa-power-off"></i></button>
				</div>

				<?php
				// packages
				$packages = $connect->query("SELECT * FROM Package WHERE nZoneID = :zoneID ORDER BY nStepIndex",
					[
						'zoneID' => intval($zones[$z]['nID'])
					]
				);

				//var_dump($packages[0]['sTitle']);
				//var_dump($packages[0]);

				?>

				<!-- 6 packages -->
				<!-- top -->
				<div class="row" style="height: 180px;">
					<div id="p<?php echo strval($packages[0]['nID']); ?>" class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="<?php echo htmlentities($packages[0]['sTitle']); ?>" placeholder="Package name">
							<button onclick="UploadTitle(this, <?php echo $packages[0]["nID"]; ?>);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(<?php echo strval($packages[0]['nID']); ?>);"><i class="fa fa-edit"></i></button>

						<button name="moveButton" pid="<?php echo strval($packages[0]['nID']); ?>" style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this, <?php echo strval($packages[0]['nID']); ?>);"><i class="fa fa-arrow-right"></i></button>

						<div ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this, <?php echo $packages[0]["nID"]; ?>);" style="margin-top: 0px; background-color: #eee; border: 1px solid #aaa; background-image: url('https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Images/Package/<?php echo $packages[0]["nID"]; ?>.jpg'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
								<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
					<div id="p<?php echo strval($packages[1]['nID']); ?>" class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="<?php echo htmlentities($packages[1]['sTitle']); ?>" placeholder="Package name">
							<button onclick="UploadTitle(this, <?php echo $packages[1]["nID"]; ?>);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(<?php echo strval($packages[1]['nID']); ?>);"><i class="fa fa-edit"></i></button>

						<button name="moveButton" pid="<?php echo strval($packages[1]['nID']); ?>" style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this, <?php echo strval($packages[1]['nID']); ?>);"><i class="fa fa-arrow-right"></i></button>

						<div ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this, <?php echo $packages[1]["nID"]; ?>);" style="margin-top: 0px; background-color: #eee; border: 1px solid #aaa; background-image: url('https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Images/Package/<?php echo $packages[1]["nID"]; ?>.jpg'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
								<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
					<div id="p<?php echo strval($packages[2]['nID']); ?>" class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="<?php echo htmlentities($packages[2]['sTitle']); ?>" placeholder="Package name">
							<button onclick="UploadTitle(this, <?php echo $packages[2]["nID"]; ?>);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(<?php echo strval($packages[2]['nID']); ?>);"><i class="fa fa-edit"></i></button>

						<button name="moveButton" pid="<?php echo strval($packages[2]['nID']); ?>" style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this, <?php echo strval($packages[2]['nID']); ?>);"><i class="fa fa-arrow-right"></i></button>

						<div ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this, <?php echo $packages[2]["nID"]; ?>);" style="margin-top: 0px; background-color: #eee; border: 1px solid #aaa; background-image: url('https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Images/Package/<?php echo $packages[2]["nID"]; ?>.jpg'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
								<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
				</div>

				<!-- bottom -->
				<div class="row" style="height: 220px;">
					<div id="p<?php echo strval($packages[3]['nID']); ?>" class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="<?php echo htmlentities($packages[3]['sTitle']); ?>" placeholder="Package name">
							<button onclick="UploadTitle(this, <?php echo $packages[3]["nID"]; ?>);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(<?php echo strval($packages[3]['nID']); ?>);"><i class="fa fa-edit"></i></button>

						<button name="moveButton" pid="<?php echo strval($packages[3]['nID']); ?>" style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this, <?php echo strval($packages[3]['nID']); ?>);"><i class="fa fa-arrow-right"></i></button>

						<div ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this, <?php echo $packages[3]["nID"]; ?>);" style="margin-top: 0px; background-color: #eee; border: 1px solid #aaa; background-image: url('https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Images/Package/<?php echo $packages[3]["nID"]; ?>.jpg'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
								<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
					<div id="p<?php echo strval($packages[4]['nID']); ?>" class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="<?php echo htmlentities($packages[4]['sTitle']); ?>" placeholder="Package name">
							<button onclick="UploadTitle(this, <?php echo $packages[4]["nID"]; ?>);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(<?php echo strval($packages[4]['nID']); ?>);"><i class="fa fa-edit"></i></button>

						<button name="moveButton" pid="<?php echo strval($packages[4]['nID']); ?>" style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this, <?php echo strval($packages[4]['nID']); ?>);"><i class="fa fa-arrow-right"></i></button>

						<div ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this, <?php echo $packages[4]["nID"]; ?>);" style="margin-top: 0px; background-color: #eee; border: 1px solid #aaa; background-image: url('https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Images/Package/<?php echo $packages[4]["nID"]; ?>.jpg'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
								<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
					<div id="p<?php echo strval($packages[5]['nID']); ?>" class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="<?php echo htmlentities($packages[5]['sTitle']); ?>" placeholder="Package name">
							<button onclick="UploadTitle(this, <?php echo $packages[5]["nID"]; ?>);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(<?php echo strval($packages[5]['nID']); ?>);"><i class="fa fa-edit"></i></button>

						<button name="moveButton" pid="<?php echo strval($packages[5]['nID']); ?>" style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this, <?php echo strval($packages[5]['nID']); ?>);"><i class="fa fa-arrow-right"></i></button>

						<div ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this, <?php echo $packages[5]["nID"]; ?>);" style="margin-top: 0px; background-color: #eee; border: 1px solid #aaa; background-image: url('https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Images/Package/<?php echo $packages[5]["nID"]; ?>.jpg'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
								<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
				</div>

			</div>
		</div>
		<button style="margin: auto; display: block; width:300px; height: 75px; background-color: #33a; color: #fff; opacity: 0.75; border-radius: 13px; border: none;">
			Background Image
			<br>
			<input type="file" accept="image/*" class="file-upload" onchange="BackgroundUpload(this, <?php echo $zones[$z]['nID']; ?>)" style="width: 200px; height: 50px; cursor: pointer;" />
		</button>
	</div>
	<!-- end of zone -->

	<?php
		if(intval($z) == count($zones) - 1)
		{
	?>
	</div>
	<?php
		}
	}
	?>

	<div id="clone_zone" data-activated="0" class="col" style="display: none; height: 600px; border: solid #eee 1px; background-image: url(''); background-repeat: no-repeat; background-size: cover;">
		<div class="row" style="margin: auto; width: 700px; height: 550px;">
			<div class="col">
				<div class="row" style="height: 80px;">
					<div class="col">
						<input onkeydown="UpdateZoneTitle(this);" type="text" style="height: 30px;width: 640px;" value="" placeholder="Title">
						<button name="uploadTitle" onclick="UploadZoneTitle(this);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
					</div>
					<div class="col">
						<input onkeydown="UpdateZoneDescription(this);" type="text" style="height: 30px;width: 640px;" value="" placeholder="Description">
						<button name="uploadDescription" onclick="UploadZoneDescription(this);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
					</div>
					<img name="removeZone" onclick="RemoveZone(this);" style="position: absolute; cursor: pointer; margin-left: -30px; height: 25px;width: 25px; display: block;border-radius: 50px;" src="../assets/img/remove.png">
					<button name="activate" style="width:29px; height: 25px; position: absolute; cursor: pointer; margin-left: -32px; margin-top: 40px; background-color: #faa; border-radius: 13px; border: none;" onclick="ToggleActive(this);"><i class="fa fa-power-off"></i></button>
				</div>

				<!-- 6 packages -->

				<!-- top -->
				<div class="row" style="height: 180px;">
					<div class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="" placeholder="Package name">
							<button name="t0" onclick="UploadTitle(this);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button name="e0" style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(this);"><i class="fa fa-edit"></i></button>

						<button style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this);"><i class="fa fa-arrow-right"></i></button>

						<div name="i0" ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this);" style="margin-top: 0px; background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
					<div class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="" placeholder="Package name">
							<button name="t1" onclick="UploadTitle(this);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button name="e1" style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(this);"><i class="fa fa-edit"></i></button>

						<button style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this);"><i class="fa fa-arrow-right"></i></button>

						<div name="i1" ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this);" style="margin-top: 0px; background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
					<div class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="" placeholder="Package name">
							<button name="t2" onclick="UploadTitle(this);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button name="e2" style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(this);"><i class="fa fa-edit"></i></button>

						<button style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this);"><i class="fa fa-arrow-right"></i></button>

						<div name="i2" ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this);" style="margin-top: 0px; background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
				</div>

				<!-- bottom -->
				<div class="row" style="height: 220px;">
					<div class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="" placeholder="Package name">
							<button name="t3" onclick="UploadTitle(this);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button name="e3" style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(this);"><i class="fa fa-edit"></i></button>

						<button style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this);"><i class="fa fa-arrow-right"></i></button>

						<div name="i3" ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this);" style="margin-top: 0px; background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
					<div class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="" placeholder="Package name">
							<button name="t4" onclick="UploadTitle(this);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button name="e4" style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(this);"><i class="fa fa-edit"></i></button>

						<button style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this);"><i class="fa fa-arrow-right"></i></button>

						<div name="i4" ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this);" style="margin-top: 0px; background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
					<div class="col" style="width: 120px;">
						<div class="col" style="height: 40px;">
							<input onkeydown="UpdateTitle(this);" type="text" style="height: 30px;max-width: 140px;" value="" placeholder="Package name">
							<button name="t5" onclick="UploadTitle(this);" style="height: 35px; background-color: #afa; border-radius: 10px; border: none;"><i class="fa fa-upload"></i></button>
						</div>
						<img style="height: 100px;width: 100px;margin: auto;display: none;border-radius: 10px;" src="../assets/img/avatars/avatar2.jpeg">
						<button name="e5" style="width:30px; height: 25px; background-color: #aaf; border-radius: 13px; border: none; font-size: 16px;" onclick="EnterPackage(this);"><i class="fa fa-edit"></i></button>
						
						<button style="width:30px; height: 25px; background-color: #aaa; border-radius: 13px; border: none; font-size: 16px;" onclick="MovePackage(this);"><i class="fa fa-arrow-right"></i></button>

						<div name="i5" ondragenter="ImageDragEnter(this);" ondragover="return false;" ondragexit="ImageDragLeave(this);" ondrop="ImageDrop(this);" onchange="ImageUpload(this);" style="margin-top: 0px; background-color: #eee; background-image: url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png'); margin-left: 38px; width: 100px; border-radius: 10px; background-size: cover;">
							<input type="file" accept="image/*" class="file-upload" style="opacity: 0; width: 100px; height: 100px; cursor: pointer;" />
						</div>
					</div>
				</div>
			</div>
			<button style="margin: auto; display: block; width:300px; height: 75px; background-color: #33a; color: #fff; opacity: 0.75; border-radius: 13px; border: none;">
			Background Image
			<br>
			<input name="bi" type="file" accept="image/*" class="file-upload" onchange="BackgroundUpload(this)" style="width: 200px; height: 50px; cursor: pointer;" />
		</button>
		</div>
	</div>
	<!-- end of zone -->

	<div class="row" style="border-bottom: solid #eee 1px; border-top: solid #eee 1px;">
		<div class="col" style="padding: 25px;"><img onclick="AddRow();" style="cursor:pointer; border-radius: 50px;width: 100px;margin: auto;display: block;" src="../assets/img/add_2.png"></div>
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

	<script type="text/javascript">
		jQuery.fn.SwapWith = function(to) {
			return this.each(function() {
				var copy_to = $(to).clone(true);
				var copy_from = $(this).clone(true);
				$(to).replaceWith(copy_from);
				$(this).replaceWith(copy_to);
			});
		};
	</script>
</body>
</html>
