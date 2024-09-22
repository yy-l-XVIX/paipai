$(document).ready(function()
{
	let nowSelFriend = $('input[name=sSelectFriend]');
	// 點選項目
	$('.JqChatGroupBtn').on('click',function()
	{
		if($(this).siblings('td').find('input[type="checkbox"]').prop('checked'))
		{
			$(this).siblings('td').find('input[type="checkbox"]').prop('checked',false);
		}
		else
		{
			$(this).siblings('td').find('input[type="checkbox"]').prop('checked',true);
		}
	});

	$('.JqListSelect').on('click',function()
	{
		var Ths = $(this).find('.JqSelectFriend');
		if(Ths.prop('checked'))
		{
			nowSelFriend.val(`${nowSelFriend.val()},${Ths.data('id')}`);
		}
		else
		{
			nowSelFriend.val(nowSelFriend.val().replace(`,${Ths.data('id')}`,''));
		}
	});

	// 完成
	$('.JqHeaderBtn').on('click', function()
	{
		var sUrl = $('input[name=sAct]').val();
		var sName0 = $('input[name=sName0]').val();
		var sSelectFriend = $('input[name=sSelectFriend]').val();
		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');

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
				$(this).removeClass('active');
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');
			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});