function MessageQuestion_Init()
{
	//if(data.videos == "")
	//	InitVideo(5);

	//$("#MessageQuestion_Video_0").val(GetVideo(0));
	//$("#MessageQuestion_Video_1").val(GetVideo(1));
	//$("#MessageQuestion_Video_2").val(GetVideo(2));
	//$("#MessageQuestion_Video_3").val(GetVideo(3));
	//$("#MessageQuestion_Video_4").val(GetVideo(4));

	//_ChangeVideoCountry = MessageQuestion_ChangeVideoCountry;

	// get data
	$("#MessageQuestion_Answer").val(data.question);

	$("#MessageQuestion_Question_0").val(data.content.split('_')[0]);
	$("#MessageQuestion_Question_1").val(data.content.split('_')[1]);
	$("#MessageQuestion_Question_2").val(data.content.split('_')[2]);
	$("#MessageQuestion_Question_3").val(data.content.split('_')[3]);

	switch(data.answer)
	{
		case 0: $("#MessageQuestion_Radio_0").prop("checked", true); break;
		case 1: $("#MessageQuestion_Radio_1").prop("checked", true); break;
		case 2: $("#MessageQuestion_Radio_2").prop("checked", true); break;
		case 3: $("#MessageQuestion_Radio_3").prop("checked", true); break;
		default:
			$("#MessageQuestion_Radio_0").prop("checked", false);
			$("#MessageQuestion_Radio_1").prop("checked", false);
			$("#MessageQuestion_Radio_2").prop("checked", false);
			$("#MessageQuestion_Radio_3").prop("checked", false);
			break;
	}
}

function MessageQuestion_ChangeVideoCountry()
{
	$("#MessageQuestion_Video_0").val(GetVideo(0));
	$("#MessageQuestion_Video_1").val(GetVideo(1));
	$("#MessageQuestion_Video_2").val(GetVideo(2));
	$("#MessageQuestion_Video_3").val(GetVideo(3));
	$("#MessageQuestion_Video_4").val(GetVideo(4));
}

function MessageQuestion_Answer(_target)
{
	data.question = $(_target).val();
	$("#save_lesson").css("background-color", "#000");
}

function MessageQuestion_Question(_target, index)
{
	var array = data.content.split('_');
	array[index] = $(_target).val();
	data.content = array[0] + "_" + array[1] + "_" + array[2] + "_" + array[3];
	$("#save_lesson").css("background-color", "#000");
}

function MessageQuestion_Video(_target, index)
{
	UpdateVideo($(_target).val(), index);
	$("#MessageQuestion_Video_" + index).val(GetVideo(index));
	$("#save_lesson").css("background-color", "#000");
}

function MessageQuestion_CorrectIndex(index)
{
	data.answer = index;
	$("#save_lesson").css("background-color", "#000");
}