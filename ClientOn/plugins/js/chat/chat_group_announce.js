$(document).ready(function()
{
	$('.JqHeaderLeft').attr('href',$('.JqBackUrl').val());

	$('.JqFinish').on('click', function (event)
	{
		event.preventDefault();
		var sUrl = $('#JqPostForm').attr('action');
		console.log(sUrl);

		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');
			fetch(sUrl, {
				method: 'post',
				body: new FormData(document.getElementById('JqPostForm'))
			}).then( res => {
				if (!res.ok)
				{
					alert(res.statusText);
	 				throw new Error(res.statusText);
				}

				return res.json();
			}).then( result => {
				$('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqRedirectClose').attr('href',result.sUrl);
				$('.JqJumpMsgBox').addClass('active');
			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});