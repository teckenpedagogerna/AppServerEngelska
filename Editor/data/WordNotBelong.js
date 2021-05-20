function WordNotBelong_Init()
{
	// get data
	$("#WordNotBelong_Text").val(FUC(data.question));
	WordNotBelong_Buttons_Init(data.question.split(' '), data.answer);
	$("#save_lesson").css("background-color", "");
}

function WordNotBelong_ChangeVideoCountry()
{
	$("#WordNotBelong_Video").val(GetVideo(0));
}

function WordNotBelong_Text(_target)
{
	data.question = FUC($(_target).val());
	$(_target).val(FUC($(_target).val()));
	WordNotBelong_Buttons_Init(data.question.split(' '));
	$("#save_lesson").css("background-color", "#000");
}

function WordNotBelong_Video(_target)
{
	UpdateVideo($(_target).val(), 0);
	$("#WordNotBelong_Video").val(GetVideo(0));

	$("#save_lesson").css("background-color", "#000");
}

function WordNotBelong_Buttons_Init(words, selectIndex = -1)
{
	$("#WordNotBelong_Buttons").html("");
	for(var i = 0; i < words.length; i++)
	{
		$("#WordNotBelong_Buttons").append(
			'<button id="WordNotBelong_Button_' + i + '" onclick="WordNotBelong_Button_Select(' + i + ', ' + words.length + ');" class="btn btn-primary" type="button" style="margin-left: 10px;">' + words[i] + '</button>'
		);
	}

	if(selectIndex != -1)
	{
		WordNotBelong_Button_Select(selectIndex, words.length);
	}
}

function WordNotBelong_Button_Select(index, length)
{
	for (var i = 0; i < length; i++)
	{
		$("#WordNotBelong_Button_" + i).css("background-color", "");
	}
	$("#WordNotBelong_Button_" + index).css("background-color", "#000");
	data.answer = index;
	$("#save_lesson").css("background-color", "#000");
}