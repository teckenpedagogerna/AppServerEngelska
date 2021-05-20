function IsTrueImage_Init()
{
	$("#IsTrueImage_title").val(FUC(data.question));
	$("#IsTrueImage_answerTitle").val(FUC(data.answer.split('_')[1]));
	$("#IsTrueImage_answerTitle_translation").val(FUC(data.content));

	$("#IsTrueImage_Image").css("background-image", "url('https://media-teckenpedagogerna.s3.eu-north-1.amazonaws.com/English/Images/Data/" + data.pictures + "')");

	switch(parseInt(data.answer.split('_')[0]))
	{
		case 0: $("#IsTrueImage_radio_1").prop("checked", true); break;
		case 1: $("#IsTrueImage_radio_0").prop("checked", true); break;
		default:
			$("#IsTrueImage_radio_0").prop("checked", false);
			$("#IsTrueImage_radio_1").prop("checked", false);
			break;
	}
}

function IsTrueImage_Title(_target)
{
	data.question = FUC($(_target).val());
	$(_target).val(data.question);
	$("#save_lesson").css("background-color", "#000");
}

function IsTrueImage_AnswerTitle(_target)
{
	var array = data.answer.split('_');
	array[1] = FUC($(_target).val());
	$(_target).val(array[1]);
	data.answer = array[0] + "_" + array[1];
	$("#save_lesson").css("background-color", "#000");
}

function IsTrueImage_AnswerTitle_Translation(_target)
{
	data.content = FUC($(_target).val());
	$(_target).val(data.content);
	$("#save_lesson").css("background-color", "#000");
}

function IsTrueImage_CorrectIndex(index)
{
	var array = data.answer.split('_');
	array[0] = index;
	data.answer = array[0] + "_" + array[1];
	$("#save_lesson").css("background-color", "#000");
}

function IsTrueImage_ImageUpload(_target)
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