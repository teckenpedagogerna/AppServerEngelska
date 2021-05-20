function MessageAnswer_Init()
{
	$("#MessageAnswer_Question").val(FUC(data.question));

	$("#MessageAnswer_Answer_0").val(FUC(data.content.split('_')[0]));
	$("#MessageAnswer_Answer_1").val(FUC(data.content.split('_')[1]));
	$("#MessageAnswer_Answer_2").val(FUC(data.content.split('_')[2]));
	$("#MessageAnswer_Answer_3").val(FUC(data.content.split('_')[3]));

	switch(data.answer)
	{
		case 0: $("#MessageAnswer_Radio_0").prop("checked", true); break;
		case 1: $("#MessageAnswer_Radio_1").prop("checked", true); break;
		case 2: $("#MessageAnswer_Radio_2").prop("checked", true); break;
		case 3: $("#MessageAnswer_Radio_3").prop("checked", true); break;
		default:
			$("#MessageAnswer_Radio_0").prop("checked", false);
			$("#MessageAnswer_Radio_1").prop("checked", false);
			$("#MessageAnswer_Radio_2").prop("checked", false);
			$("#MessageAnswer_Radio_3").prop("checked", false);
			break;
	}
}

function MessageAnswer_ChangeVideoCountry()
{
	$("#MessageAnswer_Video_0").val(GetVideo(0));
	$("#MessageAnswer_Video_1").val(GetVideo(1));
	$("#MessageAnswer_Video_2").val(GetVideo(2));
	$("#MessageAnswer_Video_3").val(GetVideo(3));
	$("#MessageAnswer_Video_4").val(GetVideo(4));
}

function MessageAnswer_Question(_target)
{
	data.question = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	$("#save_lesson").css("background-color", "#000");
}

function MessageAnswer_Answer(_target, index)
{
	var array = data.content.split('_');
	array[index] = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	data.content = array[0] + "_" + array[1] + "_" + array[2] + "_" + array[3];
	$("#save_lesson").css("background-color", "#000");
}

function MessageAnswer_Video(_target, index)
{
	UpdateVideo($(_target).val(), index);
	$("#MessageAnswer_Video_" + index).val(GetVideo(index));

	$("#save_lesson").css("background-color", "#000");
}

function MessageAnswer_CorrectIndex(index)
{
	data.answer = index;
	$("#save_lesson").css("background-color", "#000");
}