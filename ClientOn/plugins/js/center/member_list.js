
$(document).ready(function()
{
	// next page
	$(window).scroll(function()
	{
		totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());
		if ($(document).height() == totalheight)
		{
			data={
				sUrl: $('input[name=sFetch]').val()+'&nPageNo='+parseInt($('input[name=nPageNo]').val()),
				nPageNo: parseInt($('input[name=nPageNo]').val()),
				sTemlplateHtml: $('.JqCopy').html(),
			}
			DoNextPage(data);
		}
	});

	// 聊天
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
				if (result.sUrl != '')
				{
					location.href = result.sUrl;
				}
			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});