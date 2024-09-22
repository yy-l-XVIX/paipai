$(document).ready(function()
{
	// 編輯更多
	$(document).on('click','.JqMoreBtn',function()
	{
		$(this).parents('.JqMoreBox').find('.JqMoreBlock').toggleClass('active');
	});

	// 查看評論
	$(document).on('click','.JqJobViewBtn',function()
	{
		$(this).toggleClass('active');
		$('.JqJobViewBox[data-view="'+$(this).attr('data-viewctrl')+'"]').toggleClass('active');
	});

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
});