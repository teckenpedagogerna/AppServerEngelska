function BuildText_Init()
{
	// get data
	$("#BuildText_Original").val(FUC(data.question));
	$("#BuildText_Build").val(FUC(data.content));
}

function BuildText_Original(_target)
{
	data.question = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	$("#save_lesson").css("background-color", "#000");
}

function BuildText_Build(_target)
{
	data.content = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	$("#save_lesson").css("background-color", "#000");
}

function BuildText_Useless(_target)
{
	data.answer = $(_target).val();
	$("#save_lesson").css("background-color", "#000");
}
