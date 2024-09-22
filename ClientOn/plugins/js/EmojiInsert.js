$(document).ready(function(){
	// 點選emoji 插入當前游標位置
	$('.JqEmojiImage').on('click', function()
	{
		$('.JqContent0').focus();
		var sIcon = $(this).attr('src');
		sIcon = '<img class="EmojiImgIcon" src="'+sIcon+'">';
		InserImg(sIcon);
	});
});

function InserImg($sVal)
{
	var oEditArea = $('.JqContent0')[0];
	var oRange, oNode;
	if(!oEditArea.hasfocus)
	{
		oEditArea.focus();
	}

	//判定是否能使用該指令
	if (window.getSelection && window.getSelection().getRangeAt)
	{
		//插入該區段
		oRange = window.getSelection().getRangeAt(0);
		oNode = oRange.createContextualFragment($sVal);
		var oChild = oNode.lastChild;
		oRange.insertNode(oNode);

		//處理指標位置
		if(oChild)
		{
			oRange.setEndAfter(oChild);
			oRange.setStartAfter(oChild);
  		}
  		var oNewRange = window.getSelection();
  		oNewRange.removeAllRanges();
		oNewRange.addRange(oRange);
	}
	else if (document.selection && document.selection.createRange)
	{
		document.selection.createRange().pasteHTML($sVal);
	}
}