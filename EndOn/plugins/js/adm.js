$(document).ready(function()
{
	// 空白鍵/ENTER 關閉彈窗
	$(document).on('keydown',function(e){
		// 13 => ENTER, 32 => SPACE
		if ($('.JqJumpMsgBox').hasClass('active') && (e.keyCode == 13 || e.keyCode == 32))
		{
			$('.JqRedirectClose')[0].click();
		}
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
		}
	});

	// 彈窗 - 防呆
	$('.JqStupidOut').on('click',function()
	{
		$('.JqJumpMsgBox[data-showmsg="'+ $(this).data('showctrl') +'"]').addClass('active');
	});

	// 刪除鈕 防呆
	$('.JqReplaceS').on('click',function()
	{
		$('.JqReplaceO').attr('href',$(this).data('replace'));
	});

	// Header選單按鈕
	$('.JqHeaderBtnNav').on('click',function()
	{
		$('.JqHeader').toggleClass('active');
		$('.JqNav').toggleClass('active');
		$('.JqNavContentContainer').toggleClass('active');
	});

	// 主目錄按鈕
	$('.JqNavMenuBtn').on('click',function()
	{
		$(this).toggleClass('active');
	});

	// 自己&對應 show,其他hide
	$('.JqBtnShowOnly').on('click',function()
	{
		$('.JqBtnShowOnly').removeClass('active');
		$(this).addClass('active');
		if($('[data-show]').length > 0)
		{
			$('[data-show]').removeClass('active');
			$('[data-show="'+ $(this).data('showctrl') +'"]').addClass('active');
		}
	});

	// 自己&對應 toggleClass
	$('.JqBtnShowHide').on('click',function()
	{
		$(this).toggleClass('active');
		if($('[data-show]').length > 0)
		{
			$('[data-show="'+ $(this).data('showctrl') +'"]').toggleClass('active');
		}
	});

	// 自己toggleClass
	$('.JqBtnToggleClass').on('click',function()
	{
		$(this).toggleClass('active');
	});


	if(document.cookie.match('soundCtrl=1'))
	{
		// 聲音提示
		var sSoundUrl = $('input[name=sSoundUrl]').val();
		var sSoundJWT = $('input[name=sSoundJWT]').val();

		setInterval(function () {
			$.ajax({
				url: sSoundUrl,
				type: 'POST',
				dataType: 'json',
				data: {
					'sJWT': sSoundJWT,
				},
				success: function (oRes)
				{
					var oPlay = [];
					if (oRes.nErr == 0)
					{
						if (oRes.sRingClass != 'all')
						{
							let aTmp = oRes.sRingClass.split(',');
							$.each(aTmp, function(LPnIndex, LPsSound){
								if(LPsSound != '')
								{
									oPlay.push($('#sound' + LPsSound));
								}
							});
						}
						else
						{
							oPlay = [$("#soundRecharge"), $("#soundWithdrawal"), $("#soundService")];
						}

						$.each(oPlay, function (LPnIndex, LPoEle)
						{
							const playPromise = $(LPoEle)[0].play();
							$(LPoEle)[0].pause();
							if (playPromise !== undefined)
							{
								var sTarget = $(LPoEle).data('tar');
								playPromise.then(_ => {
									// 这个时候可以安全触发
									if ($('input[name=sound' + sTarget + ']').is(':checked'))
									{
										$(LPoEle)[0].play();
									}
									else
									{
										$(LPoEle)[0].pause();
									}
								})
								.catch(err => {
									console.log('err'+err);
								});
							}
						});
					}
				},
			});
		}, 10000);
	}

	// 聲音開關
	$('.JqSoundCtrl').on('click', function () {
		var This = $(this);

		if (This.hasClass('active'))
		{
			document.cookie = 'soundCtrl=0';
		}
		else
		{
			document.cookie = 'soundCtrl=1';
		}

		This.toggleClass('active');
	});

	// 各類聲音提示開關
	$('.JqSoundOptionCtrl').on('click', function () {
		var This = $(this);

		var ChildInput = $(This.parent().siblings('input')[0]);
		if (ChildInput.is(':checked'))
		{
			ChildInput.removeAttr('checked');
			document.cookie = 'sound' + ChildInput.data('tar') + '=0';
			$('#sound' + ChildInput.data('tar'))[0].pause();
		}
		else
		{
			ChildInput.attr('checked', true);
			document.cookie = 'sound' + ChildInput.data('tar') + '=1';
		}
	});
});
