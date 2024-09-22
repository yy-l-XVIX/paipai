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

	$('.JqChangeWork').on('click', function()
	{
		if (!$(this).hasClass('active'))
		{
			$('.JqChangeWork').removeClass('active');
			$(this).addClass('active');

			fetch($(this).attr('data-act'),
			{
				method: 'post',
				body: '',
			}).then( res => {
				if (!res.ok)
				{
					alert(res.statusText);
	 				throw new Error(res.statusText);
				}

				return res.json();
			}).then( result => {
				if(result.nStatus == 0)
				{
					$('.JqChangeStatus').removeClass('off ing');
					$('.JqChangeStatus').addClass(result.aData.sClass);
					// location.reload();
				}

			}).catch( err => {
				console.log(`Reject ${err}`);
			})
		}
	});
});