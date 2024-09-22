$(document).ready(function()
{
	// 刪除聊天
	$('.JqSelfDel').on('click', function (event)
	{
		event.preventDefault();
		$('.JqJumpMsgBox[data-showmsg=0]').removeClass('active');

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

				if (result.nStatus == 1)
				{
					location.reload();
				}
				// $('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqRedirectClose').attr('href',result.sUrl);
				// $('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				// $('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');

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

	// next page
	$(window).scroll(function()
	{
		totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());
		if ($(document).height() == totalheight)
		{
			data={
				sUrl: $('input[name=sFetch]').val()+'&nPageNo='+parseInt($('input[name=nPageNo]').val()),
				nPageNo: parseInt($('input[name=nPageNo]').val()),
				sTemlplateHtml: $('.JqCopy').html(),
			}

			DoNextPage(data);
		}
	});
});