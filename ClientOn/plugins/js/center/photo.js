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

	// 點選照片
	$('.JqPhotoBtnZoom').on('click' , function()
	{
		var ImgUrl = $(this).find('img').attr('src');
		var DelUrl = $(this).find('img').attr('data-act');
		$('.JqPhotoZoomBox').addClass('active');
		$('.JqZoomImg').attr('src',ImgUrl);
		$('.JqDelImg').attr('data-act',DelUrl);
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
	$('.JqDelImg').on('click', function ()
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

	// 上傳檔案
	$('.JqFile').on('change', function ()
	{
		$('.JqPreviewImage[data-file="'+this.dataset.filebtn+'"]').remove();
		if (readURL(this))
		{
			if($('.JqPreviewImage[data-file="'+this.dataset.filebtn+'"]').attr('src') !== undefined)
			{
				$(this).parent().addClass('active');
				$(this).addClass('active');
				$('.JqEmojiContentPhotoBox').addClass('active'); //目前討論區留言在使用 (裝照片框架)
				$('.JqPreviewImage[data-file="'+this.dataset.filebtn+'"]').addClass('active');
				if($('.JqFileBtnActChange').length==0)
				{
					$('.JqFileBtnActBox').prepend('<div class="BtnAct2 MarginBottom15 JqFileBtnActChange" data-filechangebtn="'+this.dataset.filebtn+'">'+aJSDEFINE['CHANGE']+'</div>');
				}
			}
		}
	});

	// 更換按鈕
	$('.JqFileBtnActBox').on('click' , '.JqFileBtnActChange', function ()
	{
		$('.JqFile[data-filebtn="'+this.dataset.filechangebtn+'"]').click();
	});
});

function readURL(input)
{
	var nIsFile = 0;
	var nCount = 0;

	$.each(input.files, function(index, val)
	{
		if (nCount < $('.JqImgLeft').val() && val)
		{
			var reader = new FileReader();
			reader.onload = function (e)
			{
				// $('.JqPreviewImage[data-file="'+input.dataset.filebtn+'"]').attr('src', e.target.result);
				$('.JqAppend').each(function(index, el)
				{
					if ($(this).find('img').length == 0)
					{
						if (input.dataset.filebtn == 0) {
							$(this).append('<img data-file="0" class="infPhotoBlock JqPreviewImage " src="'+e.target.result+'">');
						}
						else
						{
							$(this).append('<img data-file="1" class="infPhotoBlock JqPreviewImage active" src="'+e.target.result+'">');
						}

						return false;
					}

				});
				// $('.JqAppendTd').first().append('<img data-file="1" class="infPhotoBlock JqPreviewImage active" src="'+e.target.result+'">');
			}
			reader.readAsDataURL(val);
			nIsFile = 1;
			nCount ++;
		}

	});
	if (nIsFile == 1)
	{
		return true;
	}
	else
	{
		return false;
	}

	// if (input.files && input.files[0])
	// {
	// 	var reader = new FileReader();
	// 	reader.onload = function (e)
	// 	{
	// 		console.log(e);
	// 		$('.JqPreviewImage[data-file="'+input.dataset.filebtn+'"]').attr('src', e.target.result);
	// 	}
	// 	reader.readAsDataURL(input.files[0]);
	// 	return true;
	// }
}

function progressHandlingFunction(e)
{
	var inner = document.querySelector(".Jqinner");
	var maxwidth = $('.Jqouter').width();

	if (e.lengthComputable)
	{
		inner.style.width = ((e.loaded / e.total) * maxwidth-2) + 'px';
      }
}