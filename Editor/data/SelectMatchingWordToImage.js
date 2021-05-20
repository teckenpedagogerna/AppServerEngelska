function SelectMatchingWordToImage_Init()
{
	// get data
	$("#SelectMatchingWordToImage_Title").val(FUC(data.question));

	$("#SelectMatchingWordToImage_GuessWord_0").val(FUC(data.content.split('_')[0]));
	$("#SelectMatchingWordToImage_GuessWord_1").val(FUC(data.content.split('_')[1]));
	$("#SelectMatchingWordToImage_GuessWord_2").val(FUC(data.content.split('_')[2]));
	$("#SelectMatchingWordToImage_GuessWord_3").val(FUC(data.content.split('_')[3]));

	if(data.pictures != "")
	{
		$("#SelectMatchingWordToImage_Image").css("background-image", "url('https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Images/Data/" + data.pictures + "')");		
	}
	else
	{
		$("#SelectMatchingWordToImage_Image").css("background-image", "url('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Download/Images/Server/upload.png')");		
	}

	switch(parseInt(data.answer))
	{
		case 0: $("#SelectMatchingWordToImage_Radio_0").prop("checked", true); break;
		case 1: $("#SelectMatchingWordToImage_Radio_1").prop("checked", true); break;
		case 2: $("#SelectMatchingWordToImage_Radio_2").prop("checked", true); break;
		case 3: $("#SelectMatchingWordToImage_Radio_3").prop("checked", true); break;
		default:
			$("#SelectMatchingWordToImage_Radio_0").prop("checked", false);
			$("#SelectMatchingWordToImage_Radio_1").prop("checked", false);
			$("#SelectMatchingWordToImage_Radio_2").prop("checked", false);
			$("#SelectMatchingWordToImage_Radio_3").prop("checked", false);
			break;
	}
}

function SelectMatchingWordToImage_ChangeVideoCountry()
{
	$("#SelectMatchingWordToImage_Video_0").val(GetVideo(0));
	$("#SelectMatchingWordToImage_Video_1").val(GetVideo(1));
	$("#SelectMatchingWordToImage_Video_2").val(GetVideo(2));
	$("#SelectMatchingWordToImage_Video_3").val(GetVideo(3));
	$("#SelectMatchingWordToImage_Video_4").val(GetVideo(4));
}

function SelectMatchingWordToImage_Title(_target)
{
	data.question = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	$("#save_lesson").css("background-color", "#000");
}

function SelectMatchingWordToImage_GuessWord(_target, index)
{
	var array = data.content.split('_');
	array[index] = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	data.content = array[0] + "_" + array[1] + "_" + array[2] + "_" + array[3];
	$("#save_lesson").css("background-color", "#000");
}

function SelectMatchingWordToImage_Video(_target, index)
{
	UpdateVideo($(_target).val(), index);
	$("#SelectMatchingWordToImage_Video_" + index).val(GetVideo(index));

	$("#save_lesson").css("background-color", "#000");
}

function SelectMatchingWordToImage_CorrectIndex(index)
{
	data.answer = index;
	$("#save_lesson").css("background-color", "#000");
}

function SelectMatchingWordToImage_ImageUpload(_target)
{
	var formdata = new FormData();
	formdata.append("image", $(_target).children(0)[0].files[0]);
	formdata.append("stepIndex", data.stepIndex);
	formdata.append("packageID", packageID);
	formdata.append("lessonID", lessonID);
	formdata.append("index", 0);

	$.ajax({
		url: "data/upload_image.php",
		type: "POST",
		data: formdata,
		processData: false,
		contentType: false,
		success: function (result)
		{
			$("#save_lesson").css("background-color", "#000");
			data.pictures = packageID + "-" + lessonID + "-" + data.stepIndex + "-0.jpg";

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