<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage']; ?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aBANK['ACCOUNT'];?></span>
			<div class="Ipt">
				<input type="text" name="sAccount" value="<?php echo $sAccount;?>" placeholder="<?php echo aBANK['ACCOUNT'];?>">
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aBANK['NAME0'];?></span>
			<div class="Ipt">
				<input type="text" name="sName0" value="<?php echo $sName0;?>" placeholder="<?php echo aBANK['NAME0'];?>">
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aBANK['BANKNAME'];?></span>
			<div class="Sel">
				 <select name="nBid">
					<?php
					foreach ($aBank as $LPnBid => $LPaBank)
					{
						?>
						<option value="<?php echo $LPnBid;?>" <?php echo $LPaBank['sSelect'];?> ><?php echo $LPaBank['sTitle'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo STATUS;?></span>
			<div class="Sel">
				<select name="nOnline">
					<option value="-1"><?php echo aBANK['SELSTATUS'];?></option>
					<?php
					foreach ($aOnline as $LPnOnline => $LPaOnline)
					{
						?>
						<option value="<?php echo $LPnOnline;?>" <?php echo $LPaOnline['sSelect'];?> ><?php echo $LPaOnline['sText'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>

		<input type="submit" class="BtnAny" value="<?php echo SEARCH;?>">
	</div>
</form>
<!-- 新增按鈕 -->
<div class="Block MarginBottom10">
	<a href="<?php echo $aUrl['sIns'];?>" class="BtnAdd"><?php echo INS.$sHeadTitle;?></a>
</div>

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
						<th><?php echo aBANK['BANKNAME'];?></th>
						<th><?php echo aBANK['NAME2'];?></th>
						<th><?php echo aBANK['NAME1'];?></th>
						<th><?php echo aBANK['NAME0'];?></th>
						<th><?php echo STATUS;?></th>
						<th><?php echo CREATETIME;?></th>
						<th><?php echo UPDATETIME;?></th>
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
							<td><?php echo $LPaData['sAccount'];?></td>
							<td><?php echo $LPaData['sBank'];?></td>
							<td><?php echo $LPaData['sName2'];?></td>
							<td><?php echo $LPaData['sName1'];?></td>
							<td><?php echo $LPaData['sName0'];?></td>
							<td class="<?php echo $aOnline[$LPaData['nOnline']]['sClass'];?>"><?php echo $aOnline[$LPaData['nOnline']]['sText'];?></td>
							<td><?php echo $LPaData['sCreateTime'];?></td>
							<td><?php echo $LPaData['sUpdateTime'];?></td>
							<td>
								<a href="<?php echo $LPaData['sIns'];?>" class="TableBtnBg">
									<i class="fas fa-pen"></i>
								</a>
								<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaData['sDel'];?>">
									<i class="fas fa-times"></i>
								</div>
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