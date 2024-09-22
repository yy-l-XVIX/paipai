<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage']; ?>" method="POST" class="Form MarginBottom20">
	<div class="Search">
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo ACCOUNT;?></span>
			<div class="Ipt">
				<input type="text" name="sAccount" value="<?php echo $sAccount;?>" placeholder="<?php echo ACCOUNT;?>">
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<div class="IptRadio">
				<input type="radio" name="nType" value="1" <?php echo ($nType == 1) ? 'checked="checked"' : '';?>>
				<span><?php echo aFRIEND['FRIEND'];?></span>

				<input type="radio" name="nType" value="2" <?php echo ($nType == 2) ? 'checked="checked"' : '';?>>
				<span><?php echo aFRIEND['BLOCK'];?></span>
			</div>
		</div>
		<input type="submit" class="BtnAny" value="<?php echo SEARCH;?>">
	</div>
</form>

<!-- 純顯示資訊 -->
	<?php
	#if($sAccount != '' && ($nType == 1 || $nType == 2))
	#{
	?>
	<div class="Information">
		<table class="InformationTit">
			<tbody>
				<tr>
					<td class="InformationTitCell" style="width:calc(100%/1);">
						<div class="InformationName"><?php echo $sHeadTitle; ?></div>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="InformationScroll">
			<div class="InformationTableBox">
				<table>
					<thead>
						<tr>
							<th><?php echo NO;?></th>
							<th><?php echo aFRIEND['NAME0'];?></th>
							<th><?php echo ACCOUNT;?></th>
							<th><?php echo CREATETIME;?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($aData as $LPnId => $LPaData)
						{
							?>
							<tr>
								<td><?php echo $LPnId;?></td>
								<td><?php echo $LPaData['sName0'];?></td>
								<td><?php echo $LPaData['sAccount'];?></td>
								<td><?php echo $LPaData['sCreateTime'];?></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php echo $aPageList['sHtml'];?>
	<?php
	#}
?>
