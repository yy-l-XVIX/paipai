$(document).ready(function()
{

	$('.JqSubmit').on('click' , function()
	{
		if (!$(this).hasClass('active'))
		{
			$(this).addClass('active');
			var sUrl = $(this).attr('data-url');
			fetch(sUrl, {
				method: 'post',
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
