<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aGROUP['NAME0'];?></span>
			<div class="Ipt">
				<input type="text" name="sName0" value="<?php echo $sName0;?>" placeholder="<?php echo aGROUP['NAME0'];?>">
			</div>
		</div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aGROUP['ACCOUNT'];?></span>
			<div class="Ipt">
				<input type="text" name="sAccount" value="<?php echo $sAccount;?>" placeholder="<?php echo aGROUP['ACCOUNT'];?>">
			</div>
		</div>

		<input type="submit" class="BtnAny" value="<?php echo SEARCH;?>">
	</div>
</form>
<?php
/*
<!-- 新增按鈕 -->
<div class="Block MarginBottom10">
	<a href="<?php echo $aUrl['sIns'];?>" class="BtnAdd"><?php echo INS;?></a>
</div>
*/
?>
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
						<th><?php echo aGROUP['NAME0'];?></th>
						<th><?php echo aGROUP['ACCOUNT'];?></th>
						<th><?php echo aGROUP['GROUPMEN'];?></th>
						<th><?php echo CREATETIME;?></th>
						<th><?php echo UPDATETIME;?></th>
						<th><?php echo OPERATE;?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($aData as $LPnId => $LPaData)
					{
					?>
						<tr>
							<td><?php echo $LPnId;?></td>
							<td><?php echo $LPaData['sName0'];?></td>
							<td><?php echo $aMember[$LPaData['nUid']];?></td>
							<td><?php echo $LPaData['nCount'];?></td>
							<td><?php echo $LPaData['sCreateTime'];?></td>
							<td><?php echo $LPaData['sUpdateTime'];?></td>
							<td>
								<a href="<?php echo $LPaData['sIns'];?>" class="TableBtnBg">
									<i class="fas fa-pen"></i>
								</a>
								<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaData['sDel'];?>">
									<i class="fas fa-times"></i>
								</div>
								<a href="<?php echo $LPaData['sChat'];?>" class="TableBtnBg">
									<?php echo aGROUP['CHATHISTORY'];?>
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