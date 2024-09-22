$(document).ready(function()
{
	$('.JqCopy').on('click',function()
	{
		var sUrl = $('.JqCopyUrl').val();
		$('.JqCopyUrl').attr('value', sUrl);
		$('.JqCopyUrl').attr('type', 'text').select();
		document.execCommand('copy');
		$('.JqCopyUrl').attr('type', 'hidden');

		alert(aJSDEFINE['COPYSUCCESS']+'！');
	});
});