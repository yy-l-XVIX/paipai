$(document).ready(function()
{
	$('.JqContent0').on('focus', function()
	{
		$('.JqEmojiImgBox').addClass('active');
	});

	$('.JqSubmit').on('click' , function()
	{
		var bPass = true;
		var sUrl = $('input[name=sAct]').val();
		var sContent0 = $('.JqContent0').html();
		$('input[name=sContent0]').val(sContent0);

		var sentence =[sContent0];
		var aMatch = CheckKeywords(sentence);

		if (aMatch.length > 0)
		{
			$.each(aMatch, function(index, keywords)
			{
				var LPreg = new RegExp(keywords,'g');
				sContent0 = sContent0.replace(LPreg,'<div style="color:#ff0000;">'+keywords+'</div> ');
			});
			$('.JqContent0').html(sContent0);
			$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(aJSDEFINE['SNOOZEKEYWORDS']);
			$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
			$('.JqEmojiImgBox').removeClass('active');

			bPass = false;
		}
		if (sContent0 == '' && $('.JqFile').val() == '')
		{
			bPass = false;
		}

		if (bPass && !$(this).hasClass('active'))
		{
			$('.JqJumpMsgBox[data-showmsg=dataprocessing]').addClass('active');
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
				$('.JqJumpMsgBox[data-showmsg=dataprocessing]').removeClass('active');
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');
			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});