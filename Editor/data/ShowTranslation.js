function ShowTranslation_Init()
{
	//if(data.videos == "")
	//	InitVideo(1);

	//$("#ShowTranslation_Video").val(GetVideo(0));
	//_ChangeVideoCountry = ShowTranslation_ChangeVideoCountry;

	// get data
	$("#ShowTranslation_Original").val(data.question);
	$("#ShowTranslation_Translation").val(data.content);
}

function ShowTranslation_ChangeVideoCountry(_target)
{
	$("#ShowTranslation_Video").val(GetVideo(0));
}

function ShowTranslation_Original(_target)
{
	data.question = $(_target).val();
	$("#save_lesson").css("background-color", "#000");
}

function ShowTranslation_Translation(_target)
{
	data.content = $(_target).val();
	$("#save_lesson").css("background-color", "#000");
}

function ShowTranslation_Video(_target)
{
	UpdateVideo($(_target).val(), 0);
	$("#ShowTranslation_Video").val(GetVideo(0));

	$("#save_lesson").css("background-color", "#000");
}