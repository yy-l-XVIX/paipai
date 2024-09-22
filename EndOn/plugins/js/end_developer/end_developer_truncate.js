$(document).ready(function()
{
	$('.JqSubAll').on('click' , function()
	{
		if($(this).find('input').prop('checked'))
		{
			$(this).siblings().find('input').prop('checked',true);
		}
		else
		{
			$(this).siblings().find('input').prop('checked',false);
		}
	});
	$('.JqCheckAll').on('click' , function()
	{
		if(!$(this).hasClass('active'))
		{
			$('.JqControlBlock').find('input').prop('checked',true);
		}
		else
		{
			$('.JqControlBlock').find('input').prop('checked',false);
		}
		$(this).toggleClass('active');
	});
});