$(document).ready(function () {
	laydate.render({ elem: '.JqStartTime', type: 'datetime' });
	laydate.render({ elem: '.JqEndTime', type: 'datetime' });

	$('.JqDate').click(function(event)
	{
		$('.JqDate').removeClass('active');
		$(this).addClass('active');
		$('.JqStartTime').val($(this).data('date0'));
		$('.JqEndTime').val($(this).data('date1'));
		$('input[name=sSelDay]').val($(this).data('day'));
	});
});