function SelectPicture_Init()
{
	$("#SelectPicture_Text").val(FUC(data.question));
	var pictures = data.pictures.split(',');

	for(var i = 0; i < pictures.length; i++)
	{
		if(pictures[i] != "")
			$("#SelectPicture_Image_" + i).css("background-image", "url('https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Images/Data/" + pictures[i] + "')");
		else
			$("#SelectPicture_Image_" + i).css("background-image", "url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png')");
	}

	$("#SelectPicture_GuessWord_0").val(FUC(data.content.split('_')[0]));
	$("#SelectPicture_GuessWord_1").val(FUC(data.content.split('_')[1]));
	$("#SelectPicture_GuessWord_2").val(FUC(data.content.split('_')[2]));
	$("#SelectPicture_GuessWord_3").val(FUC(data.content.split('_')[3]));

	switch(data.answer)
	{
		case 0: $("#SelectPicture_Radio_0").prop("checked", true); break;
		case 1: $("#SelectPicture_Radio_1").prop("checked", true); break;
		case 2: $("#SelectPicture_Radio_2").prop("checked", true); break;
		case 3: $("#SelectPicture_Radio_3").prop("checked", true); break;
		default:
			$("#SelectPicture_Radio_0").prop("checked", false);
			$("#SelectPicture_Radio_1").prop("checked", false);
			$("#SelectPicture_Radio_2").prop("checked", false);
			$("#SelectPicture_Radio_3").prop("checked", false);
			break;
	}
}

function SelectPicture_ChangeVideoCountry()
{
	$("#SelectPicture_Video").val(GetVideo(0));
}

function SelectPicture_Text(_target)
{
	data.question = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	$("#save_lesson").css("background-color", "#000");
}

function SelectPicture_Video(_target)
{
	UpdateVideo($(_target).val(), 0);
	$("#SelectPicture_Video").val(GetVideo(0));
	
	$("#save_lesson").css("background-color", "#000");
}

function SelectPicture_CorrectIndex(index)
{
	data.answer = index;
	$("#save_lesson").css("background-color", "#000");
}

function SelectPicture_GuessWords(_target, index)
{
	var array = data.content.split('_');
	array[index] = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	data.content = array[0] + "_" + array[1] + "_" + array[2] + "_" + array[3];
	$("#save_lesson").css("background-color", "#000");
}

function SelectPicture_ImageUpload(_target, index)
{
	var formdata = new FormData();
	formdata.append("image", $(_target).children(0)[0].files[0]);
	formdata.append("stepIndex", data.stepIndex);
	formdata.append("packageID", packageID);
	formdata.append("lessonID", lessonID);
	formdata.append("index", index);

	$.ajax({
		url: "data/upload_image.php",
		type: "POST",
		data: formdata,
		processData: false,
		contentType: false,
		success: function (result)
		{
			$("#save_lesson").css("background-color", "#000");
			var pics = data.pictures.split(',');

			pics[index] = packageID + "-" + lessonID + "-" + data.stepIndex + "-" + index + ".jpg";
			data.pictures = pics[0] + "," + pics[1] + "," + pics[2] + "," + pics[3];

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