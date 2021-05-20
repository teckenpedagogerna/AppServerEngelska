var edit_id = "";
var data = 
{
	stepIndex: -1,
	content:"",
	dataType:""
};
var stepIndexID = -1;

function MoveUp(button)
{
	data.stepIndex = $(button).parent().parent().parent().attr('stepIndex');

	var newStepIndex = parseInt(data.stepIndex) - 1;

	for(var i = 0; i < 1000; i++)
	{
		if($("div[stepIndex='" + newStepIndex.toString() + "']").length >= 1)
			break;
		if(i > 998)
			return;
		newStepIndex--;	
	}

	MoveStepIndex(newStepIndex);
}

function MoveDown(button)
{
	data.stepIndex = $(button).parent().parent().parent().attr('stepIndex');

	var newStepIndex = parseInt(data.stepIndex) + 1;

	for(var i = 0; i < 1000; i++)
	{
		if($("div[stepIndex='" + newStepIndex.toString() + "']").length >= 1)
			break;
		if(i > 998)
			return;
		newStepIndex++;
	}
	
	MoveStepIndex(newStepIndex);
}

function MoveStepIndex(newStepIndex)
{
	$.post("move.php",
	{
		packageID: packageID,
		stepIndex: data.stepIndex,
		newStepIndex: newStepIndex
	}).done(function(moveData)
	{
		var oldData = $("div[stepIndex='" + data.stepIndex + "']").html();
		var newData = $("div[stepIndex='" + newStepIndex + "']").html();
		$("div[stepIndex='" + data.stepIndex + "']").html(newData);
		$("div[stepIndex='" + newStepIndex + "']").html(oldData);
	});
}

function TableAlterRow(button, alter)
{
	if(alter == 1)
	{
		var tempcell_header = '<th><input onchange="ChangeTextTable(this);" placeholder=". . ." type="text" class="table-header-input" /></th>';
		var tempcell = '<th><input onchange="ChangeTextTable(this);" placeholder=". . ." type="text" class="table-input" /></th>';
		var tempcell_builder = "";
		var table = $(button).parent().find('table')[0];
		var rows = $(table).find('tr');
		//var cells = null;

		if(rows.length == 0)
		{
			$(table).append('<tr class="table-header">' + tempcell_header + '</tr>');
		}
		else
		{
			var cells = $(rows[0]).find('th');

			for(var i = 0; i < cells.length; i++)
			{
				tempcell_builder = tempcell_builder + tempcell;
			}

			$(table).append('<tr>' + tempcell_builder + '</tr>');
		}
	}
	else
	{
		var table = $(button).parent().find('table')[0];
		var rows = $(table).find('tr');

		if(rows.length > 0)
			$(rows[rows.length - 1]).remove();			
	}

	var button0 = $(button).parent().find('button[name="title"]');
	$(button0).css('background-color', '#f00');
}

function TableAlterCell(button, alter)
{
	if(alter == 1)
	{
		var tempcell_header = '<th><input onchange="ChangeTextTable(this);" placeholder=". . ." type="text" class="table-header-input" /></th>';
		var tempcell = '<th><input onchange="ChangeTextTable(this);" placeholder=". . ." type="text" class="table-input" /></th>';
		var table = $(button).parent().find('table')[0];
		var rows = $(table).find('tr');

		if(rows.length > 0)
		{
			$(rows[0]).append(tempcell_header);
		}

		for(var i = 1; i < rows.length; i++)
		{
			$(rows[i]).append(tempcell);
		}
	}
	else
	{
		var table = $(button).parent().find('table')[0];
		var rows = $(table).find('tr');

		for(var i = 0; i < rows.length; i++)
		{
			var cells = $(rows[i]).find('th');
			if(cells.length > 1)
				$(cells[cells.length - 1]).remove();
		}
	}

	var button0 = $(button).parent().find('button[name="title"]');
	$(button0).css('background-color', '#f00');
}

function UpdatePreview(id)
{
	$("#modal_show_load").css('display', 'block');
	$("#modal_show_preview_img").css('display', 'none');

	var depth = $("#modal_show_depth").val().replace(',', '.').replace(/[^\d.-]/g,'');
	var blend = $("#modal_show_blend").val().replace(',', '.').replace(/[^\d.-]/g,'');

	$("#modal_show_depth").val(depth);
	$("#modal_show_blend").val(blend);

	$.post(
		"preview.php",
		{
			video: "explain_" + id,
			blend: blend,
			depth: depth
		}
	).done(function (respone)
	{
		console.log(respone);
		$("#modal_show_load").css('display', 'none');
		$("#modal_show_preview_img").css('display', 'block');
		$("#modal_show_preview_img")[0].src = "https://appserver-admin.teckenpedagogerna.se/data/pexplain_" + id + ".jpg?" + Date.now();
	});
}

function RenderVideo()
{
	var id = $('#modal_show_video').attr('data-id');

	$("#vupdate_" + id).css('display', 'none');
	$("#vrender_" + id).css({'display': ''});

	$('#modal_show_video').modal('hide');

	$.post(
		"render.php",
		{
			video: "explain_" + id
		}
	).done(function (respone)
	{
		//setTimeout(function(){ ShowVideo(word, wordID, signLanguage); }, 1);
	});
}

function ShowVideo(id)
{
	$('#modal_show_video').attr('data-id', id);

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

	$.post(
		"videoStatus.php",
		{
			id: id
		}
	).done(function (rData)
	{
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

			$('#modal_show_preview').attr("onclick", "UpdatePreview(" + id + ");");


			$('#modal_show_video_ok').css('display', 'block');
			$('#modal_show_video_ok').html("Ta bort");
			$('#modal_show_video_ok').attr("onclick", "DoDeleteVideo(" + id + ");");

			$('#modal_show_video_title').html('Justera mask');
			$('#modal_show_video_close').html("Tillbaka");

			setTimeout(function(){ $('#modal_show_video').modal(); UpdatePreview(id); }, 500);
		}
		else if(data[0] == '1')
		{
			$("#modal_show_video_preview_video").parent().css('display', 'none');
			$('#modal_show_video_title').html('<b style="color:#f00;">Videon renderar fortfarande i bakgrunden</b>');
			$('#modal_show_video_close').html("Tillbaka");
			$('#modal_show_video_ok').css('display', 'none');
			$('#modal_show_video').modal();
		}
		else if(data[0] == '2')
		{
			$("#modal_show_video_preview_video").parent().css('display', 'block');
			$("#modal_show_video_preview_video")[0].src = "https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Videos/explain_" + id + ".mp4?" + Date.now();
			$("#modal_show_video_preview_video").parent()[0].load();

			$('#modal_show_video_title').html('<b><i>' + id + '</i></b>');
			$('#modal_show_video_close').html("Tillbaka");
			$('#modal_show_video_ok').css('display', 'block');
			$('#modal_show_video_ok').html("Ta bort");
			$('#modal_show_video_ok').attr("onclick", "DoDeleteVideo(" + id + ");");
			$('#modal_show_video').modal();
		}
	});
}

function DoDeleteVideo(id)
{
	$.post(
		"deleteVideo.php",
		{
			id: id
		}
	).done(function (message)
	{
		//$("#videos_" + signLanguage + "_" + wordID).find('option[value="' + alt + '"').remove();
		$('#modal_show_video').modal('hide');
		$("#remove_" + id).css('display', '');
		$("#vupdate_" + id).css('display', 'none');
		$("#vupload_" + id).css('display', '');

		$("#vupload_" + id).children().prop('disabled', false);

		$("#vupload_" + id).find('div[name="progress"]').html('');
	});
}

function UpdateVideo(input)
{
	$(input).parent().parent().find('video').find('source')[0].src = URL.createObjectURL(input.files[0]);
	$(input).parent().parent().find('video')[0].load();
	$(input).parent().parent().find('video')[0].play();
}

function UpdateVideoTitle(button)
{
	var inputTitle = $(button).parent().find('input[type="text"]');
	var stepIndex = $(button).parent().parent().attr('stepIndex'); 

	data.content = inputTitle.val();
	data.dataType = "video";
	data.stepIndex = stepIndex;

	$.post("update_video_title.php",
	{
		packageID: packageID,
		stepIndex: data.stepIndex,
		data: JSON.stringify(data)
	}).done(function()
	{
		$(button).css('background-color', '#00a');
	});
}

function UpdateTable(button)
{
	var table = $(button).parent().find('table')[0];
	var rows = $(table).find('tr');

	if(rows.length == 0)
		return;

	var array_map = [];
	var w = $(rows[0]).find('th').length;
	var h = rows.length;

	for(var y = 0; y < rows.length; y++)
	{
		var cells = $(rows[y]).find('th');
		for(var x = 0; x < cells.length; x++)
			array_map[x + y * cells.length] = $(cells[x]).find('input').val();
	}

	var remap = [];

	for(var x = 0; x < w; x++)
	{
		remap[x] = { y : [] };

		for(var y = 0; y < h; y++)
		{
			remap[x].y[y] = array_map[x + y * w];
		}
	}

	// {"Items":[{"y":["Hello","World"]},{"y":["Hello 2","World 2"]}]}

	data.content = '{"Items":' + JSON.stringify(remap) + "}";
	data.dataType = 'table';
	data.stepIndex = $(button).parent().parent().attr('stepIndex');

	//console.log(JSON.parse(JSON.parse(JSON.stringify(data)).content));
	//return;

	$.post("update_table.php",
	{
		packageID: packageID,
		stepIndex: data.stepIndex,
		data: JSON.stringify(data)
	}).done(function()
	{
		$(button).css('background-color', '#00a');
	});
}

function UpdateText(button, dateType)
{
	var inputTitle = $(button).parent().find('input[type="text"]');

	if(inputTitle.length == 0)
		inputTitle = $(button).parent().find('textarea[type="textarea"]');

	var stepIndex = $(button).parent().parent().attr('stepIndex'); 

	data.content = inputTitle.val();
	data.dataType = dateType;
	data.stepIndex = stepIndex;

	$.post("update_title.php",
	{
		packageID: packageID,
		stepIndex: data.stepIndex,
		data: JSON.stringify(data)
	}).done(function()
	{
		$(button).css('background-color', '#00a');
	});
}

function ChangeText(input)
{
	var button = $(input).parent().find('button[name="title"]');
	$(button).css('background-color', '#f00');
}

function ChangeTextTable(input)
{
	var button = $(input).parent().parent().parent().parent().parent().find('button[name="title"]');
	
	console.log(button);

	$(button).css('background-color', '#f00');
}

function ChangeVideoTitle(input)
{
	var button = $(input).parent().find('button[name="title"]');
	$(button).css('background-color', '#f00');
}

function UploadVideo(button)
{
	var inputTitle = $(button).parent().parent().find('input[type="text"]');
	var inputVideo = $(button).parent().parent().find('input[type="file"]');
	var stepIndex = $(button).parent().parent().parent().attr('stepIndex'); 

	if(inputVideo[0].files.length == 0)
	{
		alert("Vänligen välja en video!");
		return;
	}

	data.content = inputTitle.val();
	data.dataType = "video";
	data.stepIndex = stepIndex;

	$(inputVideo).prop('disabled', true);
	$(button).prop('disabled', true);

	var formdata = new FormData();
	formdata.append("video", inputVideo[0].files[0]);
	formdata.append("stepIndex", stepIndex);
	formdata.append("packageID", packageID);
	formdata.append("data", JSON.stringify(data));

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
					//$("#uploadProgress").css('width', p + '%');
					if(p < 100)
						$(button).parent().find('div[name="progress"]').html(p + '%');
					else
						$(button).find('div[name="progress"]').html('Klart! Din video bearbetar i bakgrunden, det kan ta upp 3 minuter att bli klara.');
				}
			}, false);
			return xhr;
		},
		success: function (id)
		{
			$("#remove_" + id).css('display', 'none');
			$("#vupdate_" + id).css('display', '');
			$("#vupload_" + id).css('display', 'none');
			$(button).parent().find('div[name="progress"]').html('');

			setTimeout(function(){ ShowVideo(id); }, 500);

			//$(button).prop('disabled', false);
			//$(inputVideo).prop('disabled', false);
			//$(button).parent().html("Arbetar i bakgrund... [F5] för att uppdatera sidan.");

			//$(button).find('div[name="progress"]').html('Klart! Din video bearbetar i bakgrunden.<br>Det kan ta upp 3 minuter att bli klara.');
		},
		error: function (result)
		{
			console.error(result);
			alert('Server error! Please check the Console log (F12) or take contact with the developer.');
			$(button).prop('disabled', false);
			$(inputVideo).prop('disabled', false);
		}
	});
}

function NewData()
{
	$("#selectData").prop("disabled", false);

	var id = 1;

	$("#data_list").find("div[name='eData']").each(
		function()
		{
			var newID = parseInt($(this).attr("stepIndex"));

			if(newID >= id)
			{
				id = newID + 1;
			}
		}
	);

	data.content = "";
	data.dataType = $("#selectData").val();
	data.stepIndex = id;

	$.post("data/new.php",
	{
		zoneID: zoneID,
		packageID: packageID,
		data: JSON.stringify(data),
		stepIndex: data.stepIndex
	}).done(function(new_id)
	{
		//if(message == "INSERTED")
		$("#save_data").css("background-color", "#000");

		var clone = $("#clone_data").clone();
		clone.attr('stepIndex', data.stepIndex);
		clone.prop('id', false);

		switch(data.dataType)
		{
			case 'bigTitle': clone.find('p').html("Stor titel"); break;
			case 'title': clone.find('p').html("Titel"); break;
			case 'text': clone.find('p').html("Text"); break;
			case 'textHighlight': clone.find('p').html("Text med bakgrundsfärg"); break;
			case 'video': clone.find('p').html("Video"); break;
		}

		clone.find('div[name="' + data.dataType + '"]').css('display', 'block');
		clone.css('display', 'block');

		if(data.dataType == 'video')
		{
			videoClone = clone.find('div[name="video"]');

			videoClone.find('div[name="upload_div"]').attr('id', 'vupload_' + new_id);
			videoClone.find('div[name="update_div"]').attr('id', 'vupdate_' + new_id);
			videoClone.find('div[name="render_div"]').attr('id', 'vrender_' + new_id);

			videoClone.find('div[name="upload_div"]').css('display', '');
		}


		clone.appendTo("#data_list");
	});
}

function RemoveData(input)
{
	$('#modal_title').html('Ta bort?');
	$('#modal_content').html("Det här data kommer att tas bort.");
	$('#modal_close').html("Avbryta");
	$('#modal_ok').html("Ta bort");
	$('#modal_ok').attr("onclick", "DoRemoveData(" 
		+
			$(input).parent().parent().parent().attr('stepIndex')
		+ ");");

	$('#modal').modal();
}

function DoRemoveData(stepIndex)
{
	$('#modal_ok').prop("disabled", true);

	$.post(
		"remove.php",
		{
			packageID: packageID,
			stepIndex: stepIndex
		}
	).done(function (message)
	{
		$('#modal_ok').prop("disabled", false);
		
		switch(message)
		{
			case "DELETED":
				$('#modal').modal('hide');
				$('div[stepIndex="' + stepIndex + '"]').remove();
				break;
			default:
				$('#modal_ok').prop("disabled", false);
				alert('Server fel, vänligen kontakta webbutvecklare.');
				break;
		}
	});
}
