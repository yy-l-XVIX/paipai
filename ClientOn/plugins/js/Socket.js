	let aMember = JSON.parse($('.JqGroupMember').val());
	let nUid =  parseInt($('input[name=nUid]').val()); // self user id
	let nGid = parseInt($('input[name=nGid]').val()); // group id
	var Height = $(".JobListChatBox").height();

	$('html,body').animate({scrollTop:Height},0);


	//先建立 main server 的 Photon Interface 物件
	////tst.lineme.top
	let Ms_Pi = new PhotonController.PhotonIf(Photon.ConnectionProtocol.Wss, "ssl.paipaisss.com:23651");
	//建立 main server 的 command logic 物件
	let Ms_CMD_Logic = new BaseCmdLogic.MainSrvCmdLogic(Ms_Pi);
	//初始設定 Photon Interface 物件的 Callback Function
	Ms_Pi.InitCallbackFunction(Ms_CMD_Logic, Ms_CMD_Logic.PeerStatusCallback, Ms_CMD_Logic.ResponseCallback, Ms_CMD_Logic.EventCallback);

	$(document).ready(function()
	{
		//連線server
		Ms_CMD_Logic.RunConnect(aMember[nUid]['sAccount'],'',nUid,nGid,aMember[nUid]['sToken']);
		Ms_CMD_Logic.SetGameCmdFunc(ProcessMainSrvCmd);
	});

	//點擊發送
	$('.JqSend').on('click', function(event)
	{
		var reg = new RegExp('<(br|div|\/div)>','g');
		var sContent0 = $('.JqChat').html();
		var sentence =[sContent0];
		var aMatch = CheckKeywords(sentence);
		var sHtml = $('.JqCopySelfMsg').html();

		// 敏感字阻擋
		if (aMatch.length > 0)
		{
			$.each(aMatch, function(index, keywords)
			{
				var LPreg = new RegExp(keywords,'g');
				sContent0 = sContent0.replace(LPreg,'<span style="color:#ff0000;">'+keywords+'</span> ');
			});
			$('.JqChat').html(sContent0);
			$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(aJSDEFINE['SNOOZEKEYWORDS']);
			$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
			$('.JqMsgIptBox').removeClass('active');
			$('.JqEmojiImgBox').removeClass('active');
		}
		else
		{
			sContent0 = sContent0.replace(/<img class="EmojiImgIcon" src="images\/emoji\/(\d{1,3})\.png">/g, '[:$1:]');
			if (sContent0.length > 0 || $('input[name=nImgCount').val() > 0)
			{

				if (sContent0.length > 0) // 文字訊息
				{
					Ms_CMD_Logic.SendMessage(sContent0);

					var data=[0,nUid,aMember[nUid]['sName0'],$('.JqChat').html(),nGid];
					$(".JqShowArea").append(SayMessage(data));
					$('.JqChat').empty();
				}

				if ($('input[name=nImgCount').val() > 0) // 圖片訊息
				{
					// 先上傳再寄 url
					UploadFile();

					$('input[name=nImgCount').val(0); // 圖片歸0
					$('.JqFile.DisplayBlockNone').remove();
					$('.JqEmojiContentPhotoBox').empty();
					$('.JqEmojiContentPhotoBox').removeClass('active');
				}

				//滑到底
				$('html').animate({scrollTop:$('html').height()}, 333);
				$('body').removeClass('active');
				$('.JqMsgIptBox').removeClass('active');
				$('.JqEmojiImgBox').removeClass('active');
				$('.JqPhotoOtherBox').removeClass('active'); // 因上傳圖片會擋住原本對話,所以塞個class給他,但送出後必須拉掉
			}
		}
	});

	function ProcessMainSrvCmd(vals, pi)
	{
		// console.log('----');
		// console.log(vals);

		switch(vals[0])
		{
			case 103:
			// 接收訊息
				if ((vals[6] == '0' || vals[6] == nUid) && vals[4] == nGid && aMember[nUid]['nGroupStatus'] == 1)
				{
					$(".JqShowArea").append(SayMessage(vals));
					$(".JqBtnDown").addClass('active');// scroll down
				}
				break;
			case 106:
			// 成員變動
				location.reload();
				// if (vals[1] == 1 && aMember[vals[2]] === undefined) // 新成員加入
				// {
				// 	aMember[vals[2]]['nId']		 = vals[2];
				// 	aMember[vals[2]]['sAccount']	 = vals[3];
				// 	aMember[vals[2]]['sHeadImage'] = vals[4];
				// }
				// if (vals[1] == 0) // 成員離開
				// {
				// 	location.reload();
				// }
				break;
		}
	}

	// Say message
	function SayMessage(data)
	{
		//參數1:發話者id ,參數2:發話者暱稱，參數3:聊天文字內容 ，參數4：group_id（驗證用） ，參數5: 訊息時間 ，參數6: 指定可看會員id(0:大家可看)
		var sHtml = $('.JqCopyOtherMsg').html();
		if (nUid == data[1])
		{
			sHtml = $('.JqCopySelfMsg').html();
		}
		if (data[3] == '[:invite job:]')
		{
			sHtml = $('.JqCopyInviteMsg').html();
		}

		data[3] = data[3].replace(/\[:?(\d{1,3}):\]/g, '<img class="EmojiImgIcon" src="images/emoji/$1.png">');

		if (typeof data[5] === 'undefined')
		{
			var myDate = new Date();
			//獲取當前年
			var year=myDate.getFullYear();
			//獲取當前月
			var month=myDate.getMonth()+1;//月份記得+1
			//獲取當前日
			var date=myDate.getDate();
			var h=myDate.getHours();	//獲取當前小時數(0-23)
			var m=myDate.getMinutes();	//獲取當前分鐘數(0-59)
			var s=myDate.getSeconds();
			data[5] = year+'/'+month+'/'+date+' '+h+':'+m+':'+s;
		}

		sHtml = sHtml.replace('[[::nUid::]]',data[1]);
		sHtml = sHtml.replace('[[::sName0::]]',aMember[data[1]]['sName0']);
		sHtml = sHtml.replace('[[::sMsg::]]',data[3]);
		sHtml = sHtml.replace('[[::sHeadImage::]]',aMember[data[1]]['sHeadImage']);
		sHtml = sHtml.replace('[[::sCreateTime::]]',data[5]);

		return sHtml;
	}

	function UploadFile()
	{
		var myDate = new Date();
		var nTime = myDate.getTime();
		var sUrl = $('#JqImageForm').attr('action');
		if (true)
		{
			var data=[0,nUid,aMember[nUid]['sName0'],'<div class="JqUploading" data-time="'+nTime+'"><div class="MarginBottom10">'+aJSDEFINE['IMGUPLOADING']+'...</div> <div class="barouter Jqouter"><div class="barinner Jqinner"></div></div></div>',nGid];
			$(".JqShowArea").append(SayMessage(data));
			$(this).addClass('active');

			$.ajax({
				url: sUrl,
				type: "POST",
				dataType: "json",
				data: new FormData(document.getElementById('JqImageForm')),
				processData: false,
				contentType : false,
				xhr: function() {
					myXhr = $.ajaxSettings.xhr();
					if (myXhr.upload)
					{
						myXhr.upload.addEventListener('progress', function (e){
							var inner = $('.JqShowArea').find('.JqUploading[data-time='+nTime+']').find(".Jqinner")[0];
							var maxwidth = parseInt($('.Jqouter').width());
							if (e.lengthComputable)
							{
								inner.style.width = ((e.loaded / e.total) * (maxwidth-2)) + 'px';
							}
						}, false);
					}
					return myXhr;
				},
				success: function (result)
				{
					var maxwidth = parseInt($('.Jqouter').width());
					$('.JqShowArea').find('.JqUploading[data-time='+nTime+']').find('.Jqinner').width(maxwidth+'px');
					if (result.nStatus == '1')
					{
						var sContent = '';
						$.each(result.aData, function(index, LPsUrl)
						{
							/* iterate through array or object */
							sContent += '<img src="'+LPsUrl+'">';
						});
						$('.JqShowArea').find('.JqUploading[data-time='+nTime+']').html(sContent).removeClass('JqUploading');
						Ms_CMD_Logic.SendMessage(sContent);
					}
					else
					{
						$('.JqShowArea').find('.JqUploading[data-time='+nTime+']').html(aJSDEFINE['IMGUPLOADFAILED']).removeClass('JqUploading');
						$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(result.sMsg);
						$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
					}
				},
				error: function (txt)
				{
					console.log(txt);
				}
			});

		}
	}