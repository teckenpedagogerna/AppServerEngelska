
function EditReport(id)
{
	//window.location.href = 'https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Editor/data.php?dataID=' + id;
	window.open('https://appserver-admin.teckenpedagogerna.se/AppServer/Engelska/Editor/data.php?dataID=' + id, '_blank').focus();
}

function RemoveReport(id)
{
	$("#modal_title").html('Ta bort det här rapporten?');
	$("#modal_content").html('Inga data kommer att gå förlorade, bara det här rapport kommer att tas bort. Också om övningen sparas då rapporten försvinner automatiskt.');
	$("#modal_ok").attr("onclick", "DoRemoveReport(" + id + ");")
	$("#modal").modal();
}

function DoRemoveReport(id)
{
	$.post("removereport.php",
	{
		id: id
	}).done(function(response)
	{
		$("#report_" + id).remove();
		$("#modal").modal('hide');
	});
}