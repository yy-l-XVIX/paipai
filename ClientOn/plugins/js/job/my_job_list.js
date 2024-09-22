$(document).ready(function()
{

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