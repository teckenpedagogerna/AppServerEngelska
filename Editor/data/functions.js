var edit_id = "";
var data = 
{
	stepIndex: -1,
	question: "",
	content:"",
	answer:0,
	pictures:"",
	quizType:""
};
var li_target;
var country_code = "se";

var li = '<li onclick="SelectLessonList(this);" id="li_clone" class="list-group-item list-data-item"><span>{0}</span></li>';
var _ChangeVideoCountry;
var stepIndexID = -1;

document.addEventListener('DOMContentLoaded', function()
{
	CheckListDone();
}, false);

function Autocorrect(string)
{
	if(string == undefined)
		return '';

	var array = string.split('_');

	if(array.length > 0)
	{
		var buffer = "";
		for(var i = 0; i < array.length; i++)
		{
			buffer = buffer + "_" + fix_string(array[i]);
		}

		while(buffer.charAt(0) == '_')
		{
			buffer = buffer.substring(1);
		}

		return buffer;
	}

	return fix_string(string);
}

function ReportThis()
{
	$.post("../Report/localreport.php",
	{
		id: data.id
	}).done(function(response)
	{
		alert(response);
	});
}

function fix_string(string)
{
	if(string.length > 0)
	{
		while(string.charAt(0) == ' ')
		{
			string = string.substring(1);
		}

		while(string.charAt(string.length - 1) == ' ')
		{
			string = string.slice(0, -1);
		}

		while(string.indexOf("  ") != -1)
		{
			string = string.replace("  ", " ");
		}
	}

	return string;
}

function CheckListDone()
{
	$("#lesson_list").children().each(function(index)
	{
		SetListDone(this);
	});
}

function SetListDone(listItem)
{
	$.post("data/CheckList.php",
	{
		lessonID: lessonID,
		packageID: packageID,
		stepIndex: $(listItem).attr('id').split('_')[1]
	}).done(function(response)
	{
		if(response == 0)
		{
			$(listItem).css("background-color", "#faa");
		}
		else if(response == 1)
		{
			$(listItem).css("background-color", "");
		}
		else
		{
			console.log( 'Stepindex: ' + $(listItem).attr('id').split('_')[1] );
			console.log(response);
		}
	});
}

function SetListDoneSelected(listItem)
{
	$.post("data/CheckList.php",
	{
		lessonID: lessonID,
		packageID: packageID,
		stepIndex: $(listItem).attr('id').split('_')[1]
	}).done(function(response)
	{
		if(response == 0)
		{
			$(listItem).css("background-color", "#a00");
		}
		else if(response == 1)
		{
			$(listItem).css("background-color", "#666");
		}
		else
		{
			console.log( 'Stepindex: ' + $(listItem).attr('id').split('_')[1] );
			console.log(response);
		}
	});
}

function FUC(string)
{
	return string != undefined ? string.charAt(0).toUpperCase() + string.slice(1) : '';
}

function MoveUp()
{
	var newStepIndex = parseInt(data.stepIndex) - 1;

	for(var i = 0; i < 1000; i++)
	{
		if($("#data_" + newStepIndex.toString()).length >= 1)
			break;
		if(i > 998)
			return;
		newStepIndex--;	
	}

	MoveStepIndex(newStepIndex);
}

function MoveDown()
{
	var newStepIndex = parseInt(data.stepIndex) + 1;

	for(var i = 0; i < 1000; i++)
	{
		if($("#data_" + newStepIndex.toString()).length >= 1)
			break;
		if(i > 998)
			return;
		newStepIndex++;
	}
	
	MoveStepIndex(newStepIndex);
}

function MoveStepIndex(newStepIndex)
{
	$.post("data/move.php",
	{
		lessonID: lessonID,
		packageID: packageID,
		stepIndex: data.stepIndex,
		newStepIndex: newStepIndex
	}).done(function(moveData)
	{
		var oldData = $("#data_" + data.stepIndex).html();
		var newData = $("#data_" + newStepIndex).html();
		$("#data_" + data.stepIndex).html(newData);
		$("#data_" + newStepIndex).html(oldData);
		data.stepIndex = newStepIndex;
		SelectLessonID(newStepIndex);
	});
}

function SelectVideo(id, self)
{
	for (var i = 1; i <= 2; i++)
	{
		$("input[name='video_sl_" + i + "']").each(function() {
			if(parseInt($(this).attr("data-id")) != id)
			{
				$(this).prop("checked", false);
			}
		});
	}
}

function SelectVideoWord(index, inputID, language)
{
	var word = '';

	switch (inputID)
	{
		case 'SelectMatchingWord_title':
			word = $("#" + inputID)[0].value;
			if(data.answer != "")
				word = $("#" + inputID)[0].value.replace("{0}", $("#SelectMatchingWord_input_" + data.answer)[0].value);
			break;
		default:
			word = $("#" + inputID)[0].value;
			break;
	}

	$("#previewVideo").parent().css('display', 'none');
	$('#modal_video').attr("data-index", index);

	$.post("data/getVideo.php",
	{
		lessonID: lessonID,
		packageID: packageID,
		data: JSON.stringify(data),
		stepIndex: data.stepIndex,
		word: word.toLowerCase(),
		index: index,
		language: language
	}).done(function(videoData)
	{
		//console.log(videoData);
		var d = JSON.parse(videoData);

		var htmlSWE = "<b style=\"color:#000;\">SSL</b><br>";
		var htmlUSA = "<b style=\"color:#000;\">ASL</b><br>";

		var haveVideo = false;

		for(var v = 0; v < d.length; v++)
		{
			console.log(d[v]);

			if(!(d[v].video[1].length == 0 && d[v].video[2].length == 0))
			{
				haveVideo = true;
			}
			else
			{
				continue;
			}

			$('#modal_video').attr("data-id", d[v].id);
			$('#modal_video').attr("data-word", word.toLowerCase());
			$('#modal_video_title').html('<b>Videor till </b> <i>' + word + '</i>');
			
			for (var s = 1; s <= 2; s++)
			{
				if(s == 1)
				{
					htmlSWE = htmlSWE + "ID:" + d[v].id + "<br>";
				}
				else
				{
					htmlUSA = htmlUSA + "ID:" + d[v].id + "<br>";
				}

				var htmlData = "";

				for (var i = 0; i < d[v].video[s].length; i++)
				{
					var videoFile = d[v].id.toString() + '-' + d[v].video[s][i].split(':')[0] + '-' + s + '.mp4';

					console.log(videoFile);

					var checked = d[v].video[s][i].split(':')[1] == 1 ? 'checked' : '';

					htmlData = htmlData + '<div><input ' + checked + ' data-translation="'
					+ '' // d[v].video[s][i][1]
					+ '" data-id="' + d[v].id
					+ '" type="checkbox" name="video_sl_' + s 
					+ '" value="' + encodeURIComponent(d[v].video[s][i].split(':')[0])
					+ '" style="" onchange="SelectVideo(' + d[v].id + ', this);"> <button onclick="ShowVideo(\''
					+ videoFile +
					'\');" class="btn btn-primary btn-sm" style="">Alternativ ' + d[v].video[s][i].split(':')[0] + ( d[v].translation[s][i] == '' ? '' : ' ( ' + d[v].translation[s][i] + ' )' ) + '</button></div><br>';
				}

				if(s == 1)
				{
					htmlSWE = htmlSWE + htmlData;
				}
				else
				{
					htmlUSA = htmlUSA + htmlData;
				}

				//$("#alt_" + s).html(htmlData);
			}
		}

		if(!haveVideo)
		{
			alert("Det finns inga videor för det här ordet.");
			return;
		}

		$("#alt_1").html(htmlSWE);
		$("#alt_2").html(htmlUSA);


		$('#modal_video_close').html("Tillbaka");
		$('#modal_video_ok').html("Spara");
		$('#modal_video_ok').attr("onclick", "SaveVideo();");

		$('#modal_video').modal();
	});
}

function SelectVideoText(index, inputID, language)
{
	var text = '';

	switch (inputID)
	{
		case 'SelectMatchingWord_title':
			text = $("#" + inputID)[0].value;
			if(data.answer != "")
				text = $("#" + inputID)[0].value.replace("{0}", $("#SelectMatchingWord_input_" + data.answer)[0].value);
			break;
		case 'WordNotBelong_Text':
			{
				var tempData = $("#" + inputID)[0].value.split(" ");
				if(data.answer >= 0)
					tempData.splice(data.answer, 1);
				text = tempData[0];
				for(var i = 1; i < tempData.length; i++)
					text = text + " " + tempData[i];

			}
			break;
		default:
			text = $("#" + inputID)[0].value;
			break;
	}

	$("#previewVideo").parent().css('display', 'none');
	$('#modal_video').attr("data-index", index);

	$.post("data/getVideo_text.php",
	{
		lessonID: lessonID,
		packageID: packageID,
		data: JSON.stringify(data),
		stepIndex: data.stepIndex,
		text: text.toLowerCase(),
		index: index,
		language: language
	}).done(function(videoData)
	{
		//console.log(videoData);
		var d = JSON.parse(videoData);

		var htmlSWE = "<b style=\"color:#000;\">SSL</b><br>";
		var htmlUSA = "<b style=\"color:#000;\">ASL</b><br>";

		var haveVideo = false;

		for(var v = 0; v < d.length; v++)
		{
			console.log(d[v]);

			if(!(d[v].video[1].length == 0 && d[v].video[2].length == 0))
			{
				haveVideo = true;
			}
			else
			{
				continue;
			}

			$('#modal_video').attr("data-id", d[v].id);
			$('#modal_video').attr("data-text", text.toLowerCase());
			$('#modal_video_title').html('<b>Videor till </b> <i>' + text + '</i>');
			
			for (var s = 1; s <= 2; s++)
			{
				if(s == 1)
				{
					htmlSWE = htmlSWE + "ID:" + d[v].id + "<br>";
				}
				else
				{
					htmlUSA = htmlUSA + "ID:" + d[v].id + "<br>";
				}

				var htmlData = "";

				for (var i = 0; i < d[v].video[s].length; i++)
				{
					var videoFile = d[v].id.toString() + '-' + d[v].video[s][i].split(':')[0] + '-' + s + '_text.mp4';

					console.log(videoFile);

					var checked = d[v].video[s][i].split(':')[1] == 1 ? 'checked' : '';

					htmlData = htmlData + '<div><input ' + checked + ' data-translation="'
					+ '' // d[v].video[s][i][1]
					+ '" data-id="' + d[v].id
					+ '" type="checkbox" name="video_sl_' + s 
					+ '" value="' + encodeURIComponent(d[v].video[s][i].split(':')[0])
					+ '" style="" onchange="SelectVideo(' + d[v].id + ', this);"> <button onclick="ShowVideo(\''
					+ videoFile +
					'\');" class="btn btn-primary btn-sm" style="">Alternativ ' + d[v].video[s][i].split(':')[0] + ( d[v].translation[s][i] == '' ? '' : ' ( ' + d[v].translation[s][i] + ' )' ) + '</button></div><br>';
				}

				if(s == 1)
				{
					htmlSWE = htmlSWE + htmlData;
				}
				else
				{
					htmlUSA = htmlUSA + htmlData;
				}

				//$("#alt_" + s).html(htmlData);
			}
		}

		if(!haveVideo)
		{
			alert("Det finns inga videor för det här textet.");
			return;
		}

		$("#alt_1").html(htmlSWE);
		$("#alt_2").html(htmlUSA);


		$('#modal_video_close').html("Tillbaka");
		$('#modal_video_ok').html("Spara");
		$('#modal_video_ok').attr("onclick", "SaveVideoText();");

		$('#modal_video').modal();
	});
}

function ShowVideo(file)
{
	$("#previewVideo").parent().css('display', '');
	$("#previewVideo")[0].src = 'https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Videos/' + file;
	$("#previewVideo").parent()[0].load();
}

function SaveVideo()
{
	var id = -1;

	for (var i = 1; i <= 2; i++)
	{
		$("input[name='video_sl_" + i + "']").each(function() {
			if($(this).prop("checked"))
			{
				id = parseInt($(this).attr("data-id"));
			}
		});
	}	

	if(id == -1)
	{
		alert("Inga video valda!");
		return;
	}

	var videoData = {
		id: id,
		video: [[""], [], []]//,
		//word: $("#modal_video").attr('data-word')
	};

	for (var i = 1; i <= 2; i++)
	{
		$("input[name='video_sl_" + i + "']").each(function() {
			videoData.video[i].push(parseInt(this.value).toString() + ":" + (this.checked ? '1' : '0'));
		});
	}

	$.post("data/saveVideo.php",
	{
		id: id,
		lessonID: lessonID,
		packageID: packageID,
		data: JSON.stringify(videoData),
		stepIndex: data.stepIndex,
		index: $("#modal_video").attr('data-index')
	}).done(function(reply)
	{
		$('#modal_video').modal('hide');
	});
}

function SaveVideoText()
{
	var id = -1;

	for (var i = 1; i <= 2; i++)
	{
		$("input[name='video_sl_" + i + "']").each(function() {
			if($(this).prop("checked"))
			{
				id = parseInt($(this).attr("data-id"));
			}
		});
	}	

	if(id == -1)
	{
		alert("Inga video valda!");
		return;
	}

	var videoData = {
		id: id,
		video: [[""], [], []]//,
		//text: $("#modal_video").attr('data-text')
	};

	for (var i = 1; i <= 2; i++)
	{
		$("input[name='video_sl_" + i + "']").each(function() {
			videoData.video[i].push(parseInt(this.value).toString() + ":" + (this.checked ? '1' : '0'));
		});
	}

	$.post("data/saveVideo_text.php",
	{
		id: id,
		lessonID: lessonID,
		packageID: packageID,
		data: JSON.stringify(videoData),
		stepIndex: data.stepIndex,
		index: $("#modal_video").attr('data-index')
	}).done(function(reply)
	{
		$('#modal_video').modal('hide');
	});
}


function NewLesson()
{
	$("#selectLesson").prop("disabled", false);
	var id = 0;
	//data.stepIndex = $("#lesson_list").find("li").length;
	
	$("#lesson_list").append(li);
	
	$("#lesson_list").find("li").each(
		function()
		{
			var newID = parseInt($(this).attr("id").split('_')[1]);

			if(newID >= id)
			{
				id = newID + 1;
			}
		}
	);

	
	$("#li_clone").attr("id", "data_" + id);

	data.question = "";
	data.content = "";
	data.answer = 0;
	data.pictures = "";
	//data.videos = "";
	data.quizType = $("#selectLesson").val();
	data.stepIndex = id;

	edit_id = "#data_" + id;

	$.post("data/new.php",
	{
		lessonID: lessonID,
		packageID: packageID,
		data: JSON.stringify(data),
		stepIndex: data.stepIndex
	}).done(function(message)
	{
		if(message == "INSERTED")
			$("#save_lesson").css("background-color", "#000");
		SelectLessonID(data.stepIndex);
		li_target = $("#data_" + id)[0];
		SelectLesson();
	});
}

function DuplicateLesson()
{
	$("#selectLesson").prop("disabled", false);
	var id = 0;
	//var dupID = data.stepIndex;
	//data.stepIndex = $("#lesson_list").find("li").length;
	
	$("#lesson_list").append(li);
	
	$("#lesson_list").find("li").each(
		function()
		{
			var newID = parseInt($(this).attr("id").split('_')[1]);

			if(newID >= id)
			{
				id = newID + 1;
			}
		}
	);

	
	$("#li_clone").attr("id", "data_" + id);
	
	data.stepIndex = id;

	edit_id = "#data_" + id;

	$.post("data/duplicate.php",
	{
		lessonID: lessonID,
		packageID: packageID,
		data: JSON.stringify(data),
		stepIndex: data.stepIndex
	}).done(function(message)
	{
		if(message == "INSERTED")
			$("#save_lesson").css("background-color", "#000");
		SelectLessonID(data.stepIndex);
		li_target = $("#data_" + id)[0];
		SelectLesson();
	});
}

function RemoveData()
{
	$('#modal_title').html('Ta bort vald lektion?');
	$('#modal_content').html("Alla övningar under det här lektion kommer att tas bort.");
	$('#modal_close').html("Avbryta");
	$('#modal_ok').html("Ta bort");
	$('#modal_ok').attr("onclick", "DoRemoveData();");

	$('#modal').modal();
}

function DoRemoveData()
{
	$('#modal_ok').prop("disabled", true);

	$.post(
		"data/remove.php",
		{
			lessonID: lessonID,
			packageID: packageID,
			stepIndex: data.stepIndex
		}
	).done(function (message)
	{
		$('#modal_ok').prop("disabled", false);
		
		switch(message)
		{
			case "DELETED":
				$('#modal').modal('hide');
				$("#data_" + data.stepIndex).remove();

				data.question = "";
				data.content = "";
				data.answer = 0;
				data.pictures = "";
				//data.videos = "";
				data.quizType = "";
				data.stepIndex = -1;

				$("#selectLesson").prop("disabled", true);
				$("#save_lesson").prop("disabled", true);
				$("#delete_lesson").prop("disabled", true);

				$("#moveup_lesson").prop("disabled", true);
				$("#movedown_lesson").prop("disabled", true);

				for (var i = 0; i < allLessons.length; i++)
					$("#" + allLessons[i]).css("display", "none");

				break;
			default:
				$('#modal_ok').prop("disabled", false);
				alert('Server fel, vänligen kontakta webbutvecklare.');
				break;
		}
	});
}

function Data_Save()
{
	if(isNaN(data.question))
		data.question = Autocorrect(data.question);
	if(isNaN(data.content))
		data.content = Autocorrect(data.content);
	if(isNaN(data.answer))
		data.answer = Autocorrect(data.answer);

	$.post("data/save.php",
	{
		lessonID: lessonID,
		packageID: packageID,
		data: JSON.stringify(data),
		stepIndex: data.stepIndex
	}).done(function(message)
	{
		if(message == "UPDATED")
		{
			$("#save_lesson").css("background-color", "");
			
			SetListDoneSelected($("#data_" + data.stepIndex)[0]);
			//CheckListDone();
		}
	});
}

function SelectLessonList(_target)
{
	stepIndexID = $(_target).attr("id").split('_')[1];
	$.post("data/get.php",
	{
		lessonID: lessonID,
		packageID: packageID,
		stepIndex: stepIndexID
	}).done(function(message)
	{
		li_target = _target;
		data = JSON.parse(JSON.parse(message).data);
		data.id = JSON.parse(JSON.parse(message).id);
		if(data.videos != undefined)
			delete data.videos;

		$(_target).parent().children('li').each(
			function ()
			{
				if(this == _target)
				{
					SetListDoneSelected(this);
					//$(this).css("background-color", "#666");
					$(this).css("color", "#fff");
				}
				else
				{
					SetListDone(this);
					//$(this).css("background-color", "");
					$(this).css("color", "");
				}
			}
		);

		for (var i = 0; i < allLessons.length; i++)
			$("#" + allLessons[i]).css("display", "none");
		$("#" + data.quizType).css("display", "block");
		$("#selectLesson").val(data.quizType);
		$("#selectLesson").prop("disabled", false);
		$("#save_lesson").prop("disabled", false);
		$("#delete_lesson").prop("disabled", false);

		$("#moveup_lesson").prop("disabled", false);
		$("#movedown_lesson").prop("disabled", false);


		switch($("#selectLesson").val())
		{
			case "stepLock":
				$(li_target).children("span").text("Lås steg");
				break;
			case "messageAnswer":
				$(li_target).children("span").text("Meddelande svar");
				if(data.content == "" || data.content == undefined)
					data.content = "___";
				if(data.answer == "" || data.answer == undefined)
					data.answer = 0;
				MessageAnswer_Init();
				break;
			case "messageQuestion":
				if(data.content == "" || data.content == undefined)
					data.content = "___";
				if(data.answer == "" || data.answer == undefined)
					data.answer = 0;
				$(li_target).children("span").text("Meddelande fråga");
				MessageQuestion_Init();
				break;
			case "isTrueImage":
				$(li_target).children("span").text("Sant eller falskt");
				if(data.answer == "" || data.answer == undefined)
					data.answer = "1_";
				IsTrueImage_Init();
				break;
			case "matchWords":
				$(li_target).children("span").text("Matcha ord");
				MatchWords_Init();
				break;
			case "buildWord":
				$(li_target).children("span").text("Bygga upp ett ord");
				BuildWord_Init();
				break;
			case "buildText":
				$(li_target).children("span").text("Bygga upp en mening");
				BuildText_Init();
				break;
			case "dragDropIntoText":
				$(li_target).children("span").text("Drag & Släpp till text");
				DragDropIntoText_Init();
				break;
			case "selectWord":
				$(li_target).children("span").text("Välja ord");
				if(data.answer == "" || data.answer == undefined)
					data.answer = 0;
				SelectWord_Init();
				break;
			case "selectWordReverse":
				$(li_target).children("span").text("Omvänd välja ord");
				if(data.answer == "" || data.answer == undefined)
					data.answer = 0;
				SelectWordReverse_Init();
				break;
			case "selectPicture":
				$(li_target).children("span").text("Välja bild till ord");
				if(data.pictures == "" || data.pictures == undefined)
					data.pictures = ",,,";
				if(data.content == "" || data.content == undefined)
					data.content = "___";
				if(data.answer == "" || data.answer == undefined)
					data.answer = 0;
				SelectPicture_Init();
				break;
			case "selectMatchingWordToImage":
				$(li_target).children("span").text("Välja matchade ord till bild");
				if(data.answer == "" || data.answer == undefined)
					data.answer = 0;
				if(data.content == "" || data.content == undefined)
					data.content = "___";
				SelectMatchingWordToImage_Init();
				break;
			case "selectMatchingWordToImageReverse":
				$(li_target).children("span").text("Omvänd välja matchade ord till bild");
				if(data.answer == "" || data.answer == undefined)
					data.answer = 0;
				if(data.content == "" || data.content == undefined)
					data.content = "___";

				SelectMatchingWordToImageReverse_Init();
				break;
			case "selectPictureAndWord":
				$(li_target).children("span").text("Välja bild + text till ord");
				if(data.answer == "" || data.answer == undefined)
					data.answer = 0;
				if(data.content == "" || data.content == undefined)
					data.content = "___";
				SelectPictureAndWord_Init();
				break;
			case "selectMatchingWord":
				$(li_target).children("span").text("Välj ett matchade ord");
				if(data.content == "" || data.content == undefined)
					data.content = "___";
				if(data.answer == "" || data.answer == undefined)
					data.answer = 0;
				SelectMatchingWord_Init();
				break;
			case "wordNotBelong":
				$(li_target).children("span").text("Ordet inte tillhör");
				WordNotBelong_Init();
				break;
			case "pairedText":
				if(data.content == "" || data.content == undefined)
					data.content = "___";
				data.answer = 0;
				$(li_target).children("span").text("Parad text");
				PairedText_Init();
				break;
			case "textNotBelong":
				if(data.content == "" || data.content == undefined)
					data.content = "___";
				if(data.answer == "" || data.answer == undefined)
					data.answer = 0;
				$(li_target).children("span").text("Textet inte tillhör");
				TextNotBelong_Init();
				break;
			case "selectAnswerToImage":
				$(li_target).children("span").text("Välja ett svar till bild");
				if(data.answer == "" || data.answer == undefined)
					data.answer = 0;
				if(data.content == "" || data.content == undefined)
					data.content = "___";
				SelectAnswerToImage_Init();
				break;
		}
	});
}

function SelectLessonID(stepIndex)
{
	var _target = $('#data_' + stepIndex)[0];
	SelectLessonList(_target);
}

function ShowExampleImage(index, title)
{
	$('#modal_example_title').html('Exempelbild på <i><b>' + title + '</b></i>');
	$("#modal_example_image")[0].src = 'https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Editor/data/example/' + index + '.png';
	$('#modal_example').modal();
}

function SelectLesson()
{
	for (var i = 0; i < allLessons.length; i++)
		$("#" + allLessons[i]).css("display", "none");
	$("#" + $("#selectLesson").val()).css("display", "block");

	data.question = "";
	data.content = "";
	data.answer = "";
	data.pictures = "";
	//data.videos = "";
	data.quizType = $("#selectLesson").val();

	$("#save_lesson").prop("disabled", false);
	$("#delete_lesson").prop("disabled", false);
	$("#save_lesson").css("background-color", "#000");

	switch($("#selectLesson").val())
	{
		case "stepLock":
			$(li_target).children("span").text("Lås steg");
			break;
		case "messageAnswer":
			$(li_target).children("span").text("Meddelande svar");
			data.content = "___";
			data.answer = 0;
			MessageAnswer_Init();
			break;
		case "messageQuestion":
			data.content = "___";
			data.answer = 0;
			$(li_target).children("span").text("Meddelande fråga");
			MessageQuestion_Init();
			break;
		case "isTrueImage":
			$(li_target).children("span").text("Sant eller falskt");
			data.answer = "1_";
			IsTrueImage_Init();
			break;
		case "matchWords":
			$(li_target).children("span").text("Matcha ord");
			MatchWords_Init();
			break;
		case "buildWord":
			$(li_target).children("span").text("Bygga upp ett ord");
			BuildWord_Init();
			break;
		case "buildText":
			$(li_target).children("span").text("Bygga upp en mening");
			BuildText_Init();
			break;
		case "dragDropIntoText":
			$(li_target).children("span").text("Drag & Släpp till text");
			DragDropIntoText_Init();
			break;
		case "selectWord":
			$(li_target).children("span").text("Välja ord");
			data.answer = 0;
			SelectWord_Init();
			break;
		case "selectWordReverse":
			$(li_target).children("span").text("Omvänd välja ord");
			data.answer = 0;
			SelectWordReverse_Init();
			break;
		case "selectPicture":
			$(li_target).children("span").text("Välja bild till ord");
			data.pictures = ",,,";
			data.content = "___";
			data.answer = 0;
			SelectPicture_Init();
			break;
		case "selectMatchingWordToImage":
			$(li_target).children("span").text("Välja matchade ord till bild");
			data.answer = 0;
			data.content = "___";
			SelectMatchingWordToImage_Init();
			break;
		case "selectMatchingWordToImageReverse":
			$(li_target).children("span").text("Omvänd välja matchade ord till bild");
			data.answer = 0;
			data.content = "___";
			SelectMatchingWordToImageReverse_Init();
			break;
		case "selectPictureAndWord":
			$(li_target).children("span").text("Välja bild + text till ord");
			data.answer = 0;
			data.content = "___";
			SelectPictureAndWord_Init();
			break;
		case "selectMatchingWord":
			$(li_target).children("span").text("Välj ett matchade ord");
			data.content = "___";
			data.answer = 0;
			SelectMatchingWord_Init();
			break;
		case "wordNotBelong":
			$(li_target).children("span").text("Ordet inte tillhör");
			WordNotBelong_Init();
			break;
		case "pairedText":
				$(li_target).children("span").text("Parad text");
				data.content = "___";
				data.answer = 0;
				PairedText_Init();
				break;
		case "textNotBelong":
				$(li_target).children("span").text("Textet inte tillhör");
				data.content = "___";
				data.answer = 0;
				TextNotBelong_Init();
				break;
		case "selectAnswerToImage":
				$(li_target).children("span").text("Välja ett svar till bild");
				data.answer = 0;
				data.content = "___";
				SelectAnswerToImage_Init();
				break;
	}
}

function _ImageDragEnter(_target)
{
	$(_target).css("border", "solid #aaf 2px");
}

function _ImageDragLeave(_target)
{
	$(_target).css("border", "");
}

function _ImageDrop(_target)
{
	$(_target).css("border", "solid #afa 2px");
}