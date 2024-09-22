$(document).ready(function()
{
	$('.JqGetVcode').click(function()
	{
		if(!$('.JqGetVcode').hasClass('active'))
		{
			$('.Notice').removeClass('active');

			var sUrl 		= $('form').attr('action');
			var sJWT		= $('input[name=sVcodeJWT]').val();
			var sAccount 	= $('input[name=sAccount]').val();

			if (!$(this).hasClass('JqNophone') && !sAccount.match(/^09[0-9]{8}$/))
			{
				$('.Format').addClass('active');
			}
			else
			{
				sUrl += '&sJWT='+sJWT;

				fetch(sUrl, {
					method: 'post',
					body: new FormData(document.getElementById('JqRegisterForm'))
				}).then( res => {
					if (!res.ok)
					{
						alert(res.statusText);
						throw new Error(res.statusText);
					}

					return res.json();
				}).then( result => {

					if (result.nStatus == 1)
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
					else
					{
						$('.' + result.sMsg).addClass('active');
					}


				}).catch( err => {
					console.log(`Reject ${err}`);
				});
			}

		}
	});

	$('.JqSubmit').on('click' , function()
	{
		var This = $(this);
		var sUrl = $('#JqRegisterForm').attr('action');
		var sJWT = $('input[name=sPostJWT]').val();
		var bPass = true;
		$("form#JqRegisterForm :input").each(function()
		{
			if ($(this).val() == '' && $(this).attr('name') != 'sPromoCode')
			{

				$(this).focus();
				$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html($('#JqRegisterForm').attr('data-info'));
				$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
				bPass = false;
				return false;
			}
		});

		if (bPass && !This.hasClass('active'))
		{
			This.addClass('active');
			sUrl += '&sJWT='+sJWT;
			fetch(sUrl, {
				method: 'post',
				body: new FormData(document.getElementById('JqRegisterForm'))
			}).then( res => {
				if (!res.ok)
				{
					alert(res.statusText);
	 				throw new Error(res.statusText);
				}

				return res.json();
			}).then( result => {
				This.removeClass('active');
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');
				$('.JqNotice').attr('style', '');
				if(result.nStatus == 1)
				{
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqRedirectClose').attr('href',result.sUrl);
				}
				else
				{
					$.each(result.aData, function(LPsName, bTrue)
					{
						 /* iterate through array or object */
						 $('input[name='+LPsName+']').parents('.JqNotice').attr('style', 'border:red 1px solid;');
					});
				}

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});