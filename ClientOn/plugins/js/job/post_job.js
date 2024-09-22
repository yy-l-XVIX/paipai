$(document).ready(function()
{
	laydate.render({ elem: '.JqStartTime', type: 'datetime' });
	laydate.render({ elem: '.JqEndTime', type: 'datetime' });

	// 發文分類選擇
	$('.JqPostJobBtnKind').on('click' , function()
	{
		$('.JqPostJobBtnKind').removeClass('active');
		if($(this).find('input[type="radio"]').prop('checked'))
		{
			$(this).addClass('active');
		}
	});

	// 工作地點選擇
	$('.JqChangeCity').change(function()
	{
		var sUrl = $('input[name=sAct]').val();
		var sJWT = $('input[name=sChangeCityJWT]').val();
		var sHTML = '';
		sUrl = sUrl+'&sJWT='+sJWT;
		fetch(sUrl, {
			method: 'post',
			body: 'nId='+$(this).val(),
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
				$.each(result.aData, function(LPnI, LPaArea)
				{
					sHTML += '<option value="'+LPaArea.nId+'">'+LPaArea.sName0+'</option>';
				});
				$('.JqChangeArea').empty().append(sHTML);
			}
			else
			{
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');
			}
		}).catch( err => {
			console.log(`Reject ${err}`);
		})
	});

	$('.JqContent0').on('focus', function()
	{
		$('.JqEmojiImgBox').addClass('active');
	});

	// $('.JqContent0').on('blur', function()
	// {
	// 	$('.JqEmojiImgBox').removeClass('active');
	// });

	$('.JqSubmit').on('click', function ()
	{

		$('input[name=sContent0]').val($('.JqContent0').html());
		var sUrl = $('#JqPostForm').attr('action');
		var sContent0 = $('.JqContent0').html();
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
			$('.JqMsgIptBox').removeClass('active');
			$('.JqEmojiImgBox').removeClass('active');
		}

		if (!$(this).hasClass('active') && aMatch.length == 0)
		{
			$(this).addClass('active');
			$('.JqJumpMsgBox[data-showmsg=dataprocessing]').addClass('active');

			fetch(sUrl, {
				method: 'post',
				body: new FormData(document.getElementById('JqPostForm'))
			}).then(res => {
				if (!res.ok) {
					alert(res.statusText);
					throw new Error(res.statusText);
				}

				return res.json();
			}).then(result => {

				if(result.nStatus == 1)
				{
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqRedirectClose').attr('href',result.sUrl);
				}
				$(this).removeClass('active');
				$('.JqJumpMsgBox[data-showmsg=dataprocessing]').removeClass('active');
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');

			}).catch(err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});