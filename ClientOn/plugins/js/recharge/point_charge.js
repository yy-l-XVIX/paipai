$(document).ready(function()
{
	$('.JqSubmit').on('click' , function()
	{
		var This = $(this);
		var sUrl = $('input[name=sAct]').val();
		var bPass = true;

		if (!This.hasClass('active'))
		{
			This.addClass('active');
			fetch(sUrl, {
				method: 'post',
				body: new FormData(document.getElementById('JqPointchargeForm'))
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
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqRedirectClose').attr('href',result.sUrl);
				}
				This.removeClass('active');
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});
