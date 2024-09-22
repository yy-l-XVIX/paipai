$(document).ready(function()
{
	var size = $('.JqBtnSize').outerWidth(true);
	$('.JqBtnAddSize').css({'width':size+'px','height':size+'px'});
	$('.JqHeaderTit').text($('.JqChange').val());
	$('.JqHeaderLeft').attr('href',$('.JqBackUrl').val());

	// 防呆
	$('.JqActStupidOut').on('click', function (event)
	{
		// 換上內容
		$('.JqJumpMsgBox[data-showmsg="stupidout"]').find('.JqJumpMsgContentTxt').html($(this).attr('data-msg'));

		//換上連結
		$('.JqJumpMsgBox[data-showmsg="stupidout"]').find('.JqReplaceO').attr('href',$(this).attr('data-act'));

		// 彈窗顯示
		$('.JqJumpMsgBox[data-showmsg="stupidout"]').addClass('active');
	});

	$('.JqDoAction').on('click', function (event)
	{
		event.preventDefault();

		$(this).parents('.JqJumpMsgBox').removeClass('active');
		var sUrl = $(this).attr('href');
		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');
			fetch(sUrl, {
				method: 'post',
				body: '',
				headers: {
					'content-type': 'application/x-www-form-urlencoded; charset=UTF-8'
				},
			}).then( res => {
				if (!res.ok)
				{
					alert(res.statusText);
		 			throw new Error(res.statusText);
				}

				return res.json();
			}).then( result => {
				$(this).removeClass('active');

				$('.JqJumpMsgBox[data-showmsg=1]').find('.JqRedirectClose').attr('href',result.sUrl);
				$('.JqJumpMsgBox[data-showmsg=1]').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg=1]').addClass('active');

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});

	// 選擇人才視窗
	$('.JqGroupBtnPick').on('click' , function()
	{
		$('.JqGroupPickBox').addClass('active');
		$('body').css('overflow','hidden');
	});
});