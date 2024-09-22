$(document).ready(function()
{
	$('.JqChange').on('change', function()
	{
		var Url = $('input[name=sPage]').val();
      	location.href=Url+'&nStatus='+$(this).val();
	});
});