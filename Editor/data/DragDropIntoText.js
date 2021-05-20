function DragDropIntoText_Init()
{
	// get data
	$("#DragDropIntoText_DragWords").val(data.question);
	$("#DragDropIntoText_ContentText").val(data.content);
	$("#DragDropIntoText_FinalContentText").val(data.answer);
}

function DragDropIntoText_ContentText(_target)
{
	data.content = $(_target).val();
	$(_target).val($(_target).val());
	$("#save_lesson").css("background-color", "#000");
}

function DragDropIntoText_DragWords(_target)
{
	data.question = $(_target).val();
	$(_target).val($(_target).val());
	$("#save_lesson").css("background-color", "#000");
}

function DragDropIntoText_FinalContentText(_target)
{
	data.answer = $(_target).val();
	$("#save_lesson").css("background-color", "#000");
}

function DragDropIntoText_Video(_target)
{
	UpdateVideo($(_target).val(), 0);
	//$("#DragDropIntoText_Video").val(GetVideo(0));	
	$("#save_lesson").css("background-color", "#000");
}