$(document).ready(function()
{
	// 上傳檔案
	$(document).on('change','.JqFile' ,function ()
	{
		var nFilebtn = parseInt($(this).attr('data-filebtn'));
		if ($('input[name=nImgCount').val() < $('input[name=nImgCount').attr('data-max'))
		{
			if (readURL(this))
			{
				$('.JqEmojiContentPhotoBox').addClass('active');
				$('.JqPhotoOtherBox').addClass('active'); // 因上傳圖片會擋住原本對話,所以塞個class給他,
				if($('.JqPreviewImage[data-file="'+this.dataset.filebtn+'"]').attr('src') !== undefined)
				{
					// $(this).parent().addClass('active');
					// $(this).addClass('active');
					$('.JqPreviewImage[data-file="'+this.dataset.filebtn+'"]').addClass('active');
				}
				// 新增一個input file
				$(this).addClass('DisplayBlockNone');
				$('input[name=nImgCount').val(parseInt($('input[name=nImgCount').val())+1);
				$('.JqFileBtnBox').append('<input type="file" class="JqFile" name="aFile[]" data-filebtn="'+(nFilebtn+1)+'">');
			}
		}

	});

	// 刪除按鈕
	$('.JqEmojiContentPhotoBox').on('click' , '.JqEmojiContentPhotoBtnDelete', function ()
	{
		var nFilebtn = parseInt($(this).siblings('.JqPreviewImage').attr('data-file'));

		$(this).parent().remove();
		$('[data-filebtn='+nFilebtn+']').remove();
		$('input[name=nImgCount').val(parseInt($('input[name=nImgCount').val())-1);
		if($('.JqPreviewImage').length < 1)
		{
			$('.JqEmojiContentPhotoBox').removeClass('active');
			$('.JqPhotoOtherBox').removeClass('active'); // 因上傳圖片會擋住原本對話,所以塞個class給他,但送出後必須拉掉
		}
	});
});

function readURL(input)
{
	if (input.files && input.files[0])
	{
		var reader = new FileReader();
		reader.onload = function (e)
		{
			$('.JqEmojiContentPhotoBox').append('<div class="EmojiContentPhotoBlock">'+
										'<img class="EmojiContentPhoto JqPreviewImage" data-file="'+input.dataset.filebtn+'" src="'+e.target.result+'">'+
										'<div class="EmojiContentPhotoBtn JqEmojiContentPhotoBtnDelete">'+
											'<i class="fas fa-times"></i>'+
										'</div>'+
									'</div>');
			// $('.JqPreviewImage[data-file="'+input.dataset.filebtn+'"]').attr('src', e.target.result);
		}

		reader.readAsDataURL(input.files[0]);
		return true;
	}
}