$(document).ready(function()
{
	$('.JqPendingAll').on('click', function()
	{
		var sClass = $(this).attr('data-class');
		console.log(sClass);
		$('.'+sClass).prop("checked", true);
	});
});