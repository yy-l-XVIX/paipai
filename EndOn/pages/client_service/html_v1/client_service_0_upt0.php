<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="post" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWT;?>">
	<input type="hidden" name="nId" value="<?php echo $nId;?>">
	<div class="Block MarginBottom20" >
		<span class="InlineBlockTit"><?php echo KIND;?></span>
		<span><?php echo $aData['sName0'];?></span>
	</div>
	<div class="Block MarginBottom20" >
		<span class="InlineBlockTit"><?php echo ACCOUNT;?></span>
		<span><?php echo $aData['sAccount'];?></span>
	</div>
	<div class="Block MarginBottom20" >
		<span class="InlineBlockTit"><?php echo aSERVICE['QUESTION'];?></span>
		<span><?php echo $aData['sQuestion'];?></span>
	</div>

	<?php
		if(false)
		{
	?>
	<div class="Block MarginBottom20" >
		<span class="InlineBlockTit"><?php echo aSERVICE['IMAGE'];?></span>
		<div class="BlockImg">
			<img src="<?php echo $aData['sImageUrl'];?>" class="ImgZoom" alt="">
		</div>
	</div>
	<?php
		}
	?>

	<div class="Block MarginBottom20" >
		<span class="InlineBlockTit"><?php echo STATUS;?></span>
		<?php
			foreach($aStatus as $LPnStatus => $LPaDetail)
			{
		?>
				<label for="" class="IptRadio">
					<input type="radio" name="nStatus" value="<?php echo $LPnStatus;?>" <?php echo $LPaDetail['sChecked'];?>>
					<span><?php echo $LPaDetail['sTitle'];?></span>
				</label>
		<?php
			}
		?>
	</div>

	<div class="Block MarginBottom20" >
		<span class="InlineBlockTit"><?php echo aSERVICE['RESPONSE'];?></span>
		<span class="FontRed"><?php echo aSERVICE['RULE'];?></span>
		<div class="Textarea">
			<textarea name="sResponse"><?php echo $aData['sResponse'];?></textarea>
		</div>
	</div>



	<!-- 操作選項 -->
	<div class="EditBtnBox">
		<div class="EditBtn JqStupidOut" data-showctrl="0">
			<i class="far fa-save"></i>
			<span><?php echo CSUBMIT;?></span>
		</div>
		<a href="<?php echo $aUrl['sBack'];?>" class="EditBtn red">
			<i class="fas fa-times"></i>
			<span><?php echo CBACK;?></span>
		</a>
	</div>
</form>