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
		if($(window).scrollTop() < begin )
		{
			$('.JqBtnDown').addClass('active');
		}
		else
		{
			$('.JqBtnDown').removeClass('active');
		}
	});

	// 選擇人才視窗
	$('.JqMyjobBtnPick').on('click' , function()
	{
		$('.JqMyjobPickBox').addClass('active');
		$('body').css('overflow','hidden');
	});

	$('.JqMyjobCheckbox').on('click',function()
	{
		if($('.JqMyjobCheckbox:checked').length>0)
		{
			$('.JqHeadConfirm').addClass('active');
		}
		else
		{
			$('.JqHeadConfirm').removeClass('active');
		}
	});
	// 選擇人才checkbox
	$('.JqMyjobPickBtn').on('click',function()
	{
		if($(this).siblings('td').find('input[type="checkbox"]').prop('checked'))
		{
			$(this).siblings('td').find('input[type="checkbox"]').prop('checked',false);
		}
		else
		{
			$(this).siblings('td').find('input[type="checkbox"]').prop('checked',true);
		}

		if($('.JqMyjobCheckbox:checked').length>0)
		{
			$('.JqHeadConfirm').addClass('active');
		}
		else
		{
			$('.JqHeadConfirm').removeClass('active');
		}
	});

	//邀請參加(job)
	$('.JqInviteO').on('click', function()
	{
		if($('.JqMyjobCheckbox:checked').length>0)
		{
			var nNotBePicked = $('.JqMyjobNotBePicked').length;
			if(nNotBePicked>=$('.JqMyjobCheckbox:checked').length)
			{
				var sInviteMember = '';
				$.each($('.JqMyjobCheckbox:checked'),function(index,pick)
				{
					// send message to 人才
					sInviteMember += aMember[$(pick).attr('data-id')]['sName0']+'<br>';
					Ms_CMD_Logic.SendMessage('[:invite job:]',$(pick).attr('data-id'));
				});

				$('.JqJumpMsgBox[data-showmsg=kindremind]').removeClass('active');
				$('.JqMyjobCheckbox').prop('checked', false);
				$('[data-showctrl=kindremind]').parents('.JqWindowBox').removeClass('active');
				$('body').css('overflow','unset');
			}
			else
			{
				location.reload();
			}
		}
	});

	// 踢出群組
	$('.JqKickOut').on('click', function (event)
	{
		event.preventDefault();
		$('.JqJumpMsgBox[data-showmsg=2]').removeClass('active');
		var sJWT = $('input[name=sKickOutJWT]').val();
		var sUrl = $(this).attr('href')+'&sJWT='+sJWT;

		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');

			fetch(sUrl, {
				method: 'post',
				body: '',
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
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqRedirectClose').attr('href',result.sUrl);
				}
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});

	// 人才接受工作(示意)
	$('.JqChatBox').on('click','.JqMyjobBtnAccept,.JqMyjobBtnDeny' , function()
	{
		var nNotBePicked = $('.JqMyjobNotBePicked').length;
		// 新增
		var This = $(this);
		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');

			var sUrl = $('input[name=sPageAct]').val()+'&sJWT='+$(this).attr('data-jwt');
			var nJid = $('input[name=nGid]').val();

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
				if (result.nStatus == '1')
				{
					This.siblings('.JqInviteBtn').remove();
					// 接受工作
					if (This.hasClass('JqMyjobBtnAccept'))
					{
						$('.JqMyjobNotBePicked').remove();
						var $sHtml = '';
						$sHtml +=  '<a href="'+This.attr('data-href')+'" class="selfieBox JqMyjobBePicked BG" style="background-image: url(\''+$('.JqMyHeadImage').attr('data-headimg')+'\')"></a>';
						// 還剩幾個未選擇
						for($nAdd=1;$nAdd<nNotBePicked;$nAdd++)
						{
							$sHtml += '<div class="myjobChooseUserEmpty JqMyjobNotBePicked"></div>';
						}
						$('.JqMyjobBePickedBox').append($sHtml);
						Ms_CMD_Logic.JoinGroup(nUid);
						var timmer = null;
						var count = 0;
						timer = setInterval(function()
						{
							count += 1;
							if(count > 2 )
							{
								Ms_CMD_Logic.SendMessage(result.aData.sSendMsg,result.aData.nBossId); // 通知雇主
								clearInterval(timer);
							}
						},1000);
					}
				}
				else
				{
					$(this).removeClass('active');
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');
				}

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});

	// 關閉
	$('.JqClose').on('click' , function()
	{
		$(this).parents('.JqWindowBox').removeClass('active');
		$('body').css('overflow','unset');
	});

	// 我要退出
	$('.JqOut').on('click', function()
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

				if(result.nStatus == 1)
				{
					// Ms_CMD_Logic.SendMessage(aMember[nUid]['sName0']+aJSDEFINE['OUTJOB']); // 通知大家我要退出
					Ms_CMD_Logic.LeaveGroup(nUid); // 通知大家我要退出
					// location.href = result.sUrl;
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqRedirectClose').attr('href',result.sUrl);
				}
				// else
				// {
				// 	$('.JqJumpMsgContentTxt').html(result.sMsg);
				// 	$('.JqJumpMsgBox').addClass('active');
				// }

				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});

	// 收藏工作
	$('.JqFavorite').on('click', function()
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
					$(this).removeClass('active');
				}
			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});

	// 結案
	$('.JqCloseJob').on('click', function(event)
	{
		event.preventDefault();
		$('.JqJumpMsgBox[data-showmsg=3]').removeClass('active');
		var sJWT = $('input[name=sCloseJobJWT]').val();
		var sUrl = $(this).attr('href')+'&sJWT='+sJWT;
		var This = $(this);
		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');

			fetch(sUrl, {
				method: 'post',
				body: '',
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
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').addClass('active');
				// $('.JqJumpMsgContentTxt').html(result.sMsg);
				// $('.JqJumpMsgBox').addClass('active');
				if(result.nStatus == 1)
				{
					Ms_CMD_Logic.SendMessage(aJSDEFINE['CLOSEDJOB']); // 通知大家結案
					// location.href = result.sUrl;
					$('.JqJumpMsgBox[data-showmsg='+result.nStatus+']').find('.JqRedirectClose').attr('href',result.sUrl);
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

							if (LPsKey == 'sImgUrl')
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