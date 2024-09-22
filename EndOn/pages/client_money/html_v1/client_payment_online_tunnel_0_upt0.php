<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="post" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWT;?>">
	<input type="hidden" name="nId" value="<?php echo $nId;?>">

	<!-- Select -->
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo STATUS;?></span>
		<div class="Sel">
			<select name="nOnline">
				<?php
					foreach($aOnline as $LPnStatus => $LPaDetail)
					{
				?>
						<option value="<?php echo $LPnStatus;?>" <?php echo $LPaDetail['sSelect'];?>><?php echo $LPaDetail['sText'];?></option>
				<?php
					}
				?>
			</select>
		</div>
	</div>
	<!-- Select -->
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINETUNNEL['PAYMENT'];?></span>
		<div class="Sel">
			<select name="nPid">
				<?php
					foreach($aPayment as $LPnId => $LPaDetail)
					{
				?>
						<option value="<?php echo $LPnId;?>" <?php echo $LPaDetail['sSelect'];?>><?php echo $LPaDetail['sName0'];?></option>
				<?php
					}
				?>
			</select>
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINETUNNEL['TUNNELKEY'];?></span>
		<div class="Ipt">
			<input type="text" name="sKey" value="<?php echo $aData['sKey'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINETUNNEL['TUNNELVALUE'];?></span>
		<div class="Ipt">
			<input type="text" name="sValue" value="<?php echo $aData['sValue'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINETUNNEL['TUNNELMIN'];?></span>
		<div class="Ipt">
			<input type="number" name="nMin" value="<?php echo number_format($aData['nMin'],0,'.','');?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINETUNNEL['TUNNELMAX'];?></span>
		<div class="Ipt">
			<input type="number" name="nMax" value="<?php echo number_format($aData['nMax'],0,'.','');?>">
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