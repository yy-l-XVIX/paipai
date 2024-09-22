<?php
	$aData = json_decode($sData,true);
?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="post" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWT;?>">
	<input type="hidden" name="nLid" value="<?php echo $nLid;?>">

	<!-- Select -->
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo STATUS;?></span>
		<div class="InlineBlockTxt"><?php echo $aOnline[$aData[$LPsLang]['nOnline']]['sText'];?></div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aUSERKIND['MEMBERFREETIME'];?></span>
		<div class="Ipt">
			<input type="text" class="JqTime" name="sFreeStartTime" value="<?php echo $aData[$LPsLang]['sFreeStartTime'];?>">
		</div>
		<span>~</span>
		<div class="Ipt">
			<input type="text" class="JqTime" name="sFreeEndTime" value="<?php echo $aData[$LPsLang]['sFreeEndTime'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"></span>
		<div class="Sel">
			<select name="nType0">
				<?php
					foreach($aType0 as $LPnType0 => $LPaDetail)
					{
						?>
						<option value="<?php echo $LPnType0;?>" <?php echo $LPaDetail['sSelect'];?>><?php echo $LPaDetail['sText'];?></option>
						<?php
					}
				?>
			</select>
		</div>
		<span class="InlineBlockTit"><?php echo aUSERKIND['FREEDAYS'];?></span>
		<div class="Ipt">
			<input type="text" name="nFreeDays" value="<?php echo $aData[$LPsLang]['nFreeDays'];?>">
		</div>
	</div>

	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aUSERKIND['PRICE'];?></span>
		<div class="Ipt">
			<input type="text" name="nPrice" value="<?php echo $aData[$LPsLang]['nPrice'];?>">
		</div>
	</div>

	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aUSERKIND['BONUS0'];?> <?php echo aUSERKIND['BONUSINFO'];?></span>
		<div class="Ipt">
			<input type="text" name="sPromoteBonus" value="<?php echo $aData[$LPsLang]['sPromoteBonus'];?>">
		</div>
	</div>

	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aUSERKIND['BONUSTAX'];?> <?php echo aUSERKIND['BONUSTAXINFO'];?></span>
		<div class="Ipt">
			<input type="text" name="sPromoteBonusTax" value="<?php echo $aData[$LPsLang]['sPromoteBonusTax'];?>">
		</div>
		<div class="Sel">
			<select name="nType1">
				<?php
				foreach ($aType1 as $LPnType1 => $LPaType1)
				{
					?>
					<option value="<?php echo $LPnType1;?>" <?php echo $LPaType1['sSelect'];?>><?php echo $LPaType1['sText'];?></option>
					<?php
				}
				?>
			</select>
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo CHOSELANG;?></span>
		<span class="DisplayInlineBlock VerticalAlignMiddle">
			<?php
				foreach(aLANG as $LPsLang => $LPsText)
				{
					$sActive = '';
					if($aSystem['sLang'] == $LPsLang)
					{
						$sActive = 'active';
					}
					?>
					<span class="BtnKind JqBtnShowOnly <?php echo $sActive;?>" data-showctrl="<?php echo $LPsLang;?>"><?php echo $LPsText;?></span>
					<?php
				}
			?>
		</span>
	</div>
	<?php
		foreach(aLANG as $LPsLang => $LPsText)
		{
			$sActive = '';
			if($aSystem['sLang'] == $LPsLang)
			{
				$sActive = 'active';
			}
			?>
			<div class="Block DisplayBlockNone MarginBottom20 <?php echo $sActive;?>" data-show="<?php echo $LPsLang;?>">
				<span class="InlineBlockTit"><?php echo aUSERKIND['NAME'];?></span>
				<div class="Ipt">
					<input type="text" name="sName0[<?php echo $LPsLang;?>]" value="<?php echo $aData[$LPsLang]['sName0'];?>">
				</div>
			</div>
			<div class="Block DisplayBlockNone MarginBottom20 <?php echo $sActive;?>" data-show="<?php echo $LPsLang;?>">
				<span class="InlineBlockTit"><?php echo aUSERKIND['CONTENT0'];?></span>
				<div class="Ipt">
					<input type="text" name="sContent0[<?php echo $LPsLang;?>]" value="<?php echo $aData[$LPsLang]['sContent0'];?>">
				</div>
			</div>
			<?php
		}
	?>

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