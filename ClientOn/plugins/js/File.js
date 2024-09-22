$(document).ready(function(){
	// 上傳檔案
	$('.JqFile').on('change', function ()
	{
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
	if (input.files && input.files[0])
	{

		var file = input.files[0];
		var reader = new FileReader();

		if (file.type.match('image'))
		{
			reader.onload = function (e)
			{
				$('.JqPreviewImage[data-file="'+input.dataset.filebtn+'"]').attr('src', e.target.result);
			}
			reader.readAsDataURL(file);
			return true;
		}
		else
		{
			const video = $('.JqPreviewImage[data-file="'+input.dataset.filebtn+'"]')[0];
			// video do something...
			reader.onload = function(e)
			{
				$('.JqPreviewImage[data-file="'+input.dataset.filebtn+'"]').attr('src', e.target.result);
				video.autoplay = true;
				video.load();
				video.play();
			};
			reader.readAsDataURL(file);
			return true;
		}
	}
}