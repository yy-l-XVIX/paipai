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

			var sUrl = $('input[name=sSaveAct]').val()+'&sJWT='+JWT;
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

	// 按星星
	$(document).on('click', '.JqStar', function()
	{
		var containbox = $(this).parent('.JqScoreBox');
		var elementIndex = $(this).index();
		var i = 0;
		var nFinalScore = 0;
		var nScored = containbox.attr('data-scored');
		if (nScored == 0)
		{
			$.each(containbox.find('.JqStar'),function(index, element)
			{
				if(i <= elementIndex)
				{
					$(element).find('img').attr('src','images/scoreActive.png');
					nFinalScore++;
				} else
				{
					$(element).find('img').attr('src','images/score.png');
				}
				i++;
			});
			$('input[name=nScore]').val(nFinalScore);
		}
	});

	// 按完成
	$(document).on('click', '.JqSubmit', function()
	{
		var nJid = $(this).attr('data-jid');
		var sUrl = $('form[data-jid='+nJid+']').attr('action');
		var sContent0 = $('form[data-jid='+nJid+']').find('.JqContent0').html();
		$('form[data-jid='+nJid+']').find('input[name=sContent0]').val(sContent0);

		var sentence =[sContent0];
		var aMatch = CheckKeywords(sentence);

		if (aMatch.length > 0)
		{
			$.each(aMatch, function(index, keywords)
			{
				var LPreg = new RegExp(keywords,'g');
				sContent0 = sContent0.replace(LPreg,'<div style="color:#ff0000;">'+keywords+'</div> ');
			});
			$('form[data-jid='+nJid+']').find('.JqContent0').html(sContent0);
			$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(aJSDEFINE['SNOOZEKEYWORDS']);
			$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
			$('.JqMsgIptBox').removeClass('active');
			$('.JqEmojiImgBox').removeClass('active');
		}

		if (!$(this).hasClass('active') && aMatch.length == 0)
		{
			$(this).addClass('active');
			$('.JqJumpMsgBox[data-showmsg=dataprocessing]').addClass('active');

			fetch(sUrl, {
				method: 'post',
				body: new FormData($('form[data-jid='+nJid+']')[0])
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
				$('.JqJumpMsgBox[data-showmsg=dataprocessing]').removeClass('active');
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');

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