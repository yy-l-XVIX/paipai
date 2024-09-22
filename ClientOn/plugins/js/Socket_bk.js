$(document).ready(function()
{
	let aMember = JSON.parse($('.JqGroupMember').val());
	let nUid = $('input[name=nUid]').val(); // self user id
	var Height = $(".JqMainBox").height();
	$('html,body').animate({scrollTop:Height},0);

	if( typeof(WebSocket) != "function" )
	{
    		$('body').html("<h1>Error</h1><p>Your browser does not support HTML5 Web Sockets. Try Google Chrome instead.</p>");
	}

	var sUrl = 'ws://103.35.207.162:6111';
	var wk;

	if(window.WebSocket)
	{
		wk = new WebSocket(sUrl);
		console.log(wk);

		//建立連線
		wk.onopen = function(event)
		{
			console.log("伺服器連線成功");
			/**
			 * 連線啟用成功後將對話進行分類
			 * 傳送此房間的ID給SERVER端
			 */

			let auth = {
				sType:'join',
				sGroupType: $('input[name=sGroupType]').val(),
				nGroupId:$('input[name=nGid]').val(),
				nUid:$('input[name=nUid]').val(),
				sName0: $('input[name=sName0]').val(),
			}
			wk.send(JSON.stringify(auth));
			console.log($('input[name=sName0]').val()+'加入聊天 發送資訊給Server:'+JSON.stringify(auth));
		}
		//異常
		wk.onerror = function(event)
		{
			console.log("伺服器連線失敗");
		}

		//關閉連線
		wk.onclose = function(event)
		{
			console.log('關閉伺服器連線');
		}

		//接收伺服端訊息
		wk.onmessage = function(event)
		{
			let aJson = JSON.parse(event.data);
			console.log('接收Server訊息 '+JSON.stringify(aJson));
			switch(aJson['sType'])
			{
				case 'join':
					console.log(aMember[aJson.nUid]['sName0']+'加入成功');
					break;
				case 'chat':
					sayMsg(aJson);
					break;
			}
		}

		//發送
		$('.JqSend').click(function(event)
		{
			if (sendMsg())
			{
				$('.JqChat').empty();
				//滑到底
				$('html').animate({scrollTop:$('html').height()}, 333);

			}
		});
		//按enter發送
		$('.JqContent0').on("keydown", function(e)
		{
			if(e.keyCode === 13 && !e.shiftKey)
			{
				e.preventDefault();
				if (sendMsg())
				{
					$('.JqChat').empty();
					//滑到底
					$('html').animate({scrollTop:$('html').height()}, 333);
				}
				return false;
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
					var aBePicked = [];
					var $sHtml = '';
					var sInviteMember = '';
					$.each($('.JqMyjobCheckbox:checked'),function(index,pick)
					{
						// send message to 人才
						let msg = {
							sType: 'chat',
							nUid: nUid,
							nTargetUid: $(pick).attr('data-id'),
							sGroupType: $('input[name=sGroupType]').val(),
							nGroupId: $('input[name=nGid]').val(),
							sMsg: '[:invite job:]',
						}
						sendMsg(msg);
						sInviteMember += aMember[$(pick).attr('data-id')]['sName0']+'<br>';
					});

					let msg = {
						sType: 'chat',
						nUid: nUid,
						nTargetUid: nUid,
						sGroupType: $('input[name=sGroupType]').val(),
						nGroupId: $('input[name=nGid]').val(),
						sMsg: '邀請<br>'+sInviteMember+'參加工作',
					}
					sendMsg(msg);

					$('.JqMyjobCheckbox').prop('checked', false);
					$(this).parents('.JqWindowBox').removeClass('active');
					$('body').css('overflow','unset');
				}
				else
				{
					location.reload();
				}
			}

			$('html').animate({scrollTop:$('html').height()}, 333);

		});

		//發送訊息
		function sendMsg(message)
		{
			if (message === undefined)
			{
				var reg = new RegExp('<(br|div|\/div)>','g');
				var sContent0 = $('.JqChat').html();
				var sentence =[sContent0];
				var aMatch = CheckKeywords(sentence);

				if (aMatch.length > 0)
				{
					$.each(aMatch, function(index, keywords)
					{
						var LPreg = new RegExp(keywords,'g');
						sContent0 = sContent0.replace(LPreg,'<div style="color:#ff0000;">'+keywords+'</div> ');
					});
					$('.JqChat').html(sContent0);
					$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(aJSDEFINE['SNOOZEKEYWORDS']);
					$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
					$('.JqMsgIptBox').removeClass('active');
					$('.JqEmojiImgBox').removeClass('active');
				}

				sContent0 = sContent0.replace(reg,'');
				if (sContent0 != '' && aMatch.length == 0)
				{
					message = {
						sType: 'chat',
						nUid: nUid,
						nTargetUid: 0,
						sGroupType: $('input[name=sGroupType]').val(),
						nGroupId: $('input[name=nGid]').val(),
						sMsg: $('.JqChat').html(),
					}
				}
			}

			if (message !== undefined)
			{
				wk.send(JSON.stringify(message));
				console.log('發送訊息 '+ JSON.stringify(message));
				return true;
			}
		}
		//放入訊息
		function sayMsg(data)
		{
			let sHtml = $('.JqCopyOtherMsg').html();
			if (nUid == data['nUid'])
			{
				sHtml = $('.JqCopySelfMsg').html();
			}
			if (data['sMsg'] == '[:invite job:]')
			{
				sHtml = $('.JqCopyInviteMsg').html();
			}
			//解析url
			data['sMsg'] = data['sMsg'].replace(/(http|https):\/\/[0-9a-z\_\/\?\&\=\%\.\;\#\-\~\+]*/i, function(url){
				if(url.indexOf(".sinaimg.cn/") < 0)
					return "<a target='_blank' href='"+url+"'>"+url+"</a>";
				else
					return url;
			});
			sHtml = sHtml.replace('[[::nUid::]]',data['nUid']);
			sHtml = sHtml.replace('[[::sMsg::]]',data['sMsg']);
			sHtml = sHtml.replace('[[::sName0::]]',aMember[data['nUid']]['sName0']);
			sHtml = sHtml.replace('[[::sCreateTime::]]',data['sCreateTime']);
			sHtml = sHtml.replace('[[::sHeadImage::]]','<img src="'+aMember[data['nUid']]['sHeadImage']+'" >');
			// 塞進聊天室窗
			$(".JqShowArea").append(sHtml);
		}
	}
	else
	{
		console.log('Browser 不支援');
	}
})