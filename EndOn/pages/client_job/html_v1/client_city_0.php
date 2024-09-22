<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aCITY['NAME'];?></span>
			<div class="Ipt">
				<input type="text" name="sName0" value="<?php echo $sName0;?>" placeholder="<?php echo aCITY['NAME'];?>">
			</div>
		</div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aCITY['LOCATION'];;?></span>
			<div class="Sel">
				<select name="nLid">
					<option value="0" ><?php echo aCITY['SELECTLOCATION'];?></option>
					<?php
					foreach ($aLocation as $LPnLid => $LPaLocation)
					{
						?>
						<option value="<?php echo $LPnLid;?>" <?php echo $LPaLocation['sSelect'];?> ><?php echo $LPaLocation['sName0'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo STATUS;?></span>
			<div class="Sel">
				<select name="nOnline">
					<option value="-1" ><?php echo aCITY['SELECTONLINE'];?></option>
					<?php
						foreach ($aOnline as $LPnStatus => $LPaDetail)
						{
					?>
						<option value="<?php echo $LPnStatus;?>" <?php echo $LPaDetail['sSelect'];?> ><?php echo $LPaDetail['sText'];?></option>
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
	<a href="<?php echo $aUrl['sIns'];?>" class="BtnAdd"><?php echo INS.$sHeadTitle;;?></a>
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
						<th><?php echo aCITY['NAME'];?></th>
						<th><?php echo aCITY['LOCATION'];?></th>
						<th><?php echo STATUS;?></th>
						<th><?php echo CREATETIME;?></th>
						<th><?php echo UPDATETIME;?></th>
						<th><?php echo OPERATE;?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($aData as $LPnLid => $LPaCity)
					{
					?>
						<tr>
							<td><?php echo $LPnLid;?></td>
							<td><?php echo $LPaCity['sName0'];?></td>
							<td><?php echo $aLocation[$LPaCity['nLid']]['sName0'];?></td>
							<td class="<?php echo $aOnline[$LPaCity['nOnline']]['sClass']; ?>"><?php echo $aOnline[$LPaCity['nOnline']]['sText'];?></td>
							<td><?php echo $LPaCity['sCreateTime'];?></td>
							<td><?php echo $LPaCity['sUpdateTime'];?></td>
							<td>
								<a href="<?php echo $LPaCity['sIns'];?>" class="TableBtnBg">
									<i class="fas fa-pen"></i>
								</a>
								<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaCity['sDel'];?>">
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