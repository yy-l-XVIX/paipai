$(document).ready(function()
{
	$('title').html($('.JqHeaderTit').text());

	// 點選照片
	$('.JqPhotoBtnZoom').on('click' , function()
	{
		var ImgUrl = $(this).find('img').attr('src');
		$('.JqPhotoZoomBox').addClass('active');
		$('.JqZoomImg').attr('src',ImgUrl);
	});
});