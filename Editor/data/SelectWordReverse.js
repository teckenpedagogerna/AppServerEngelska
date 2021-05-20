function SelectWordReverse_Init()
{
	// get data
	$("#SelectWordReverse_Word").val(FUC(data.question));

	$("#SelectWordReverse_GuesssWord_0").val(FUC(data.content.split('_')[0]));
	$("#SelectWordReverse_GuesssWord_1").val(FUC(data.content.split('_')[1]));
	$("#SelectWordReverse_GuesssWord_2").val(FUC(data.content.split('_')[2]));
	$("#SelectWordReverse_GuesssWord_3").val(FUC(data.content.split('_')[3]));

	switch(data.answer)
	{
		case 0: $("#SelectWordReverse_Radio_0").prop("checked", true); break;
		case 1: $("#SelectWordReverse_Radio_1").prop("checked", true); break;
		case 2: $("#SelectWordReverse_Radio_2").prop("checked", true); break;
		case 3: $("#SelectWordReverse_Radio_3").prop("checked", true); break;
		default:
			$("#SelectWordReverse_Radio_0").prop("checked", false);
			$("#SelectWordReverse_Radio_1").prop("checked", false);
			$("#SelectWordReverse_Radio_2").prop("checked", false);
			$("#SelectWordReverse_Radio_3").prop("checked", false);
			break;
	}
}

function SelectWordReverse_ChangeVideoCountry()
{
	$("#SelectWordReverse_Video_0").val(GetVideo(0));
	$("#SelectWordReverse_Video_1").val(GetVideo(1));
	$("#SelectWordReverse_Video_2").val(GetVideo(2));
	$("#SelectWordReverse_Video_3").val(GetVideo(3));
	$("#SelectWordReverse_Video_4").val(GetVideo(4));
}

function SelectWordReverse_Word(_target)
{
	data.question = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	$("#save_lesson").css("background-color", "#000");
}

function SelectWordReverse_GuesssWords(_target, index)
{
	var array = data.content.split('_');
	array[index] = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	data.content = array[0] + "_" + array[1] + "_" + array[2] + "_" + array[3];
	$("#save_lesson").css("background-color", "#000");
}

function SelectWordReverse_Video(_target, index)
{
	$("#save_lesson").css("background-color", "#000");
}

function SelectWordReverse_CorrectIndex(index)
{
	data.answer = index;
	$("#save_lesson").css("background-color", "#000");
}