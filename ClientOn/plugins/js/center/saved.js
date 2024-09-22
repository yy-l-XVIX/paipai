$(document).ready(function()
{
	// 查看更多
	$('.JqJobViewBtn').on('click',function()
	{
		$(this).toggleClass('active');
		$('.JqJobViewBox[data-view="'+$(this).attr('data-viewctrl')+'"]').toggleClass('active');
	});

	// 我要應徵
	$(document).on('click', '.JqJoin', function()
	{
		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');

			var JWT  = $('input[name=sJoinJWT]').val();
			var sUrl = $('input[name=sAct]').val()+'&sJWT='+JWT;
			var nJid = $(this).attr('data-jid');

			fetch(sUrl, {
				method: 'post',
				body: 'nJid='+nJid,
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
				if(result.nStatus == 1)
				{
					location.href = result.sUrl;
				}
				else
				{
					$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(result.sMsg);
					$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
				}

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});

	// 我要退出
	$(document).on('click', '.JqOut', function()
	{
		var This = $(this);
		if (!$(this).hasClass('active1'))
		{
			$(this).addClass('active1');

			var JWT  = $('input[name=sOutJWT]').val();
			var sUrl = $('input[name=sAct]').val()+'&sJWT='+JWT;
			var nJid = $(this).attr('data-jid');

			fetch(sUrl, {
				method: 'post',
				body: 'nJid='+nJid,
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
				$(this).removeClass('active1');
				$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');

				if(result.nStatus == 1)
				{
					This.removeClass('active JqOut');
					This.addClass('JqJoin');
					This.text(result.aData.sBtnText);
				}

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});

	// 收藏工作
	$(document).on('click', '.JqFavorite', function()
	{
		if (!$(this).hasClass('active'))
		{
			var JWT = '';
			$(this).addClass('active');

			if ($(this).attr('data-favorite') == '1') // 已收藏=>不收藏
			{
				$(this).html('<img src="images/like.png">');
				$(this).attr('data-favorite',0);
				JWT = $('input[name=sDelJWT]').val();
			}
			else
			{
				$(this).html('<img src="images/likeActive.png">');
				$(this).attr('data-favorite',1);
				JWT = $('input[name=sActJWT]').val();
			}

			var sUrl = $('input[name=sAct]').val()+'&sJWT='+JWT;
			var nJid = $(this).attr('data-jid');

			fetch(sUrl, {
				method: 'post',
				body: 'nJid='+nJid,
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
				if(result.nStatus == 1)
				{
					location.reload();
				}
			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});

	// 防呆
	$(document).on('click', '.JqListStupidOut', function()
	{
		$('.JqJumpMsgBox[data-showmsg="'+ $(this).data('showctrl') +'"]').addClass('active');
		$('.JqJumpMsgBox[data-showmsg="'+ $(this).data('showctrl') +'"]').find('.JqJoin').attr('data-jid', $(this).attr('data-jid'));
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