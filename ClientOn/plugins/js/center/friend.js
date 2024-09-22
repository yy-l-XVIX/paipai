$(document).ready(function()
{
	var stop=true;
	$(window).scroll(function()
	{
		totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());

		var a =$(window).scrollTop() - 20;
		if ($(document).height() == totalheight)
		{
			if(stop==true)
			{
				$('.JqLoading').addClass('active');
				stop=false;
				var nPageNo = parseInt($('input[name=nPageNo]').val());
				var sUrl = $('input[name=sFetch]').val()+'&nPageNo='+nPageNo;

				fetch(sUrl, {
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
						$.each(result.aData,function(LPnType, LPaFriend)
						{
							var sTemlplate = $('.JqCopy'+LPnType).html();
							var LPsTemplate = '';
							var sHtml = '';

							$.each(LPaFriend,function(LPnId, LPaData)
							{
								LPsTemplate = sTemlplate;
								$.each(LPaData,function(LPsKey, LPsVal)
								{
								// if (LPsKey == 'sHeadImage')
								// {
								// 	LPsTemplate = LPsTemplate.replace('[[::'+LPsKey+'::]]','<img src="'+LPsVal+'">');
								// }
								// else
								// {

									var LPreg = new RegExp('\\[\\[::'+LPsKey+'::\\]\\]','g');
									LPsTemplate = LPsTemplate.replace(LPreg,LPsVal);

								// }
								});
								sHtml += LPsTemplate;
							});

							if (sHtml != '')
							{
								$('.JqAppend'+LPnType).append(sHtml);
							}
							if (result.nDataTotal >= nPageNo)
							{
								$('input[name=nPageNo]').val(nPageNo+1);
								stop=true;
							}
						});
						$('.JqLoading').removeClass('active');
					}
				}).catch( err => {
					console.log(`Reject ${err}`);
				})

				stop=true;
				$('.JqLoading').removeClass('active');
			}
		}
	});


	// 接受/拒絕好友邀請or刪好友
	// $('.JqAct').on('click' , function()
	$(document).on('click','.JqAct',function()
	{
		if(!$(this).data('jqurl'))
		{
			var nId = $(this).data('jqid');
			var sUrl = $('input[name=sAct]').val()+'&nId='+nId;
		}
		else
		{
			var sUrl = $(this).data('jqurl');
		}

		fetch(sUrl, {
		}).then( res => {
			if (!res.ok)
			{
				alert(res.statusText);
				throw new Error(res.statusText);
			}

			return res.json();
		}).then( result => {
			$('.JqJumpMsgContentTxt').html(result.sMsg);
			$('.JqJumpMsgBox').addClass('active');
			$(".jqJumpMsgBtn").attr('href',result.sUrl);

		}).catch( err => {
			console.log(`Reject ${err}`);
		})
	});

	// 聊天icon
	$(document).on('click','.JqGoChat',function()
	{
		var sUrl = $('input[name=sChat]').val()+'&nUid='+$(this).attr('data-id');
		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');

			fetch(sUrl, {
				method: 'post',
				body: {},
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
					location.href = result.sUrl;
				}
				else
				{
					$('.JqJumpMsgContentTxt').html(result.sMsg);
					$('.JqJumpMsgBox').addClass('active');
				}
			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});

	// 更新暱稱
	$(document).on('click','.JqNameText',function()
	{
		var sName = $(this).siblings('.JqNameUpt').find('.JqNameIpt').val();
		// 隱藏文字
		$(this).removeClass('active');
		// 顯示修改
		$(this).siblings('.JqNameUpt').addClass('active');
		$(this).siblings('.JqNameUpt').find('.JqNameIpt').val('').focus().val(sName);
	});

	$(document).on('blur','.JqNameIpt',function()
	{
		var sUrl = $(this).attr('data-act');
		sUrl += '&sName0='+$(this).val();
		fetch(sUrl, {
		}).then( res => {
			if (!res.ok)
			{
				alert(res.statusText);
				throw new Error(res.statusText);
			}

			return res.json();
		}).then( result => {
			$(this).val(result.aData.sName0);
			$(this).parents('.JqNameUpt').removeClass('active');
			$(this).parents('.JqNameUpt').siblings('.JqNameText').text(result.aData.sName0);
			$(this).parents('.JqNameUpt').siblings('.JqNameText').addClass('active');

		}).catch( err => {
			console.log(`Reject ${err}`);
		})
	});

});