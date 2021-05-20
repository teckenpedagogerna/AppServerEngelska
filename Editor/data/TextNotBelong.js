function TextNotBelong_Init()
{
	$("#TextNotBelong_0").val(FUC(data.content.split('_')[0]));
	$("#TextNotBelong_1").val(FUC(data.content.split('_')[1]));
	$("#TextNotBelong_2").val(FUC(data.content.split('_')[2]));
	$("#TextNotBelong_3").val(FUC(data.content.split('_')[3]));

	switch(data.answer)
	{
		case 0: $("#TextNotBelong_Radio_0").prop("checked", true); break;
		case 1: $("#TextNotBelong_Radio_1").prop("checked", true); break;
		case 2: $("#TextNotBelong_Radio_2").prop("checked", true); break;
		case 3: $("#TextNotBelong_Radio_3").prop("checked", true); break;
		default:
			$("#TextNotBelong_Radio_0").prop("checked", false);
			$("#TextNotBelong_Radio_1").prop("checked", false);
			$("#TextNotBelong_Radio_2").prop("checked", false);
			$("#TextNotBelong_Radio_3").prop("checked", false);
			break;
	}
}

function TextNotBelong(_target, index)
{
	var array = data.content.split('_');
	array[index] = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	data.content = array[0] + "_" + array[1] + "_" + array[2] + "_" + array[3];
	$("#save_lesson").css("background-color", "#000");
}