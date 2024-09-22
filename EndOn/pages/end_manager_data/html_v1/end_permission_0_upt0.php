<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="POST" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWTAct;?>" />
	<input type="hidden" name="nt" value="<?php echo NOWTIME;?>" />
	<input type="hidden" name="nId" value="<?php echo $aData['nId'];?>" />
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aPERMISSION['NAME0'];?></span>
		<div class="Ipt">
			<input type="text" name="sName0" value="<?php echo $aData['sName0'];?>" placeholder="<?php echo aPERMISSION['NAME0'];?>">
		</div>
	</div>
	<div class="Block">
		<div class="BlockTit MarginBottom5"><?php echo aPERMISSION['CONTROLITEM'];?></div>
		<div class="BtnAny MarginBottom5 CheckAll"><?php echo aPERMISSION['SELECTALL'];?></div>
		<div class="GridBlockBox ControlBlock">
			<?php
			foreach ($aSystem['aNav'] as $LPnMkid => $LPaMenuData)
			{
				?>
				<div class="GridBlock">
					<div class="GridBlockTopic"><?php echo $LPaMenuData['sMenuName0']; ?></div>
					<?php
					foreach ($LPaMenuData['aList'] as $LPnMlid => $LPaListData)
					{
						$LPsChecked = '';
						if (isset($aData['aControl'][$LPnMkid]) && in_array($LPnMlid, $aData['aControl'][$LPnMkid]))
						{
							$LPsChecked = 'checked';
						}
						?>
						<label class="GridBlockList">
							<input type="checkbox" <?php echo $LPsChecked;?> value="<?php echo $LPnMlid;?>" name="aControl[<?php echo $LPnMkid;?>][<?php echo $LPnMlid;?>]">
							<span><?php echo $LPaListData['sListName0'];?></span>
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