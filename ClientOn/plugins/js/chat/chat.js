$(document).ready(function()
{
	// $('html').animate({scrollTop:$('html').height()}, 1);
	// 點選Down
	$('.JqBtnDown').on('click' , function()
	{
		$('html').animate({scrollTop:$('html').height()}, 333);
		$('.JqBtnDown').removeClass('active');
	});

	// Down按鈕出現
	var begin = $(window).scrollTop();
	$(window).scroll(function()
	{
		// alert($(window).scrollTop()+' | '+begin);
		if($(window).scrollTop() < begin )
		{
			$('.JqBtnDown').addClass('active');
		}
		else
		{
			if ($(window).scrollTop() > begin)
			{
				begin = $(window).scrollTop();
			}
			$('.JqBtnDown').removeClass('active');
		}
	});

	var sUrl = $('.JqHeaderLink').attr('href')+'&nGid='+$('input[name=nGid]').val();
	$('.JqHeaderLink').attr('href', sUrl);
	$('.JqHeaderTit').text($('.JqChange').val());

	// 加入/拒絕群組
	$('.JqActBtn').on('click', function()
	{
		var sUrl = $(this).attr('data-act');
		if (!$(this).hasClass('active'))
		{
			$('.JqActBtn').addClass('active');

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
					if ($(this).hasClass('JqJoin'))
					{
						// 加入群組
						Ms_CMD_Logic.JoinGroup(nUid);
					}
					location.href=result.sUrl;
				}
				else
				{
					$(this).removeClass('active');
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqRedirectClose').attr('href',result.sUrl);
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');
				}

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});

	// 較早之前訊息
	$(window).scroll(function()
	{
		if ($(window).scrollTop() == '0' && !locked) // 到頂載入舊訊息
		{

			var sUrl = $('input[name=sFetch]').val()+'&nPageNo='+parseInt($('input[name=nPageNo]').val());
			var nPageNo = parseInt($('input[name=nPageNo]').val());
			locked = true;

			fetch(sUrl, {
			}).then( res => {
				if (!res.ok)
				{
					alert(res.statusText);
					throw new Error(res.statusText);
				}
				return res.json();
			}).then( result => {
				var 	LPsTemplate = '',
					LPsReplaceData = '',
					sHtml = '';

				if(result.nStatus == 1)
				{
					$.each(result.aData.aData,function(LPnId, LPaData)
					{

						LPsTemplate = $('.JqCopyOtherMsg').html();
						if (nUid == LPaData['nUid']) // 自己的訊息
						{
							LPsTemplate = $('.JqCopySelfMsg').html();
						}
						if (LPaData['sMsg'] == '[:invite job:]') // 邀請工作
						{
							LPsTemplate = $('.JqCopyInviteMsg').html();
						}


						$.each(LPaData,function(LPsKey, LPsDetailData)
						{
							LPsReplaceData = LPsDetailData;

							if ( LPsKey == 'sImgUrl')
							{
								LPsReplaceData = '<img src="'+LPsDetailData+'">';
							}

							var LPreg = new RegExp('\\[\\[::'+LPsKey+'::\\]\\]','g');
							LPsTemplate = LPsTemplate.replace(LPreg,LPsReplaceData);
						});

						sHtml += LPsTemplate;
					});

					if (result.aData.nDataTotal > nPageNo)
					{
						$('input[name=nPageNo]').val(nPageNo+1);
						locked = false;
					}
				}
				setTimeout(function(){
					$('.JqLoading').removeClass('active');
					if (sHtml != '')
					{
						$('.JqAppend').prepend(sHtml);
					}

				}, 100);

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});

// document.addEventListener('visibilitychange', function ()
// {
// 	var isHidden = document.hidden;
// 	if (!isHidden)
// 	{
// 		location.reload();
// 	}
// });
