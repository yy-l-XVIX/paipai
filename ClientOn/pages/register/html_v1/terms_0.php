<?php $aData = json_decode($sData,true);?>
<!-- 使用規約 -->
<div class="termsBox">
	<div class="termsBlock">
		<?php echo $aData['sContent0'];?>
	</div>
	<div class="termsBtnBox">
		<a href="<?php echo $aUrl['sPage'];?>" class="BtnAct"><?php echo AGREETERMS;?></a>
	</div>
</div>
