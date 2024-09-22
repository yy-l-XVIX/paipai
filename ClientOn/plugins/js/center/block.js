
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


	// 解封鎖

	$(document).on('click','.JqAct',function()
	{
		var nId = $(this).data('jqid');
		var sUrl = $('input[name=sAct]').val()+'&nId='+nId;

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
});