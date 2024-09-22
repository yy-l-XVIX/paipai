$(document).ready(function()
{
	// 角色選擇
	$('.JqLgnBtnKind').on('click' , function()
	{
		$('.JqLgnBtnKind').removeClass('active');
		if($(this).find('input[type="radio"]').prop('checked'))
		{
			$(this).addClass('active');
		}
	});

	$('.JqSubmit').on('click' , function()
	{
		var sUrl = $('input[name=sAct]').val();
		var sAccount = $('input[name=sAccount]').val();
		var sPassword = $('input[name=sPassword]').val();
		if (sAccount != '' && sPassword != '' && !$(this).hasClass('active'))
		{
			$(this).addClass('active');

			fetch(sUrl, {
				method: 'post',
				body: new FormData(document.getElementById('JqLoginForm'))
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
					$('.JqRedirectClose').attr('href',result.sUrl);
					// location.href = result.sUrl;
				}
				else
				{
					$('.JqRedirectClose').attr('href',result.sUrl);
					$('.JqRedirectClose').addClass('JqClose');
					$(this).removeClass('active');
				}
				$('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox').addClass('active');
			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});