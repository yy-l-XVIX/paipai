$(document).ready(function()
{
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
	$('.JqChangType').on('change' , function()
	{
		location.replace($(this).data('url')+'&nAdmType='+$(this).val()+'&nId='+$('input[name=nId]').val());
	});
	$('.JqQrsubmit').on('click' , function()
	{
		var sUrl = $('input[name=sVerifyUrl]').val();
		var sCode = $('.Jqverify').val();
		if (sCode != '')
		{
			location.replace(sUrl+'&sCode='+sCode);
		}
	});
});
