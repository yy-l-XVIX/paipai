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
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['NAME'];?></span>
		<div class="Ipt">
			<input type="text" name="sName0" value="<?php echo $aData['sName0'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['ACCOUNT'];?></span>
		<div class="Ipt">
			<input type="text" name="sAccount0" value="<?php echo $aData['sAccount0'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['CODE'];?></span>
		<div class="Ipt">
			<input type="text" name="sName1" value="<?php echo $aData['sName1'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo $aData['aFeeType']['sTitle'];?></span>
		<label for="nTypeRadio0" class="IptRadio">
			<input type="radio" name="nType1" id="nTypeRadio0" value="1" <?php echo $aData['aFeeType'][1]['sCheck'];?> >
			<span><?php echo $aData['aFeeType'][1]['sText'];?></span>
		</label>
		<label for="nTypeRadio1" class="IptRadio">
			<input type="radio" name="nType1" id="nTypeRadio1" value="2" <?php echo $aData['aFeeType'][2]['sCheck'];?> >
			<span><?php echo $aData['aFeeType'][2]['sText'];?></span>
		</label>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['FEE'];?></span>
		<div class="Ipt">
			<input type="number" name="nFee" value="<?php echo number_format($aData['nFee'],0,'.','');?>">
		</div>
	</div>
	<?php
	if(false)
	{
	?>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['sKey0'];?></span>
		<div class="Ipt">
			<input type="text" name="sKey0" value="<?php echo $aData['sKey0'];?>" disabled="disabled">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['sKey1'];?></span>
		<div class="Ipt">
			<input type="text" name="sKey1" value="<?php echo $aData['sKey1'];?>" disabled="disabled">
		</div>
	</div>
	<?php
	}
	?>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['MAX'];?></span>
		<div class="Ipt">
			<input type="number" name="nMax" value="<?php echo number_format($aData['nMax'],0,'.','');?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['MIN'];?></span>
		<div class="Ipt">
			<input type="number" name="nMin" value="<?php echo number_format($aData['nMin'],0,'.','');?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['DAYLIMITMONEY'];?></span>
		<div class="Ipt">
			<input type="number" name="nDayLimitMoney" value="<?php echo number_format($aData['nDayLimitMoney'],0,'.','');?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['DAYLIMITTIMES'];?></span>
		<div class="Ipt">
			<input type="number" name="nDayLimitTimes" value="<?php echo number_format($aData['nDayLimitTimes'],0,'.','');?>">
		</div>
	</div>
	<?php
	if(false)
	{
	?>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['sKey2'];?></span>
		<div class="Ipt">
			<input type="text" name="sKey2" value="<?php echo $aData['sKey2'];?>" disabled="disabled">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['sKey3'];?></span>
		<div class="Ipt">
			<input type="text" name="sKey3" value="<?php echo $aData['sKey3'];?>" disabled="disabled">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['sKey4'];?></span>
		<div class="Ipt">
			<input type="text" name="sKey4" value="<?php echo $aData['sKey4'];?>" disabled="disabled">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['sKey5'];?></span>
		<div class="Ipt">
			<input type="text" name="sKey5" value="<?php echo $aData['sKey5'];?>" disabled="disabled">
		</div>
	</div>
	<?php
	}
	?>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPAYMENTONLINESETTING['sSign'];?></span>
		<div class="Ipt">
			<input type="text" name="sSign" value="<?php echo $aData['sSign'];?>">
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