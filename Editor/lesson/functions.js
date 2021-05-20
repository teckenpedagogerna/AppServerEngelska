var target;

function AddLesson()
{
	var highest = 0;
	for(var i = 0; i < lessons.length; i++)
		if(lessons[i] > highest)
			highest = lessons[i];

	lessons.push(highest + 1);

	$.post(
		"lesson/add_lesson.php",
		{
			id: lessons[lessons.length - 1],
			step: ($('div[name*="lesson"]').length - 1),
			packageID: packageID
		}
	).done(function (message)
	{
		if(message == "INSERTED")
			$("#lesson_clone").clone().insertBefore("#lesson_clone").css("display", "flex").attr("id", "lesson_" + lessons[lessons.length - 1]);
	});
}

function MoveLessonOther(_target, id)
{
	var title = $("#lesson_" + id).children().eq(0).children().eq(1).children().eq(0).find('input').val();

	$('#modal_title').html('Flytta <b><i>' + title + '</i></b>');
	$('#modal_content').html("<input value='" + packageID + "' /> <br><br>Kopiera och klistra lektionens ID ditåt man vill flytta det här gruppen.");
	$('#modal_close').html("Avbryta");
	$('#modal_ok').html("Flytta");
	$('#modal_ok').attr("onclick", "DoMoveLessonOther('" + id + "');");

	$('#modal').modal();
}

function DoMove(id)
{
	var list = $("#lesson_" + id).parent().find('div[name="lesson"]');
	$("#lesson_" + id).remove();

	for(var i = 0; i < list.length; i++)
	{
		if($(list[i]).attr('id') != 'lesson_clone' && $(list[i]).attr('id') != 'lesson_' + id)
		{
			$(list[i]).children().eq(1).find('button').eq(1).css('display', 'none');
			break;
		}
	}

	for(var i = list.length - 1; i >= 0; i--)
	{
		if($(list[i]).attr('id') != 'lesson_clone' && $(list[i]).attr('id') != 'lesson_' + id)
		{
			$(list[i]).children().eq(1).find('button').eq(2).css('display', 'none');
			break;
		}
	}

}

function DoMoveLessonOther(id)
{
	var newPackageID = $('#modal_content').find('input').val();

	if(!Number.isInteger(parseInt(newPackageID)))
	{
		alert('Fel ID! Det ska vara XX.YY som går att hittas på sidan där man vill flytta här lektionen. Det här gruppen har ID ' + packageID + '.')
		return;
	}

	$.post(
		"lesson/move_lesson_id.php",
		{
			id: id,
			packageID: packageID,
			newPackageID: newPackageID
		}
	).done(function (message)
	{
		switch(message)
		{
			case 'ID NOT FOUND':
				alert('ID hittades inte.');
				break;
			case 'SAME ID':
				alert('Du har gett samma ID som det här sidan.');
				break;
			case 'MOVED':
				var title = $("#lesson_" + id).children().eq(0).children().eq(1).children().eq(0).find('input').val();
				DoMove(id);
				alert("\"" + title + "\" har flyttat till ID: " + newPackageID);
				break;
		}

		$('#modal').modal('hide');
	});
}

function MoveLesson(theButton, id, direction)
{
	$.post(
		"lesson/move_lesson.php",
		{
			id: id,
			packageID: packageID,
			direction: direction
		}
	).done(function (otherID)
	{
		var selfID = $(theButton).parent().parent().attr('id').split('_')[1];

		if(direction == 'UP')
			$("#lesson_" + selfID).after($("#lesson_" + otherID));
		else
			$("#lesson_" + selfID).before($("#lesson_" + otherID));

		var firstButton0 = $("#lesson_" + selfID).children().eq(1).find('button').eq(1).css('display');
		var firstButton1 = $("#lesson_" + otherID).children().eq(1).find('button').eq(1).css('display');

		$("#lesson_" + selfID).children().eq(1).find('button').eq(1).css('display', firstButton1);
		$("#lesson_" + otherID).children().eq(1).find('button').eq(1).css('display', firstButton0);

		var secondButton0 = $("#lesson_" + selfID).children().eq(1).find('button').eq(2).css('display');
		var secondButton1 = $("#lesson_" + otherID).children().eq(1).find('button').eq(2).css('display');

		$("#lesson_" + selfID).children().eq(1).find('button').eq(2).css('display', secondButton1);
		$("#lesson_" + otherID).children().eq(1).find('button').eq(2).css('display', secondButton0);
	});
}

function EditLesson(_target)
{
	window.location.href = "https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Editor/data.php?lessonID=" 
	+ $(_target).parent().parent().attr("id").split('_')[1] + "&packageID=" + packageID;
}

function UpdateTitle(_target)
{
	$(_target).parent().find("button").css("background-color", "#faa");
}

function UploadTitle(_target)
{
	$.post(
		"lesson/title.php",
		{
			id: $(_target).parent().parent().parent().parent().attr("id").split('_')[1],
			text: $(_target).parent().find("input").val(),
			packageID: packageID
		}
	).done(function (message)
	{
		if(message == "UPDATED")
			$(_target).parent().find("button").css("background-color", "#afa");
	});
}

function UpdateRecognition(_target)
{
	$(_target).parent().find("button").css("background-color", "#faa");
}

function UploadRecognition(_target)
{
	$.post(
		"lesson/recognition.php",
		{
			id: $(_target).parent().parent().parent().parent().attr("id").split('_')[1],
			text: $(_target).parent().find("input").val(),
			packageID: packageID
		}
	).done(function (message)
	{
		if(message == "UPDATED")
			$(_target).parent().find("button").css("background-color", "#afa");
	});
}

function RemoveLesson(_target)
{
	target = $(_target).parent().parent()[0];

	$('#modal_title').html('Ta bort <b><i>' + $(target).find('[name="title"]').val() + '</i></b>?');
	$('#modal_content').html("Alla övningar under det här lektion kommer att tas bort.");
	$('#modal_close').html("Avbryta");
	$('#modal_ok').html("Ta bort");
	$('#modal_ok').attr("onclick", "DoRemoveLesson();");

	$('#modal').modal();
}

function DoRemoveLesson()
{
	$('#modal_ok').prop("disabled", true);
	$.post(
		"lesson/remove.php",
		{
			id: $(target).attr("id").split('_')[1],
			packageID: packageID
		}
	).done(function (message)
	{
		$('#modal_ok').prop("disabled", false);
		
		switch(message)
		{
			case "DELETED":
				$('#modal').modal('hide');
				lessons.splice(lessons.indexOf($(target).attr("id").split('_')[1]), 1);
				$(target).remove();
				target = null;
				break;
			default:
				alert('Server fel, vänligen kontakta webbutvecklare.');
				break;
		}
	});
}

// Explainer
function AddExplain()
{
	$.post(
		"lesson/add_explain.php",
		{
			packageID: packageID
		}
	).done(function (message)
	{
		if(message == "INSERTED")
		{
			$("#lesson_tips_add").css("display", "none");
			$("#lesson_tips").css("display", "flex");
		}
	});
}

function EditExplain(_target)
{
	window.location.href = "https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Explainer/index.php?packageID=" + packageID + "&zoneID=" + zoneID;
}

function UpdateTitleExplain(_target)
{
	$(_target).parent().find("button").css("background-color", "#faa");
}

function UploadTitleExplain(_target)
{
	$.post(
		"lesson/title_explain.php",
		{
			text: $(_target).parent().find("input").val(),
			packageID: packageID,
			zoneID: zoneID
		}
	).done(function (message)
	{
		if(message == "UPDATED")
			$(_target).parent().find("button").css("background-color", "#aaf");
	});
}

function UpdateRecognitionExplain(_target)
{
	$(_target).parent().find("button").css("background-color", "#faa");
}

function UploadRecognitionExplain(_target)
{
	$.post(
		"lesson/recognition_explain.php",
		{
			text: $(_target).parent().find("input").val(),
			packageID: packageID,
			zoneID: zoneID
		}
	).done(function (message)
	{
		if(message == "UPDATED")
			$(_target).parent().find("button").css("background-color", "#aaf");
	});
}

function RemoveExplain(_target)
{
	target = $(_target).parent().parent()[0];

	$('#modal_title').html('Ta bort <b><i>' + $(target).find('[name="title"]').val() + '</i></b>?');
	$('#modal_content').html("Alla förklaringar under det här sida kommer att tas bort.");
	$('#modal_close').html("Avbryta");
	$('#modal_ok').html("Ta bort");
	$('#modal_ok').attr("onclick", "DoRemoveExplain();");

	$('#modal').modal();
}

function DoRemoveExplain()
{
	$('#modal_ok').prop("disabled", true);
	$.post(
		"lesson/remove_explain.php",
		{
			packageID: packageID,
			zoneID: zoneID
		}
	).done(function (message)
	{
		$('#modal_ok').prop("disabled", false);
		
		switch(message)
		{
			case "DELETED":
				$('#modal').modal('hide');
				$("#lesson_tips_add").css("display", "flex");
				$("#lesson_tips").css("display", "none");
				
				$("#explainTitle").val("");
				$("#explainRecognition").val("");
				break;
			default:
				alert('Server fel, vänligen kontakta webbutvecklare.');
				break;
		}
	});
}