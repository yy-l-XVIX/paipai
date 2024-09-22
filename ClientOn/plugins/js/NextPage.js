$(document).ready(function()
{
	locked = false;
});

function DoNextPage(data)
{
	if (data != undefined && !locked)
	{
		$('.JqLoading').addClass('active'); // loading gif show
		LoadNextData(data);
	}
}
function LoadNextData(data)
{
	locked = true;

	nPageNo = data.nPageNo;
	sUrl = data.sUrl;
	if ( sUrl == undefined )
	{
		return false;
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
		var 	sTemlplate = data.sTemlplateHtml,
			LPsTemplate = '',
			LPsReplaceData = '',
			sHtml = '';

		if(result.nStatus == 1)
		{
			$.each(result.aData.aData,function(LPnId, LPaData)
			{
				LPsTemplate = sTemlplate;
				$.each(LPaData,function(LPsKey, LPsDetailData)
				{
					LPsReplaceData = LPsDetailData;

					// if (LPsKey == 'sHeadImage' || LPsKey == 'sImgUrl')
					// {
					// 	LPsReplaceData = '<img src="'+LPsDetailData+'">';
					// }
					if (LPsKey == 'sImgUrl')
					{
						LPsReplaceData = '<img src="'+LPsDetailData+'">';
					}
					if (LPsKey == 'aImgUrl') // 多張圖
					{
						LPsReplaceData = '';
						$.each(LPsDetailData, function(index, LPsImgUrl)
						{
							LPsReplaceData += '<img src="'+LPsImgUrl+'">';
						});
					}
					if (LPsKey == 'sScore') // 評分
					{
						LPsReplaceData = '';
						for (var i = 0; i < 5; i++)
						{
							if (LPsDetailData <= i)
							{
								LPsReplaceData += '<div class="infScore JqStar"><img src="images/score.png"></div>';
							}
							else
							{
								LPsReplaceData += '<div class="infScore JqStar"><img src="images/scoreActive.png"></div>';
							}
						}
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
				$('.JqAppend').append(sHtml);
			}

		}, 100);

	}).catch( err => {
		console.log(`Reject ${err}`);
	})
}
