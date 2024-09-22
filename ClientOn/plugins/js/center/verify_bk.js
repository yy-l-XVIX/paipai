
$(document).ready(function()
{
	$('.JqGetVcode').click(function()
	{
		if(!$('.JqGetVcode').hasClass('active'))
		{
			$('.Notice').removeClass('active');

			var sUrl 		= $('input[name=sCodeAct]').val();
			var sAccount 	= $('input[name=sAccount]').val();
			var sPhone 		= $('input[name=sPhone]').val();
			var bPass = true;

			if (!sAccount.match(/^[A-Za-z0-9]{6,16}$/))
			{
				$('.JqAccount').addClass('active');
				bPass = false;
			}
			if (!sPhone.match(/^09[0-9]{8}$/))
			{
				$('.JqPhone').addClass('active');
				bPass = false;
			}

			if (bPass)
			{
				fetch(sUrl, {
					method: 'post',
					body: new FormData(document.getElementById('JqForgotForm'))
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
						$('input[name=nVcode]').val(result.sMsg);
						$('.JqGetVcode').addClass('active');

						var second = $('input[name=nSMSTime]').val();
						$('.JqCounting').html(second);
						var timer = null;
						timer = setInterval(function()
						{
							second -= 1;
							if(second > 0 )
							{
								$('.JqCounting').html(second);
							}
							else
							{
								clearInterval(timer);
								$('.JqGetVcode').removeClass('active');
							}
						},1000);
					}
				}).catch( err => {
					console.log(`Reject ${err}`);
				});
			}

		}
	});

	$('#JqSubmit').click(function()
	{
		var sUrl 	= $('input[name=sAct]').val();
		var bPass = true;
		$("form#JqForgotForm :input").each(function()
		{
			if ($(this).val() == '')
			{
				bPass = false;
				return false;
			}
		});

		fetch(sUrl, {
			method: 'post',
			body: new FormData(document.getElementById('JqForgotForm'))
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
				$('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox').addClass('active');
				location.href = result.sUrl;
			}
		}).catch( err => {
			console.log(`Reject ${err}`);
		});
	});
});