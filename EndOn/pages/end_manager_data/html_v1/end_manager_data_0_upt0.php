<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="POST" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWTAct;?>" />
	<input type="hidden" name="nt" value="<?php echo NOWTIME;?>" />
	<input type="hidden" name="nId" value="<?php echo $aData['nId'];?>" />
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aMANAGER['ADMTYPE'];?></span>
		<div class="Sel">
			<select name="nAdmType" class="JqChangType" data-url="<?php echo $aUrl['sPage'];?>">
				<?php
				foreach ($aAdmType as $LPnAdmType => $LPaAdmType)
				{
					?>
					<option value="<?php echo $LPnAdmType;?>" <?php echo $LPaAdmType['sSelect'];?> >
						<?php echo $LPaAdmType['sName0'];?>
					</option>
					<?php
				}
				?>
			</select>
		</div>
	</div>
	<!-- Select -->
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aMANAGER['STATUS'];?></span>
		<div class="Sel">
			<select name="nOnline">
				<?php
				foreach ($aOnline as $LPnOnline => $LPaOnline)
				{
					?>
					<option value="<?php echo $LPnOnline;?>" <?php echo $LPaOnline['sSelect'];?> >
						<?php echo $LPaOnline['sText'];?>
					</option>
					<?php
				}
				?>
			</select>
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aMANAGER['ACCOUNT'];?></span>
		<div class="Ipt">
			<input type="text" name="sAccount" value="<?php echo $aData['sAccount'];?>" <?php echo $aData['sDisable'];?> placeholder="<?php echo aMANAGER['ACCOUNTINFO'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aMANAGER['NAME'];?></span>
		<div class="Ipt">
			<input type="text" name="sName0" value="<?php echo $aData['sName0'];?>" placeholder="<?php echo aMANAGER['NAME'];?>">
		</div>
	</div>
	<?php
	if ($aData['nId'] > 0)
	{
		?>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aMANAGER['PASSWORD'];?></span>
			<div class="Ipt">
				<input type="password" name="sPassword" placeholder="<?php echo aMANAGER['PASSWORD'];?>">
			</div>
			<i class="fas fa-question-circle lowupt_notice"></i>
			<span class=""><?php echo aMANAGER['NOTE'];?></span>
		</div>
		<?php
	}
	?>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aMANAGER['NEWPASSWORD'];?></span>
		<div class="Ipt">
			<input type="password" name="sNewPassword" placeholder="<?php echo aMANAGER['NEWPASSWORD'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aMANAGER['CONFIRMPASSWORD'];?></span>
		<div class="Ipt">
			<input type="password" name="sConfirmPassword" placeholder="<?php echo aMANAGER['CONFIRMPASSWORD'];?>">
		</div>
	</div>
	<?php
	if ($nAdmType == 3) #後台管理員
	{
		?>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo '管理地區';?></span>
			<div class="Sel">
				<select name="nLid">
					<?php
					foreach ($aLocation as $LPnLid => $LPaLocation)
					{
						?>
						<option value="<?php echo $LPnLid;?>" <?php echo $LPaLocation['sSelect'];?>><?php echo $LPaLocation['sName0'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<?php
	}
	?>
	<?php
	if ($aAdm['nAdmType'] == 1)
	{
		?>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aMANAGER['IP'];?></span>
			<div class="Ipt">
				<input type="text" name="sIp" placeholder="" value="<?php echo $aData['sIp'];?>">
			</div>
		</div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aMANAGER['HIDEUSER'];?></span>
			<?php
			foreach ($aType1 as $LPnType1 => $LPaType1)
			{
				?>
				<label for="nType1_<?php echo $LPnType1;?>" class="IptRadio">
					<input type="radio" id="nType1_<?php echo $LPnType1;?>" name="nType1" <?php echo $LPaType1['sSelect'];?> value="<?php echo $LPnType1;?>">
					<span><?php echo $LPaType1['sTitle'];?></span>
				</label>
				<?php
			}
			?>
		</div>

		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aMANAGER['GOOGLE'];?></span>
			<?php
			foreach ($aGoogle as $LPnGoogle => $LPaGoogle)
			{
				?>
				<label for="nGoogle_<?php echo $LPnGoogle;?>" class="IptRadio">
					<input type="radio" id="nGoogle_<?php echo $LPnGoogle;?>" name="nGoogle" <?php echo $LPaGoogle['sSelect'];?> value="<?php echo $LPnGoogle;?>">
					<span><?php echo $LPaGoogle['sTitle'];?></span>
				</label>
				<?php
			}
			?>
		</div>
		<?php
	}
	?>
	<?php
	# google 綁定qrcode (驗證成功不再顯示sys_google_verify nStatus = 1)
	if ($aData['sGoogleQrcode'] != '')
	{
		echo $aData['sGoogleQrcode'];
		?>
		<div class="Block MarginBottom5">
			<span class="InlineBlockTit">1. <?php echo aGOOGLE['STEP1'];?></span>
		</div>
		<div class="Block MarginBottom5">
			<span class="InlineBlockTit">2. <?php echo aGOOGLE['STEP2'];?></span>
		</div>
		<div class="Block MarginBottom5">
			<span class="InlineBlockTit">3. <?php echo aGOOGLE['STEP3'];?></span>
		</div>
		<div class="Block MarginBottom20">
			<input type="hidden" name="sVerifyUrl" value="<?php echo $sVerifyJWT;?>">
			<div class="Ipt">
				<input type="text" class="Jqverify" placeholder="<?php echo aGOOGLE['CODE'];?>">
			</div>
			<a href="javascript:void(0)" class="BtnAny JqQrsubmit"><?php echo aGOOGLE['SEND'];?></a>
		</div>
		<?php
	}
	?>

	<div class="Block">
		<div class="BlockTit MarginBottom5"><?php echo aMANAGER['CONTROLITEM'];?></div>
		<div class="BtnAny MarginBottom5 JqCheckAll"><?php echo aMANAGER['SELECTALL'];?></div>
		<div class="GridBlockBox JqControlBlock">
			<?php
			foreach ($aAdmType[$nAdmType]['aControl'] as $LPnMkid => $LPaMlid)
			{
				?>
				<div class="GridBlock">
					<div class="GridBlockTopic"><?php echo aMENULANG['aKIND'][$aSystem['aNav'][$LPnMkid]['sMenuTable0']]; ?></div>
					<?php
					foreach ($LPaMlid as $LPnMlid)
					{
						if (!isset($aSystem['aNav'][$LPnMkid]['aList'][$LPnMlid]))
						{
							continue;
						}
						$LPsChecked = '';
						if (isset($aData['aControl'][$LPnMkid][$LPnMlid]))
						{
							$LPsChecked = 'checked';
						}
						?>
						<label class="GridBlockList" for="list<?php echo $LPnMlid;?>">
							<input id="list<?php echo $LPnMlid;?>" type="checkbox" <?php echo $LPsChecked;?> value="<?php echo $LPnMlid;?>" name="aControl[<?php echo $LPnMkid;?>][<?php echo $LPnMlid;?>]">
							<span><?php echo aMENULANG['aLIST'][$aSystem['aNav'][$LPnMkid]['aList'][$LPnMlid]['sListTable0']];?></span>
						</label>
						<?php
					}
					?>
				</div>
				<?php
			}
			?>
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