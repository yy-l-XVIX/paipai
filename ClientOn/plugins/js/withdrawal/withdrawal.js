$(document).ready(function()
{
	// 點選項目
	$('.JqWithdrawalSelBtn').on('click',function()
	{
		if(!$(this).siblings('td').find('input[type="radio"]').prop('checked'))
		{
			$(this).siblings('td').find('input[type="radio"]').prop('checked',true);
		}
	});

	// 確認提領
	$('.JqSubmit').on('click' , function()
	{
		var This = $(this);
		var sUrl = $('#JqPostForm').attr('action');
		var bPass = true;

		if (!This.hasClass('active'))
		{
			This.addClass('active');
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