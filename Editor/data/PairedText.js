function PairedText_Init()
{
	$("#PairedText_0").val(FUC(data.content.split('_')[0]));
	$("#PairedText_1").val(FUC(data.content.split('_')[1]));
	$("#PairedText_2").val(FUC(data.content.split('_')[2]));
	$("#PairedText_3").val(FUC(data.content.split('_')[3]));
}

function PairedText(_target, index)
{
	var array = data.content.split('_');
	array[index] = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	data.content = array[0] + "_" + array[1] + "_" + array[2] + "_" + array[3];
	$("#save_lesson").css("background-color", "#000");
}