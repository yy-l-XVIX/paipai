$(document).ready(function()
{

	// 操作
	$(document).on('click' ,'.JqDiscussBtnAct', function()
	{
		$('.JqDiscussAct').addClass('active');
	});

	// 刪除文章
	// $('.JqDiscuss').on('click', '.JqDiscussDelete', function()
	// {
	// 	$('div').removeAttr('data-delete');

	// 	var sUrl = $('input[name=sDel]').val()+'&nId='+$(this).attr('data-id');
	// 	$('.JqDelete').attr('data-act', sUrl);
	// 	$(this).attr('data-delete', '1');
	// });
	// 刪除文章
	$('.JqDiscuss').on('click', '.JqMoreBtn', function()
      {
            $(this).parents('.JqMoreBox').find('.JqMoreBlock').toggleClass('active');
      });

	$(document).on('click','.JqDiscussClose', function(event)
	{
		$(this).parents('.JqJumpMsgBox').removeClass('active');
		$(this).parents('.JqWindowBox').removeClass('active');
		$('.JqMoreBlock ').removeClass('active');
	});

	// 刪除
	$(document).on('click','.JqDelete', function(event)
	{
		event.preventDefault();
		$('.JqJumpMsgBox[data-showmsg=delete]').removeClass('active');
		var sUrl = $(this).attr('href');
		// var sUrl = $(this).attr('data-act');

		if (sUrl != '')
		{
			fetch(sUrl, {
				method: 'post',
				body: '',
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
					// $('.JqDiscussAct').removeClass('active');
					// $('div[data-delete=1]').closest('.JqBlock').remove();
					// $(this).closest('.JqBlock').remove();
					$('.JqDiscuss').find('[data-id='+result.aData.nId+']').remove();
				}
				// else
				// {
				// 	$('.JqDelete').attr('data-act', '');
				// }

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
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