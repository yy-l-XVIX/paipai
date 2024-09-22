$(document).ready(function()
{

	if($('.JqCenterData1').width() > $('.JqCenterData2').width())
	{
		$('.JqCenterProfileDecro').css('left',(30+$('.JqCenterData1').width())+'px');
	}
	else
	{
		$('.JqCenterProfileDecro').css('left',(30+$('.JqCenterData2').width())+'px');
	}

	if ($('.JqChangeRole').attr('data-url') != '')
	{
		$('.JqHeaderLink').attr('href', $('.JqChangeRole').attr('data-url'));
		$('.JqHeaderLink').text($('.JqChangeRole').attr('data-role'));
	}
	else
	{
		$('.JqHeaderLink').remove();
	}

	// 加好友or封鎖
	$('.JqAct').on('click' , function()
	{
		var sUrl = $(this).data('jqurl');

		fetch(sUrl, {
		}).then( res => {
			if (!res.ok)
			{
				alert(res.statusText);
				throw new Error(res.statusText);
			}

			return res.json();
		}).then( result => {
			$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(result.sMsg);
			$('.JqJumpMsgBox[data-showmsg=0]').find('.JqRedirectClose').attr('href',result.sUrl);
			$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
		}).catch( err => {
			console.log(`Reject ${err}`);
		})
	});

	// 送出
	$('.JqSubmit').on('click', function ()
	{
		let sUrl = $('#JqPostForm').attr('action');
		let formPass = true;

		$.each($(':required'),function(LPnIndex,LPoObj)
		{
			if (!FormVerify.empty(LPoObj))
			{
				$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html($('#JqPostForm').attr('data-info'));
				$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
				formPass = false;
				return false;
			}
		});

		if (formPass && !$(this).hasClass('active'))
		{

			$(this).addClass('active');
			$('.JqJumpMsgBox[data-showmsg=dataprocessing]').addClass('active');

			fetch(sUrl, {
				method: 'post',
				body: new FormData(document.getElementById('JqPostForm'))
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

	$('.JqCopy').on('click',function()
	{

		// var sUrl = $('.JqCopyUrl').val();
		// $('.JqCopyUrl').attr('value', sUrl);
		$('.JqCopyUrl').attr('type', 'text').select();
		document.execCommand('copy');
		$('.JqCopyUrl').attr('type', 'hidden');

		// alert(aJSDEFINE['COPYSUCCESS']+'！');
		$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(aJSDEFINE['COPYSUCCESS']+'！');
		$('.JqJumpMsgBox[data-showmsg=0]').find('.JqRedirectClose').attr('href','');
		$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
	});
});