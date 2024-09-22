$(document).ready(function()
{
	// var url = location.href;
	// if (url.indexOf("http://") != '-1' )
	// {
	// 	// https 轉 http
	// 	url = url.replace('http://','https://');
	// 	location.href = url;
	// }

	// document.addEventListener('visibilitychange', function ()
	// {
	// 	var isHidden = document.hidden;
	// 	if (!isHidden)
	// 	{
	// 		alert('close');
	// 	}
	// });


	// Top按鈕出現
	$(window).scroll(function()
	{
		if($(window).scrollTop() > 400 )
		{
			$('.JqBtnTop').addClass('active');
		}
		else
		{
			$('.JqBtnTop').removeClass('active');
		}
	});
	// 點選Top
	$('.JqBtnTop').on('click' , function()
	{
		$('html').animate({scrollTop:0}, 333);
	});

	// 彈窗 - 關閉
	$('.JqClose').on('click',function()
	{

		if($(this).parents().hasClass('JqDetailTr'))
		{
			// 是否在表格裡的查看明細
			$(this).parents('.JqDetailTr').removeClass('active');
		}
		else
		{
			$(this).parents('.JqJumpMsgBox').removeClass('active');
			$(this).parents('.JqWindowBox').removeClass('active');
		}
	});

	// 彈窗 - 防呆
	$(document).on('click','.JqStupidOut',function()
	{
		$('.JqJumpMsgBox[data-showmsg="'+ $(this).data('showctrl') +'"]').addClass('active');
	});

	// 刪除鈕 防呆
	$(document).on('click','.JqReplaceS',function()
	{
		$('.JqReplaceO').attr('href',$(this).data('replace'));
	});

	// 點選訊息輸入訊息框(輸入訊息在footer)
	$('.JqMsgIptTxt').on('click',function()
	{
		if(!$('.JqMsgIptBox').hasClass('active'))
		{
			$('.JqMsgIptBox').addClass('active');
		}

		// Emoji
		if(!$('.JqEmojiImgBox').hasClass('active'))
		{
			$('.JqEmojiImgBox').addClass('active');
		}

		$('.JqBody').addClass('active');
	});
	// 訊息輸入訊息框點選背景(輸入訊息在footer)
	$('.JqMsgIptBg').on('click',function()
	{
		$('.JqMsgIptBox').removeClass('active');
		$('.JqEmojiImgBox').removeClass('active'); // Emoji
		$('.JqBody').removeClass('active');
	});

	// 點選訊息輸入訊息框(輸入訊息不在footer)
	$('.JqEditBlock').on('click',function()
	{
		$('.JqEditBox').addClass('active');
		$('.JqEditBg').addClass('active'); //背景
		$('.JqEmojiImgBox').addClass('active2'); // Emoji

		$('.JqBody').addClass('active');
	});
	// 訊息輸入訊息框點選背景(輸入訊息不在footer)
	$('.JqEditBg').on('click',function()
	{
		$('.JqEditBox').removeClass('active');
		$('.JqEditBg').removeClass('active'); //背景
		$('.JqEmojiImgBox').removeClass('active2'); // Emoji
		$('.JqBody').removeClass('active');
	});

	// 點選Emoji
	$('.JqBtnEmoji').on('click',function()
	{
		// $('.JqMsgIptBox').toggleClass('active');
		// $('.JqEmojiImgBox').toggleClass('active');
		// $('.JqMainBox').toggleClass('active');
		// $('.JqBtnTop').toggleClass('high');

		// $('.JqBody').toggleClass('active');
		$('.JqEmojiImgBox').toggleClass('active'); // Emoji
	});

	var sUrl = $('.JqCheckMessageFetch').val();
	if (sUrl !== undefined)
	{
		CheckMessage(sUrl);
		// 每10秒檢查是否有新訊息
		setInterval(function () {
			CheckMessage(sUrl)
		}, 10000);
	}

});
function CheckMessage(sUrl)
{
	// fetch
	fetch(sUrl, {
		method: 'post',
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
			if (result.aData.nReload == '1')
			{
				location.reload();
			}
			else
			{
				$.each(result.aData.aActive, function(LPsType, LPnCount)
				{
					if (LPnCount > 0) // add class
					{
						$('.JqNewMessage[data-type='+LPsType+']').addClass('active');
					}
					else
					{
						$('.JqNewMessage[data-type='+LPsType+']').removeClass('active');
					}
				});

				$.each(result.aData.aGroup, function(LPnGid, LPnCount)
				{
					$('.JqCheckGroupMessage[data-gid='+LPnGid+']').addClass('active');
				});
			}

		}
	}).catch( err => {
		console.log(`Reject ${err}`);
	})
}

function CheckKeywords(sentence)
{
	const matched = [];
	for (var index = 0; index < sentence.length; index++)
	{
		for (var outerIndex = 0; outerIndex < aSNOOZEKEYWORDS.length; outerIndex++)
		{
			if (sentence[index].includes(aSNOOZEKEYWORDS[outerIndex]))
			{
				matched.push(aSNOOZEKEYWORDS[outerIndex]);
			}
		}
	}
	return matched;
}
function GetCookie(name)
{
	var sCookieName = name + "=";
	var aCookie = document.cookie.split(';');
	for(var i=0;i < aCookie.length;i++) {
		var c = aCookie[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(sCookieName) == 0) return c.substring(sCookieName.length,c.length);
	}
	return null;
}