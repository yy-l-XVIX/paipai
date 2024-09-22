$(document).ready(function()
{
	if ($('.JqChangeRole').attr('data-url') != '')
	{
		$('.JqHeaderLink').attr('href', $('.JqChangeRole').attr('data-url'));
		$('.JqHeaderLink').text($('.JqChangeRole').attr('data-role'));
	}
	else
	{
		$('.JqHeaderLink').remove();
	}
	// 點選影片
	$('.JqVideoBtnZoom').on('click' , function()
	{
		var VideoUrl = $(this).find('video').attr('src');
		var DelUrl = $(this).find('video').attr('data-act');

		$('.JqVideoZoomBox').addClass('active');
		$('.JqZoomVideo').attr('src',VideoUrl);
		$('.JqDelVideo').attr('data-act',DelUrl);
	});

	//完成
	$('.JqSubmit').on('click', function ()
	{
		let sUrl = $('#JqPostForm').attr('action');
		$('.JqJumpMsgBox[data-showmsg=dataprocessing]').find('.JqJumpMsgContentTxt').append('<div class="TextAlignCenter"><div class="barouter Jqouter"><div class="barinner Jqinner"></div></div></div>');
		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');
			$('.JqJumpMsgBox[data-showmsg=dataprocessing]').addClass('active');

			$.ajax({
				url: sUrl,
				type: "POST",
				dataType: "json",
				data: new FormData(document.getElementById('JqPostForm')),
				processData: false,
				contentType : false,
				xhr: function() {
					myXhr = $.ajaxSettings.xhr();
					if (myXhr.upload)
					{

						myXhr.upload.addEventListener('progress', progressHandlingFunction, false);
					}
					return myXhr;
				},
				success: function (result)
				{
					var maxwidth = parseInt($('.Jqouter').width());
					$('.Jqinner').width(maxwidth+'px');

					$('.JqJumpMsgBox[data-showmsg=dataprocessing]').removeClass('active');
					$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(result.sMsg);
					$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
					$(this).removeClass('active');
				},
				error: function (txt)
				{
					console.log(txt);
				}
			});

			// fetch(sUrl, {
			// 	method: 'post',
			// 	body: new FormData(document.getElementById('JqPostForm'))
			// }).then(res => {
			// 	if (!res.ok) {
			// 		alert(res.statusText);
			// 		throw new Error(res.statusText);
			// 	}

			// 	return res.json();
			// }).then(result => {

			// 	$('.JqJumpMsgBox[data-showmsg=dataprocessing]').removeClass('active');
			// 	$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(result.sMsg);
			// 	$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
			// 	$(this).removeClass('active');

			// }).catch(err => {
			// 	console.log(`Reject ${err}`);
			// })
		}
	});

	//刪除
	$('.JqDelVideo').on('click', function ()
	{
		let sUrl = $(this).attr('data-act');

		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');
			$('.JqJumpMsgBox[data-showmsg=dataprocessing]').addClass('active');

			fetch(sUrl, {
				method: 'post',
			}).then(res => {
				if (!res.ok) {
					alert(res.statusText);
					throw new Error(res.statusText);
				}
				return res.json();
			}).then(result => {

				$('.JqJumpMsgBox[data-showmsg=dataprocessing]').removeClass('active');
				$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
				$(this).removeClass('active');

			}).catch(err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});
function progressHandlingFunction(e)
{
	var inner = document.querySelector(".Jqinner");
	var maxwidth = $('.Jqouter').width();

	if (e.lengthComputable)
	{
		inner.style.width = ((e.loaded / e.total) * maxwidth-2) + 'px';
	}
}
