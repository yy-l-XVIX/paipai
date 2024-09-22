$(document).ready(function()
{
	$('.JqDeleteBank').on('click', function () {
		let sUrl = $(this).data('href');


		if (!$(this).hasClass('active')) {

			$(this).addClass('active');

			fetch(sUrl, {
				method: 'get'
			}).then(res => {
				if (!res.ok) {
					alert(res.statusText);
					throw new Error(res.statusText);
				}

				return res.json();
			}).then(result => {
				$('.JqJumpMsgContentTxt').html(result.sMsg);
				$('.JqJumpMsgBox').addClass('active');
				$(this).removeClass('active');
				if (result.nStatus == 1)
				{
					$('div[data-bid='+result.aData.nId+']').remove();
				}

			}).catch(err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});