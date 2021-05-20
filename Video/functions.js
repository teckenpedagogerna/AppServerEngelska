var colorTarget = '';

function Search(target)
{
	if(target.value == "")
	{
		if(!$("#noVideo").is(":checked"))
		{
			$('#table').find('tr').each(function () {
				$(this).css('display', '');
			});
		}
		else
		{
			var skipFirst = true;
			$('#table').find('tr').each(function () {
				if(skipFirst)
				{
					$(this).css('display', '');
					skipFirst = false;
				}
				else
				{
					if($(this).find('th').eq(5).find('select')[0].length == 1 || $(this).find('th').eq(6).find('select')[0].length == 1)
					{
						$(this).css('display', '');
					}
					else
					{
						$(this).css('display', 'none');
					}
				}
			});
		}
	}
	else
	{
		var skipFirst = true;
		$('#table').find('tr').each(function () {
			if(skipFirst)
			{
				$(this).css('display', '');
				skipFirst = false;
			}
			else
			{
				if(!$("#noVideo").is(":checked"))
				{
					if($(this).find('th').eq(1)[0].innerHTML.toLowerCase().indexOf(target.value.toLowerCase()) != -1
					|| $(this).find('th').eq(2)[0].innerHTML.toLowerCase().indexOf(target.value.toLowerCase()) != -1)
						$(this).css('display', '');
					else
						$(this).css('display', 'none');
				}
				else
				{
					if($(this).find('th').eq(5).find('select')[0].length > 1)
						console.log($(this).find('th').eq(5).find('select')[0].length);

					if(
						($(this).find('th').eq(1)[0].innerHTML.toLowerCase().indexOf(target.value.toLowerCase()) != -1
					|| $(this).find('th').eq(2)[0].innerHTML.toLowerCase().indexOf(target.value.toLowerCase()) != -1)
						&& ($(this).find('th').eq(5).find('select')[0].length == 1 
							|| 
							$(this).find('th').eq(6).find('select')[0].length == 1)

					)
						$(this).css('display', '');
					else
						$(this).css('display', 'none');
				}
			}
		});
	}
}

function SearchRefresh()
{
	Search($("#search_input")[0]);
}

function ReUpdate()
{
	$("button").prop("disabled",true);
	$("select").prop("disabled",true);

	$.post(
		"ReUpdateLink.php",
		{}
	).done(function (message)
	{

		alert('Nu är jag redo!');
		location.reload();

	});
}



function UpdateTranslate(target)
{
	$.post(
		"updateTranslatation.php",
		{
			wordID: $('#modal_show_video').attr('data-wordID'),
			alt: $('#modal_show_video').attr('data-alt'),
			signLanguage: $('#modal_show_video').attr('data-signLanguage'),
			translation: $("#translation_video")[0].value
		}
	).done(function (message)
	{
		$("#translation_video").parent().find("button").css("background-color", "#33f");
	});
}

function ChangeTranslate(_target)
{	
	$(_target).parent().find("button[name='transition']").css("background-color", "#f00");
}

function Whereis(data)
{
	$.post(
		"whereis.php",
		{
			data: data
		}
	).done(function (message)
	{
		$('#modal_title').html('<b><i>Hitta övningar med ordet</i></b>');
		$('#modal_content').html(message);
		$('#modal_close').css('display', 'none');
		$('#modal_ok').html("Tillbaka");
		$('#modal_ok').attr("onclick", "DoRemoveZone(" + data + ");");

		$('#modal').modal();
	});
}

function UpdatePreview()
{
	var alt = $('#modal_show_video').attr('data-alt');
	var signLanguage = $('#modal_show_video').attr('data-signLanguage');
	var wordID = $('#modal_show_video').attr('data-wordID');

	$("#modal_show_load").css('display', 'block');
	$("#modal_show_preview_img").css('display', 'none');

	var depth = $("#modal_show_depth").val().replace(',', '.').replace(/[^\d.-]/g,'');
	var blend = $("#modal_show_blend").val().replace(',', '.').replace(/[^\d.-]/g,'');

	$("#modal_show_depth").val(depth);
	$("#modal_show_blend").val(blend);

	$.post(
		"preview.php",
		{
			video: wordID + "-" + alt + "-" + signLanguage,
			blend: blend,
			depth: depth
		}
	).done(function (respone)
	{
		console.log(respone);
		$("#modal_show_load").css('display', 'none');
		$("#modal_show_preview_img").css('display', 'block');
		$("#modal_show_preview_img")[0].src = "https://appserver-admin.teckenpedagogerna.se/data/p" + wordID + "-" + alt + "-" + signLanguage + ".jpg?" + Date.now();
	});
}

function RenderVideo()
{
	var alt = $('#modal_show_video').attr('data-alt');
	var signLanguage = $('#modal_show_video').attr('data-signLanguage');
	var wordID = $('#modal_show_video').attr('data-wordID');
	$('#modal_show_video').modal('hide');

	var depth = $("#modal_show_depth").val().replace(',', '.').replace(/[^\d.-]/g,'');
	var blend = $("#modal_show_blend").val().replace(',', '.').replace(/[^\d.-]/g,'');

	$.post(
		"render.php",
		{
			video: wordID + "-" + alt + "-" + signLanguage,
			id: wordID,
			alt: alt,
			signLanguage: signLanguage,
			depth: depth,
			blend: blend,
			colorTarget: colorTarget
		}
	).done(function (respone)
	{
		//setTimeout(function(){ ShowVideo(word, wordID, signLanguage); }, 1);
	});
}

function ReplaceVideo()
{
	var alt = $('#modal_show_video').attr('data-alt');
	var signLanguage = $('#modal_show_video').attr('data-signLanguage');
	var wordID = $('#modal_show_video').attr('data-wordID');

	$("#videofile").val('');

	$('#modal_show_video').modal('hide');
	setTimeout(function(){ UploadVideo('Byta ut videon', wordID, signLanguage, alt); }, 500);
}

function ShowVideo(word, wordID, signLanguage)
{
	var alt = $("#videos_" + signLanguage + "_" + wordID)[0].value;

	if(alt == "none")
		return;

	$('#modal_show_video').attr('data-alt', alt);
	$('#modal_show_video').attr('data-signLanguage', signLanguage);
	$('#modal_show_video').attr('data-wordID', wordID);

	$("#modal_show_video_preview_video").parent().css('display', 'none');
	$("#modal_show_preview_img").css('display', 'none');
	$("#modal_show_depth").css('display', 'none');
	$("#modal_show_blend").css('display', 'none');
	$("#modal_show_input0").css('display', 'none');
	$("#modal_show_input1").css('display', 'none');
	$("#modal_show_load").css('display', 'none');
	$("#modal_show_preview").css('display', 'none');
	$("#modal_show_video_render").css('display', 'none');
	$("#modal_show_brs").css('display', 'none');
	$("#modal_show_video_replace").css('display', 'none');

	$.post(
		"videoStatus.php",
		{
			id: wordID,
			alt: alt,
			signLanguage: signLanguage,
			isWord: '1'
		}
	).done(function (rData)
	{
		$("select").prop("disabled",false);
		$("button").prop("disabled",false);
		var data = rData.split(",");

		if(data[0] == '0')
		{
			$("#modal_show_preview_img").css('display', 'none');
			$("#modal_show_depth").css('display', 'block');
			$("#modal_show_blend").css('display', 'block');
			$("#modal_show_input0").css('display', 'block');
			$("#modal_show_input1").css('display', 'block');
			$("#modal_show_load").css('display', 'block');
			$("#modal_show_preview").css('display', 'block');
			$("#modal_show_video_render").css('display', 'block');
			$("#modal_show_brs").css('display', 'block');

			var depth = data[1].split(":")[0].replace(',', '.').replace(/[^\d.-]/g,'');
			var blend = data[1].split(":")[1].replace(',', '.').replace(/[^\d.-]/g,'');

			$("#modal_show_depth").val(depth);
			$("#modal_show_blend").val(blend);

			$('#modal_show_video_ok').css('display', 'block');
			$('#modal_show_video_ok').html("Ta bort");
			$('#modal_show_video_ok').attr("onclick", "DoDeleteVideo(" + wordID + ", " + signLanguage + ", " + alt + ");");

			$('#modal_show_video_title').html('Justera mask');
			$('#modal_show_video_close').html("Tillbaka");

			setTimeout(function(){ $('#modal_show_video').modal(); UpdatePreview(); }, 500);
		}
		else if(data[0] == '1')
		{
			$("#modal_show_video_preview_video").parent().css('display', 'none');
			$('#modal_show_video_title').html('<b><i>' + word + '</i></b> [ ' + alt + ' ]' + ' <b style="color:#f00;">Videon renderar fortfarande i bakgrunden</b>');
			$('#modal_show_video_close').html("Tillbaka");
			$('#modal_show_video_ok').css('display', 'none');
			$('#modal_show_video').modal();
		}
		else if(data[0] == '2')
		{
			$("#modal_show_video_preview_video").parent().css('display', 'block');
			$("#modal_show_video_preview_video")[0].src = "https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Videos/" + wordID + "-" + alt + "-" + signLanguage + ".mp4?" + Date.now();
			$("#modal_show_video_preview_video").parent()[0].load();

			$('#modal_show_video_title').html('<b><i>' + word + '</i></b> [ ' + alt + ' ]');
			$('#modal_show_video_close').html("Tillbaka");
			$('#modal_show_video_ok').css('display', 'block');
			$('#modal_show_video_ok').html("Ta bort");
			$('#modal_show_video_ok').attr("onclick", "DoDeleteVideo(" + wordID + ", " + signLanguage + ", " + alt + ");");
			$("#modal_show_video_replace").css('display', 'block');
			$('#modal_show_video').modal();
		}

		// === POST ===
		$.post(
			"getTranslation.php",
			{
				wordID: $('#modal_show_video').attr('data-wordID'),
				alt: $('#modal_show_video').attr('data-alt'),
				signLanguage: $('#modal_show_video').attr('data-signLanguage')
			}
		).done(function (translation)
		{
			$("#translation_video")[0].value = translation;
		});
	});
}

function DoDeleteVideo(wordID, signLanguage, alt)
{
	$.post(
		"deleteVideo.php",
		{
			wordID: wordID,
			signLanguage: signLanguage,
			alt: alt
		}
	).done(function (message)
	{
		$("#videos_" + signLanguage + "_" + wordID).find('option[value="' + alt + '"').remove();
		$('#modal_show_video').modal('hide');
	});
}

var canUpload = false;

function UploadVideo(word, wordID, signLanguage, alt = -1)
{
	$("button").prop("disabled",true);
	$("select").prop("disabled",true);

	$.post(
		"freeSize.php"
	).done(function (freeSize)
	{
		$("button").prop("disabled",false);
		$("select").prop("disabled",false);


		$("#uploadProgress").css('width', 0);
		$("#uploadProgressNumber").html('0%');
		$("#modal_upload_preview_video")[0].src = "";
		$("#modal_upload_preview_video").parent()[0].load();
		$("#modal_upload_preview_video").parent().css("display", "none");

		var disk = freeSize.split('_')[0];
		var ram = freeSize.split('_')[1];

		canUpload = ram.indexOf('-') == '-1';

		$('#modal_upload_title').html('<b><i>Ladda upp video till ' + word + '</i></b> <br/> <p style="font-size:12px; color:#111;"><b style="color:#00f;">' + disk + '</b> ledigt utrymme hos videoserver. <b>' + ram + '</b> ledig ram minne.</p>');
		$('#modal_upload_close').html("Tillbaka");
		$('#modal_upload_close').css('display', 'block');
		$('#modal_upload_ok').html("Ladda upp");
		$('#modal_upload_ok').prop('disabled', true);
		$('#modal_upload_ok').attr("onclick", "DoUploadVideo(" + wordID + ", " + signLanguage + ", " + alt + ");");
		$("#videofile").val('');
		$("#videofile").trigger("click");

		$('#modal_upload').modal();
	});
}

function DoUploadVideo(wordID, signLanguage, alt)
{
	$("button").prop("disabled",true);
	$("select").prop("disabled",true);
	
	// Check session
	$.post(
		"../session.php"
	).done(function (message)
	{
		if(message == '0') // session out, need to relogin
			location.reload();
		else
		{
			// start upload
			$('#videofile').prop('disabled', true);
			$('#modal_upload_ok').prop('disabled', true);
			if($("#videofile")[0].files.length > 0)
			{
				var formdata = new FormData();
				formdata.append("video", $("#videofile")[0].files[0]);
				formdata.append("wordID", wordID);
				formdata.append("signLanguage", signLanguage);
				formdata.append("alt", alt);

				$.ajax({
					url: "upload_video.php",
					type: "POST",
					data: formdata,
					processData: false,
					contentType: false,
					xhr: function()
					{
						var xhr = new window.XMLHttpRequest();
						xhr.upload.addEventListener("progress", function(evt) 
						{
							if (evt.lengthComputable) 
							{
								var p = parseInt((evt.loaded / evt.total) * 100);

								$("#uploadProgress").css('width', p + '%');
								$("#uploadProgressNumber").html(p + '%');
							}
						}, false);
						return xhr;
					},
					success: function (result)
					{
						colorTarget = result.split('-')[0];
						var alt 	= result.split('-')[1];

						$('#videofile').prop('disabled', false);
						$("#uploadProgressNumber").html('Klart! Din video bearbetar i bakgrunden.<br>Det kan ta upp 3 minuter att bli klara. ');
						$("#videos_" + signLanguage + "_" + wordID).append("<option value=" + alt + " selected>Alternative " + alt + "</option>");
						// Enter the function
						$('#modal_upload').modal('hide');
						setTimeout(function(){ ShowVideo('', wordID, signLanguage); }, 500);
					},
					error: function (result)
					{
						console.error(result);
						alert('Server error! Please check the Console log (F12) or take contact with the developer.');
						$('#videofile').prop('disabled', false);
					}
				});
			}
			else
			{
				alert("Vänligen välja en video!");
			}
		}
	});
}

function VideoUploadPreview(target)
{
	$("#modal_upload_preview_video").parent().css("display", "block");
	$("#modal_upload_preview_video")[0].src = URL.createObjectURL(target.files[0]);
	$("#modal_upload_preview_video").parent()[0].load();

	if(canUpload)
		$("#modal_upload_ok").prop('disabled', false);
	else
		$("#modal_upload_ok").prop('disabled', true);
}