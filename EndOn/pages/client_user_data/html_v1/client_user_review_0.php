<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage']; ?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo ACCOUNT;?></span>
			<div class="Ipt">
				<input type="text" name="sAccount" value="<?php echo $sAccount;?>" placeholder="<?php echo ACCOUNT;?>">
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aREVIEW['PENDINGSTATUS'];?></span>
			<div class="Sel">
				<select name="nPendingStatus">
					<?php
					foreach ($aPendingStatus as $LPnPendingStatus => $LPaDetail)
					{
						?>
						<option value="<?php echo $LPnPendingStatus;?>" <?php echo $LPaDetail['sSelect'];?>><?php echo $LPaDetail['sText'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<input type="submit" class="BtnAny" value="<?php echo SEARCH;?>">
	</div>
</form>
<!-- 純顯示資訊 -->
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
						<th><?php echo ACCOUNT;?></th>
						<th><?php echo aREVIEW['REALNAME'];?></th>
						<th><?php echo STATUS;?></th>
						<th><?php echo CREATETIME;?></th>
						<th><?php echo OPERATE;?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($aData as $LPnId => $LPaData)
					{
						?>
						<tr>
							<td><?php echo $LPnId;?></td>
							<td>
								<div><img src="<?php echo $LPaData['sHeadImg'];?>"></div>
								<div><?php echo $LPaData['sAccount'];?></div>
							</td>
							<td><?php echo $LPaData['sName1'];?></td>
							<td>
								<span><?php echo aREVIEW['INACTIVE'];?></span>
								<span><?php echo $aPendingStatus[$LPaData['nPendingStatus']]['sText'];?></span>
							</td>
							<td><?php echo $LPaData['sCreateTime'];?></td>
							<td>
								<a href="<?php echo $LPaData['sIns'];?>" class="TableBtnBg">
									<?php echo aREVIEW['TITLENAME'];?>
								</a>
							</td>
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