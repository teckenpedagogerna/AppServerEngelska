function BuildWord_Init()
{
	// get data
	$("#BuildWord_Original").val(FUC(data.question));
	$("#BuildWord_Build").val(FUC(data.content));
}

function BuildWord_Original(_target)
{
	data.question = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	$("#save_lesson").css("background-color", "#000");
}

function BuildWord_Build(_target)
{
	data.content = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	$("#save_lesson").css("background-color", "#000");
}
