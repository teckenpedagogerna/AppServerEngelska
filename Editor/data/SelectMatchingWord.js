function SelectMatchingWord_Init()
{
	// get data
	$("#SelectMatchingWord_title").val(FUC(data.question));

	$("#SelectMatchingWord_input_0").val(FUC(data.content.split('_')[0]));
	$("#SelectMatchingWord_input_1").val(FUC(data.content.split('_')[1]));
	$("#SelectMatchingWord_input_2").val(FUC(data.content.split('_')[2]));
	$("#SelectMatchingWord_input_3").val(FUC(data.content.split('_')[3]));

	switch(data.answer)
	{
		case 0: $("#SelectMatchingWord_radio_0").prop("checked", true); break;
		case 1: $("#SelectMatchingWord_radio_1").prop("checked", true); break;
		case 2: $("#SelectMatchingWord_radio_2").prop("checked", true); break;
		case 3: $("#SelectMatchingWord_radio_3").prop("checked", true); break;
		default:
			$("#SelectMatchingWord_radio_0").prop("checked", false);
			$("#SelectMatchingWord_radio_1").prop("checked", false);
			$("#SelectMatchingWord_radio_2").prop("checked", false);
			$("#SelectMatchingWord_radio_3").prop("checked", false);
			break;
	}
}

function SelectMatchingWord_ChangeVideoCountry()
{
	$("#SelectMatchingWord_video_0").val(GetVideo(0));
	$("#SelectMatchingWord_video_1").val(GetVideo(1));
	$("#SelectMatchingWord_video_2").val(GetVideo(2));
	$("#SelectMatchingWord_video_3").val(GetVideo(3));
	$("#SelectMatchingWord_video_4").val(GetVideo(4));
}

function SelectMatchingWord_Title(_target)
{
	data.question = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	$("#save_lesson").css("background-color", "#000");
}

function SelectMatchingWord_Input(_target, index)
{
	var array = data.content.split('_');
	array[index] = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	data.content = array[0] + "_" + array[1] + "_" + array[2] + "_" + array[3];
	$("#save_lesson").css("background-color", "#000");
}

function SelectMatchingWord_Video(_target, index)
{
	UpdateVideo($(_target).val(), index);
	$("#SelectMatchingWord_video_" + index).val(GetVideo(index));

	$("#save_lesson").css("background-color", "#000");
}

function SelectMatchingWord_CorrectIndex(index)
{
	data.answer = index;
	$("#save_lesson").css("background-color", "#000");
}