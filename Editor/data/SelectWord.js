function SelectWord_Init()
{
	// get data
	$("#SelectWord_Word").val(FUC(data.question));

	$("#SelectWord_GuesssWord_0").val(FUC(data.content.split('_')[0]));
	$("#SelectWord_GuesssWord_1").val(FUC(data.content.split('_')[1]));
	$("#SelectWord_GuesssWord_2").val(FUC(data.content.split('_')[2]));
	$("#SelectWord_GuesssWord_3").val(FUC(data.content.split('_')[3]));

	switch(data.answer)
	{
		case 0: $("#SelectWord_Radio_0").prop("checked", true); break;
		case 1: $("#SelectWord_Radio_1").prop("checked", true); break;
		case 2: $("#SelectWord_Radio_2").prop("checked", true); break;
		case 3: $("#SelectWord_Radio_3").prop("checked", true); break;
		default:
			$("#SelectWord_Radio_0").prop("checked", false);
			$("#SelectWord_Radio_1").prop("checked", false);
			$("#SelectWord_Radio_2").prop("checked", false);
			$("#SelectWord_Radio_3").prop("checked", false);
			break;
	}
}

function SelectWord_ChangeVideoCountry()
{
	$("#SelectWord_Video_0").val(GetVideo(0));
	$("#SelectWord_Video_1").val(GetVideo(1));
	$("#SelectWord_Video_2").val(GetVideo(2));
	$("#SelectWord_Video_3").val(GetVideo(3));
	$("#SelectWord_Video_4").val(GetVideo(4));
}

function SelectWord_Word(_target)
{
	data.question = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	$("#save_lesson").css("background-color", "#000");
}

function SelectWord_GuesssWords(_target, index)
{
	var array = data.content.split('_');
	array[index] = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	data.content = array[0] + "_" + array[1] + "_" + array[2] + "_" + array[3];
	$("#save_lesson").css("background-color", "#000");
}

function SelectWord_Video(_target, index)
{
	$("#save_lesson").css("background-color", "#000");
}

function SelectWord_CorrectIndex(index)
{
	data.answer = index;
	$("#save_lesson").css("background-color", "#000");
}