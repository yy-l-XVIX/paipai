<?php
	$aData = json_decode($sData,true);
?>
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
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTCOMPANYSETTING['NAME'];?></span>
		<div class="Ipt">
			<input type="text" name="sName0" value="<?php echo $aData['sName0'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTCOMPANYSETTING['ACCOUNT'];?></span>
		<div class="Ipt">
			<input type="text" name="sAccount0" value="<?php echo $aData['sAccount0'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTCOMPANYSETTING['BANKNAME'];?></span>
		<div class="Sel">
			<select name="nBid">
				<?php
					foreach($aBank as $LPnId => $LPaDetail)
					{
				?>
						<option value="<?php echo $LPnId;?>" <?php echo $LPaDetail['sSelect'];?> ><?php echo $LPaDetail['sName0'];?></option>
				<?php
					}
				?>
			</select>
		</div>
	</div>
	<?php
		if(false)
		{
	?>
			<div class="Block MarginBottom20">
				<span class="InlineBlockTit"><?php echo aPAYMENTCOMPANYSETTING['MAX'];?></span>
				<div class="Ipt">
					<input type="number" name="nMax" value="<?php echo number_format($aData['nMax'],0,'.','');?>">
				</div>
			</div>
			<div class="Block MarginBottom20">
				<span class="InlineBlockTit"><?php echo aPAYMENTCOMPANYSETTING['MIN'];?></span>
				<div class="Ipt">
					<input type="number" name="nMin" value="<?php echo number_format($aData['nMin'],0,'.','');?>">
				</div>
			</div>
			<div class="Block MarginBottom20">
				<span class="InlineBlockTit"><?php echo aPAYMENTCOMPANYSETTING['DAYLIMITMONEY'];?></span>
				<div class="Ipt">
					<input type="number" name="nDayLimitMoney" value="<?php echo number_format($aData['nDayLimitMoney'],0,'.','');?>">
				</div>
			</div>
	<?php
		}
	?>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTCOMPANYSETTING['TOTALLIMITMONEY'];?></span>
		<div class="Ipt">
			<input type="number" name="nTotalLimitMoney" value="<?php echo number_format($aData['nTotalLimitMoney'],0,'.','');?>">
		</div>
		<span class="FontRed"><?php echo aPAYMENTCOMPANYSETTING['NOLIMIT'];?></span>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTCOMPANYSETTING['TOTALLIMITTIMES'];?></span>
		<div class="Ipt">
			<input type="number" name="nTotalLimitTimes" value="<?php echo $aData['nTotalLimitTimes'];?>">
		</div>
		<span class="FontRed"><?php echo aPAYMENTCOMPANYSETTING['NOLIMIT'];?></span>
	</div>
	<div class="Block">
		<span class="InlineBlockTit"><?php echo aPAYMENTCOMPANYSETTING['DAYLIMITTIMES'];?></span>
		<div class="Ipt">
			<input type="number" name="nDayLimitTimes" value="<?php echo number_format($aData['nDayLimitTimes'],0,'.','');?>">
		</div>
		<span class="FontRed"><?php echo aPAYMENTCOMPANYSETTING['NOLIMIT'];?></span>
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