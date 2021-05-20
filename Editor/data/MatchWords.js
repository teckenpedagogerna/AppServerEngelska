function MatchWords_Init()
{
	var buffer = "";
	var prebuffer = data.question.split("_");

	for(var i = 0; i < prebuffer.length; i++)
	{
		buffer = buffer + "_" + FUC(prebuffer[i]);
	}

	buffer = buffer.substring(1);
	$("#matchWords_A").val(buffer);
	
	buffer = "";
	prebuffer = data.answer.split("_");

	for(var i = 0; i < prebuffer.length; i++)
	{
		buffer = buffer + "_" + FUC(prebuffer[i]);
	}

	buffer = buffer.substring(1);

	$("#matchWords_B").val(buffer);
}

function MatchWords_A(_target)
{
	var buffer = "";
	var prebuffer = $(_target).val().split("_");

	for(var i = 0; i < prebuffer.length; i++)
	{
		buffer = buffer + "_" + FUC(prebuffer[i]);
	}

	var buffer = buffer.substring(1);

	data.question = buffer;
	$(_target).val(buffer);
	$("#save_lesson").css("background-color", "#000");
}

function MatchWords_B(_target)
{
	var buffer = "";
	var prebuffer = $(_target).val().split("_");

	for(var i = 0; i < prebuffer.length; i++)
	{
		buffer = buffer + "_" + FUC(prebuffer[i]);
	}

	var buffer = buffer.substring(1);

	data.answer = buffer;
	$(_target).val(buffer);
	$("#save_lesson").css("background-color", "#000");
}