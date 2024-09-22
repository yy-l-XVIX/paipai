$(document).ready(function()
{
	$('.JqGetVcode').on('click' , function()
	{
		var sUrl = $('form').attr('action')+'&sJWT='+$('input[name=sCodeJWT]').val();

		if(!$('.JqGetVcode').hasClass('active'))
		{
			fetch(sUrl, {
				method: 'post',
				body: new FormData(document.getElementById('JqPostForm'))
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
					$('input[name=nVcode]').val(result.sMsg);
					$('.JqGetVcode').addClass('active');

					var second = $('input[name=nSMSTime]').val();
					var timer = null;
					$('.JqCounting').html(second);

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
			})
		}
	});

	$('#JqSubmit').click(function()
	{
		var This = $(this);
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
		if (bPass && !This.hasClass('active'))
		{
			This.addClass('active');
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
				This.removeClass('active');

				if(result.nStatus == 1)
				{
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqRedirectClose').attr('href',result.sUrl);
				}
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');
			}).catch( err => {
				console.log(`Reject ${err}`);
			});
		}

	});
});