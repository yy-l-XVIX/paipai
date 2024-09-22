
$(document).ready(function()
{
	$('.JqGetCredit').click(function()
	{
		var sUrl 		= $('input[name=sAjax]').val();
		var sJWT		= $('input[name=sAjaxJWT]').val();


		$.ajax({
			url: sUrl,
			type: 'POST',
			dataType: 'json',
			data: {
					'sJWT': sJWT,
				},
			success: function (response)
			{
				aReturn = response;
				console.log(aReturn);
				$('.JqShowCredit').html(aReturn['sMsg']);
			},
			error: function (exception)
			{
				console.log('Exeption:'+ exception);
			}
		});

	});
});