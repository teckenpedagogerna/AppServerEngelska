var target;
var target2;

function UpdateApp()
{
	$("#updateID").prop("disabled", true);

	$.ajax({
		url: "https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/generate.php",
		type: "POST",
		processData: false,
		contentType: false,
		success: function (result)
		{
			$("#updateID").prop("disabled", false);
			$("#version_control").html("Verison: " +  result);
			alert('Alla enheter kommer att uppdateras vid appens nästa omstart!');
		}
	});	
}

var moveID = null;
var moveID2 = null;

function MovePackage(self, id)
{
	if(moveID == null)
	{
		moveID = id;
		var length = $('button[name="moveButton"]').length;

		for(var i = 0; i < length; i++)
		{
			if($($('button[name="moveButton"]')[i]).attr('pid') != id)
			{
				$($('button[name="moveButton"]')[i]).css('background-color', '#0af');
				$($('button[name="moveButton"]')[i]).css('color', '#fff');
			}
			else
			{
				$($('button[name="moveButton"]')[i]).css('background-color', '#f40');
				$($('button[name="moveButton"]')[i]).css('color', '#fff');
			}
		}
	}
	else if(moveID2 == null)
	{
		moveID2 = id;


		$.post(
			"package/swap_packages.php",
			{
				pid: moveID,
				newpid: moveID2
			}
		).done(function (message)
		{
			console.log(message);

			var length = $('button[name="moveButton"]').length;

			for(var i = 0; i < length; i++)
			{
				$($('button[name="moveButton"]')[i]).css('background-color', '#aaa');
				$($('button[name="moveButton"]')[i]).css('color', '#000');
			}

			// do move

			$('#p' + moveID).SwapWith($('#p' + moveID2));

			// end
			moveID = null;
			moveID2 = null;
		});
	}
}

function EnterPackage(id)
{
	window.location.href = "https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Editor/lesson.php?id=" + id;
}

function ImageDragEnter(_target)
{
	$(_target).css("border", "solid #aaf 2px");
}

function ImageDragLeave(_target)
{
	$(_target).css("border", "");
}

function ImageDrop(_target)
{
	$(_target).css("border", "solid #afa 2px");
}

function BackgroundUpload(_target, id)
{
	var formdata = new FormData();
	formdata.append("image", $(_target)[0].files[0]);
	formdata.append("id", id);

	$.ajax({
		url: "package/upload_background_image.php",
		type: "POST",
		data: formdata,
		processData: false,
		contentType: false,
		success: function (result)
		{
			var reader = new FileReader();
			reader.readAsDataURL($(_target)[0].files[0]);
			
			reader.onload = function ()
			{
				var type = $(_target)[0].files[0];
				$("#zone_" + id).css("background-image", "url('" + reader.result + "')");
			};

			reader.onerror = function (error)
			{
				alert('Error: ', error);
			};
		}
	});
}

function ImageUpload(_target, id)
{
	var formdata = new FormData();
	formdata.append("image", $(_target).children(0)[0].files[0]);
	formdata.append("id", id);

	$.ajax({
		url: "package/upload_image.php",
		type: "POST",
		data: formdata,
		processData: false,
		contentType: false,
		success: function (result)
		{
			var reader = new FileReader();
			reader.readAsDataURL($(_target).children(0)[0].files[0]);
			reader.onload = function ()
			{
				var type = $(_target).children(0)[0].files[0];
				$(_target).css("background-image", "url('" + reader.result + "')");
				$(_target).css("border", "");
			};

			reader.onerror = function (error)
			{
				alert('Error: ', error);
			};
		}
	});
}

function AddRow()
{
	$.ajax({
		url: "package/add_zone.php",
		type: "POST",
		processData: false,
		contentType: false,
		success: function (result)
		{
			var data = JSON.parse(result);

			$("#clone_zone").clone().insertBefore("#clone_zone").css("display", "flex").attr("id", "zone_" + data[0].zoneID);

			// zone
			$("#zone_" + data[0].zoneID).find('img[name="removeZone"]').attr('onclick', 'RemoveZone(' + data[0].zoneID + ');');
			$("#zone_" + data[0].zoneID).find('button[name="uploadTitle"]').attr('onclick', 'UploadZoneTitle(this, ' + data[0].zoneID + ');');
			$("#zone_" + data[0].zoneID).find('button[name="uploadDescription"]').attr('onclick', 'UploadZoneDescription(this, ' + data[0].zoneID + ');');
			$("#zone_" + data[0].zoneID).find('button[name="activate"]').attr('onclick', 'ToggleActive(' + data[0].zoneID + ');');
			$("#zone_" + data[0].zoneID).find('input[name="bi"]').attr('onchange', 'BackgroundUpload(this, ' + data[0].zoneID + ');');

			// package
			for (var i = 0; i < 6; i++)
			{
				$("#zone_" + data[0].zoneID).find('button[name="t' + i + '"]').attr('onclick', 'UploadTitle(this, ' + data[i + 1].nID + ');');
				$("#zone_" + data[0].zoneID).find('button[name="e' + i + '"]').attr('onclick', 'EnterPackage(' + data[i + 1].nID + ');');
				$("#zone_" + data[0].zoneID).find('div[name="i' + i + '"]').attr('onchange', 'ImageUpload(this, ' + data[i + 1].nID + ');');
			}
		}
	});
}

function RemoveZone(id)
{
	$('#modal_title').html('Ta bort <b><i>' + $("#zone_" + id).find('input').val() + '</i></b>?');
	$('#modal_content').html("Alla data under det här nivå kommer att tas bort.");
	$('#modal_close').html("Avbryta");
	$('#modal_ok').html("Ta bort");
	$('#modal_ok').attr("onclick", "DoRemoveZone(" + id + ");");

	$('#modal').modal();
}

function DoRemoveZone(id)
{
	$('#modal_ok').prop("disabled", true);
	$.post(
		"package/remove.php",
		{
			id: id
		}
	).done(function (message)
	{
		$('#modal_ok').prop("disabled", false);
		
		switch(message)
		{
			case "DELETED":
				$('#modal').modal('hide');
				$("#zone_" + id).remove();
				break;
			default:
				alert('Server fel, vänligen kontakta webbutvecklare.');
				break;
		}
	});
}

function ToggleActive(id)
{
	switch($("#zone_" + id).attr("data-activated"))
	{
		case "0":
			$('#modal_title').html('Aktivera <b><i>' + $("#zone_" + id).find('input').val() + '</i></b>?');
			$('#modal_content').html("Alla lektioner under det här paket kommer att vara synliga för alla användare.");
			$('#modal_ok').html("Aktivera");
			break;
		case "1":
			$('#modal_title').html('Avaktivera <b><i>' + $("#zone_" + id).find('input').val() + '</i></b>?');
			$('#modal_content').html("Alla lektioner under det här paket kommer att vara dolda för alla användare.");
			$('#modal_ok').html("Avaktivera");
			break;
	}
	
	$('#modal_close').html("Avbryta");
	$('#modal_ok').attr("onclick", "DoToggleActive(" + id + ");");

	$('#modal').modal();
}

function DoToggleActive(id)
{
	var actived;

	switch($("#zone_" + id).attr("data-activated"))
	{
		case "0":
			actived = 1;
			break;
		case "1":
			actived = 0;
			break;
	}

	$.post(
		"package/active.php",
		{
			id: id,
			active: actived
		}
	).done(function (message)
	{
		$('#modal_ok').prop("disabled", false);
		
		switch(message)
		{
			case "ACTIVATED":
				$('#modal').modal('hide');
				$("#zone_" + id).attr("data-activated", "1");
				$("#zone_" + id).find('button[name="activate"]').css("background-color", "#afa");
				break;
			case "DEACTIVATED":
				$('#modal').modal('hide');
				$("#zone_" + id).attr("data-activated", "0");
				$("#zone_" + id).find('button[name="activate"]').css("background-color", "#faa");
				break;
			default:
				alert('Server fel, vänligen kontakta webbutvecklare.');
				break;
		}
	});
}

function UpdateTitle(_target)
{	
	$(_target).parent().find("button").css("background-color", "#faa");
}

function UploadTitle(_target, id)
{
	target2 = $(_target).parent().find("input");
	$(_target).css("background-color", "#afa");
	$.post(
		"package/update_title.php",
		{
			id: id,
			title: $(target2).val()
		}
	);
}

function UpdateZoneTitle(_target)
{	
	$(_target).parent().find("button").css("background-color", "#faa");
}

function UploadZoneTitle(_target, id)
{
	target2 = $(_target).parent().find("input");
	$(_target).css("background-color", "#afa");
	$.post(
		"package/update_zone_title.php",
		{
			id: id,
			title: $(target2).val()
		}
	);
}

function UpdateZoneDescription(_target)
{	
	$(_target).parent().find("button").css("background-color", "#faa");
}

function UploadZoneDescription(_target, id)
{
	target2 = $(_target).parent().find("input");
	$(_target).css("background-color", "#afa");
	$.post(
		"package/update_zone_description.php",
		{
			id: id,
			description: $(target2).val()
		}
	);
}