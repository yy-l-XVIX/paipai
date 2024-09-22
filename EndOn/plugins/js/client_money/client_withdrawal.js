$(document).ready(function () {
	laydate.render({ elem: '.JqStartTime', type: 'datetime' });
	laydate.render({ elem: '.JqEndTime', type: 'datetime' });

	$(".JqCopy").click(function (event)
	{
		var oCopyText = $(this).parent().siblings('.JqCopyMe').children('div');
		var Range = document.createRange();
		Range.selectNodeContents(oCopyText[0]);
		var Sel = window.getSelection();
		Sel.removeAllRanges();
		Sel.addRange(Range);
		document.execCommand('copy');
	});

	$('.JqDate').click(function(event)
	{
		$('.JqDate').removeClass('active');
		$(this).addClass('active');
		$('.JqStartTime').val($(this).data('date0'));
		$('.JqEndTime').val($(this).data('date1'));
		$('input[name=sSelDay]').val($(this).data('day'));
	});
});