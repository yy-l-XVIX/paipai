$(document).ready(function()
{
	$('.JqSelectPayment').on('change', function(event)
	{
		var Url = $('input[name=sPage]').val();
		var nKid = $('input[name=nKid]').val();
		location.href=Url+'&nPid='+$(this).val()+'&nKid='+nKid;
	});

	$('.JqSubmit').on('click' , function()
	{
		var sUrl = $('input[name=sAct]').val();
		var bPass = true;
		$("form#JqPayOnlineForm :input").each(function()
		{
			if ($(this).val() == '')
			{
				bPass = false;
				return false;
			}
		});

		if (bPass)
		{
			fetch(sUrl, {
				method: 'post',
				body: new FormData(document.getElementById('JqPayOnlineForm'))
			}).then( res => {
				if (!res.ok)
				{
					alert(res.statusText);
	 				throw new Error(res.statusText);
				}

				return res.json();
			}).then( result => {
				if(result.nStatus == 1)
				{
					$('.JqJumpMsgContentTxt').html(result.sMsg);
					$('.JqJumpMsgBox').addClass('active');
				}
				else
				{
					$('body').append(result.aData.sForm);
				}
			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});
