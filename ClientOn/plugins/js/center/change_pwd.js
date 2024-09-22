$(document).ready(function()
{

	//完成
	$('.JqSubmit').on('click', function ()
	{
		let sUrl = $('#JqPostForm').attr('action')+'&sJWT='+$('input[name=sActJWT]').val();
		let formPass = true;

		$.each($(':required'),function(LPnIndex,LPoObj){
			if (!FormVerify.empty(LPoObj))
			{
				$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html($('#JqPostForm').attr('data-info'));
				$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
				formPass = false;
				return false;
			}
		});

		if (formPass && !$(this).hasClass('active'))
		{

			$(this).addClass('active');
			$('.JqJumpMsgBox[data-showmsg=dataprocessing]').addClass('active');

			fetch(sUrl, {
				method: 'post',
				body: new FormData(document.getElementById('JqPostForm'))
			}).then(res => {
				if (!res.ok) {
					alert(res.statusText);
					throw new Error(res.statusText);
				}

				return res.json();
			}).then(result => {
				if(result.nStatus == 1)
				{
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqRedirectClose').attr('href',result.sUrl);
				}
				$(this).removeClass('active');
				$('.JqJumpMsgBox[data-showmsg=dataprocessing]').removeClass('active');
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');

			}).catch(err => {
				console.log(`Reject ${err}`);
			})
		}
	});

});