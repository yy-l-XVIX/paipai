$(document).ready(function()
{

	// 操作
	$(document).on('click' ,'.JqDiscussBtnAct', function()
	{
		$('.JqDiscussAct').addClass('active');
	});

	// 發送留言
	$('.JqReply').on('click',  function()
	{
		var bPass = true;
		var sUrl = $('input[name=sReply]').val();
		var sContent0 = $('.JqContent0').html();
		var sentence =[sContent0];
		var aMatch = CheckKeywords(sentence);

		$('.JqMsgIptBox').removeClass('active');
		$('.JqEmojiImgBox').removeClass('active');

		if (aMatch.length > 0)
		{
			$.each(aMatch, function(index, keywords)
			{
				var LPreg = new RegExp(keywords,'g');
				sContent0 = sContent0.replace(LPreg,'<spen style="color:#ff0000;">'+keywords+'</spen> ');
			});
			$('.JqContent0').html(sContent0);
			$('.JqJumpMsgBox[data-showmsg=1]').find('.JqJumpMsgContentTxt').html(aJSDEFINE['SNOOZEKEYWORDS']);
			$('.JqJumpMsgBox[data-showmsg=1]').addClass('active');

			bPass = false;
		}
		if (sContent0 == '' && $('.JqFile').val() == '')
		{
			bPass = false;
		}

		if (bPass && !$(this).hasClass('active') )
		{
			$('.JqJumpMsgBox[data-showmsg=dataprocessing]').addClass('active');
			$('input[name=sContent0]').val(sContent0);
			$(this).addClass('active');
			fetch(sUrl, {
				method: 'post',
				body: new FormData(document.getElementById('JqReplyForm')),

			}).then( res => {

				if (!res.ok)
				{
					alert(res.statusText);
	 				throw new Error(res.statusText);
				}

				return res.json();
			}).then( result => {
				$('.JqJumpMsgBox[data-showmsg=dataprocessing]').removeClass('active');
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

	// 刪除文章
	// $('.JqDiscussDelete').on('click', function()
	// {
	// 	$('div').removeAttr('data-delete');

	// 	var sUrl = $('input[name=sDel]').val()+'&nId='+$(this).attr('data-id');
	// 	$('.JqDelete').attr('data-act', sUrl);
	// 	$(this).attr('data-delete', '1');
	// });

	// 刪除回覆
	// $(document).on('click', '.JqReplyDelete', function()
	// {
	// 	$('div').removeAttr('data-delete');

	// 	var sUrl = $('input[name=sDelReply]').val()+'&nId='+$(this).attr('data-id');
	// 	$('.JqDelete').attr('data-act', sUrl);
	// 	$(this).attr('data-delete', '1');
	// });

	$(document).on('click','.JqDiscussClose', function(event)
	{
		$(this).parents('.JqJumpMsgBox').removeClass('active');
		$(this).parents('.JqWindowBox').removeClass('active');
		$('.JqMoreBlock ').removeClass('active');
	});

	// 刪除文章
	$(document).on('click', '.JqMoreBtn', function()
      {
            $(this).parents('.JqMoreBox').find('.JqMoreBlock').toggleClass('active');
      });

	// 刪除
	$(document).on('click','.JqDelete', function(event)
	{
		event.preventDefault();
		$('.JqJumpMsgBox[data-showmsg=delete]').removeClass('active');
		var sUrl = $(this).attr('href');

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
					$('.JqJumpMsgBox[data-showmsg=0]').find('.JqRedirectClose').attr('href',result.sUrl);
				}
				$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');

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
