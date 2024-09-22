$(document).ready(function ()
{
	$('.JqTime').each(function(index, el)
	{
		laydate.render({ elem: el, type: 'datetime' });
	});

});