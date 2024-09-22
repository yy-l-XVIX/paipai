$(document).ready(function()
{
	laydate.render({ elem: '.JqBirthday', type: 'date' });
	//完成
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

				$('.JqJumpMsgBox').removeClass('active');
				$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
				$(this).removeClass('active');
				$('.JqJumpMsgBox[data-showmsg=dataprocessing]').removeClass('active');
			}).catch(err => {
				console.log(`Reject ${err}`);
			})
		}
	});

	$('.JqSaveRedirect').on('click', function(event) // 保存再離開
	{
		event.preventDefault();
		let sUrl = $('#JqPostForm').attr('action');
		let formPass = true;
		/* Act on the event */
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

				if (result.nStatus == '1')
				{
					location.href=$(this).attr('href');
				}
				else
				{
					$('.JqJumpMsgBox').removeClass('active');
					$('.JqJumpMsgBox[data-showmsg=0]').find('.JqJumpMsgContentTxt').html(result.sMsg);
					$('.JqJumpMsgBox[data-showmsg=0]').addClass('active');
				}
				$(this).removeClass('active');
			}).catch(err => {
				console.log(`Reject ${err}`);
			})
		}

	});

	$('a').on('click', function(event)
	{
		if (!$(this).hasClass('JqGopage'))
		{
			event.preventDefault();
			/* Act on the event */
			if (CheckDataChange())
			{
				$('.JqJumpMsgBox[data-showmsg=1]').find('.JqRedirectLink').attr('href',$(this).attr('href'));//
				$('.JqJumpMsgBox[data-showmsg=1]').addClass('active');
			}
			else
			{
				location.href=$(this).attr('href');
			}
		}

	});

	function CheckDataChange()
	{
		var bIsChange = false;
		$.each($(':required'),function(LPnIndex,LPoObj)
		{
			if($(this).val() != $(this).attr('data-old'))
			{
				bIsChange = true;
			}
		});

		return bIsChange;
	}
});