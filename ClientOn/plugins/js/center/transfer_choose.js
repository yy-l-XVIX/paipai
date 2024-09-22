$(document).ready(function()
{
	// 點選項目
	$('.JqChatGroupBtn').on('click',function()
	{
		if(!$(this).siblings('td').find('input[type="radio"]').prop('checked'))
		{
			$(this).siblings('td').find('input[type="radio"]').prop('checked',true);
		}
	});

	// 點完成
	$('.JqHeaderBtn').click(function(event)
	{
		var sUrl = $('input[name=sTransfer]').val();

		if ($('input[name=sSelectFriend]:checked').val() !== undefined)
		{
			sUrl += '&sAccount='+$('input[name=sSelectFriend]:checked').val();
		}
		location.href=sUrl;
	});

	// next page
	var stop=true;
	$(window).scroll(function()
	{
		totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());

		var a =$(window).scrollTop() - 20;
		if ($(document).height() == totalheight)
		{
			if(stop==true)
			{
				stop=false;
				$('.JqLoading').addClass('active');

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
					var sTemlplate = $('.JqCopy').html();
					var LPsTemplate = '';
					var sHtml = '';

					if(result.nStatus == 1)
					{
						$.each(result.aData.aData,function(LPnId, LPaData)
						{
							LPsTemplate = sTemlplate;

							$.each(LPaData,function(LPsKey, LPsData)
							{
								if (LPsKey == 'sHeadImage')
								{
									LPsTemplate = LPsTemplate.replace('[[::'+LPsKey+'::]]','<img src="'+LPsData+'">');
								}
								else
								{

									var LPreg = new RegExp('\\[\\[::'+LPsKey+'::\\]\\]','g');
									LPsTemplate = LPsTemplate.replace(LPreg,LPsData);
								}
							});
							sHtml += LPsTemplate;
						});

						if (result.aData.nDataTotal > nPageNo)
						{
							$('input[name=nPageNo]').val(nPageNo+1);
							stop=true;
						}
					}
					setTimeout(function(){
						$('.JqLoading').removeClass('active');
						if (sHtml != '')
						{
							$('.JqAppend').append(sHtml);
						}

					}, 500);

				}).catch( err => {
					console.log(`Reject ${err}`);
				})
			}
		}
	});
});