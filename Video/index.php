<?php

header("Location: " . "https://" . $_SERVER['HTTP_HOST'] . '/AppServer/Engelska/Video/index2.php');
exit();

include_once $_SERVER['LIBRARY'] . '/Engelska/session.php';
include_once __DIR__ . '/UpdateWords.php';
include_once __DIR__ . '/ReLinkTranslations.php';

if(!is_numeric($_GET['lID']))
    exit();

$words = $connect->query("SELECT * FROM Words WHERE nLanguageID = :languageID ORDER BY sWord",
[
    "languageID" => $_GET['lID']
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="../assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="functions.js?<?php echo $date; ?>"></script>
</head>
<body>
	<div class="card">
        <div class="card-body">
            <a href="https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/">Gå tillbaka</a>
            <br>
            <br>
            <h4 class="card-title">Ordlista, videor och översättningar</h4>
            <h6 class="text-muted card-subtitle mb-2">Ord och video</h6>
        </div>
    </div>
	<div class="card" style="margin: 24px;">
		<input onkeyup="Search(this);" type="text" class="form-control" placeholder="Sök" aria-describedby="basic-addon1">
	</div>
    <div class="card">
    	<table id="table" class="table table-bordered">
    		<!-- HEADER -->
    		<tr style="font-size:24px;">
    			<th>#</th>
    			<th>Ord</th>
    			<th>Antal</th>
    			<th>Finns på</th>
    			<th>Videor (STS)</th>
    			<th>Videor (ASL)</th>
    		</tr>
    		<!-- BODY -->
<?php 
for ($i = 0; $i < count($words); $i++)
{
?>
            <tr>
    			<th><?php echo (1 + $i); ?></th>
    			<th><?php echo ucfirst($words[$i]['sWord']); ?></th>
    			<th><?php echo $words[$i]['nUsage']; ?></th>
    			<th><button class="btn btn-primary" style="width: 100%;" onclick="Whereis('<?php echo $words[$i]['sFoundAt']; ?>');">Visa</button></th>
    			<th>
    				<select id="videos_1_<?php echo $words[$i]['nID']; ?>">
                        <option value="none">None</option>
<?php

$swe_videos = $connect->query("SELECT * FROM WordVideos WHERE nWordID = :id AND nSignLanguage = 1 ORDER BY nAltID",
[
    'id' => $words[$i]['nID']
]);

for ($v = 0; $v < count($swe_videos); $v++)
{
?>
						<option value="<?php echo (string)$swe_videos[$v]['nAltID'] ?>">Alternative <?php echo (string)$swe_videos[$v]['nAltID'] ?></option>
<?php
}
?>
					</select>
					<button class="btn btn-primary" onclick="UploadVideo(<?php echo "'" . htmlspecialchars(ucfirst($words[$i]['sWord'])) . '\', ' . $words[$i]['nID']; ?>, 1);">Ladda upp</button>
					<button class="btn btn-secondary" onclick="ShowVideo(<?php echo "'" . htmlspecialchars(ucfirst($words[$i]['sWord'])) . '\', ' . $words[$i]['nID']; ?>, 1)">Visa</button>
    			</th>
    			<th>
    				<select id="videos_2_<?php echo $words[$i]['nID']; ?>">
                        <option value="none">None</option>
<?php

$us_videos = $connect->query("SELECT * FROM WordVideos WHERE nWordID = :id AND nSignLanguage = 2 ORDER BY nAltID",
[
    'id' => $words[$i]['nID']
]);

for ($v = 0; $v < count($us_videos); $v++)
{
?>
                        <option value="<?php echo (string)$us_videos[$v]['nAltID'] ?>">Alternative <?php echo (string)$us_videos[$v]['nAltID'] ?></option>
<?php
}
?>
					</select>
					<button class="btn btn-primary" onclick="UploadVideo(<?php echo "'" . htmlspecialchars(ucfirst($words[$i]['sWord'])) . '\', ' . $words[$i]['nID']; ?>, 2);">Ladda upp</button>
                    <button class="btn btn-secondary" onclick="ShowVideo(<?php echo "'" . htmlspecialchars(ucfirst($words[$i]['sWord'])) . '\', ' . $words[$i]['nID']; ?>, 2)">Visa</button>
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
                    <video style="width: 100%;" controls loop autoplay>
                        <source id="modal_show_video_preview_video" src="">
                    </video>
                    <input onchange="ChangeTranslate(this);" id="translation_video" type="text" class="form-control" style="width:70%; float: left;" placeholder="Alternativ" aria-describedby="basic-addon1" value="">
                    <button class="btn btn-primary" style="width: 28%; float: right;" onclick="UpdateTranslate();">Uppdatera</button>
                </div>
                <div class="modal-footer">
                    <button id="modal_show_video_close" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="modal_show_video_ok" type="button" class="btn btn-danger">Delete</button>
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