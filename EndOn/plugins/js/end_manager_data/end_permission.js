$(document).ready(function()
{
	$('.CheckAll').on('click' , function()
	{
		if(!$(this).hasClass('active'))
		{
			$('.ControlBlock').find('input').prop('checked',true);
		}
		else
		{
			$('.ControlBlock').find('input').prop('checked',false);
		}
		$(this).toggleClass('active');
	});
});
