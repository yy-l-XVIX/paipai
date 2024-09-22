<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aAREA['NAME'];?></span>
			<div class="Ipt">
				<input type="text" name="sName0" value="<?php echo $sName0;?>" placeholder="<?php echo aAREA['NAME'];?>">
			</div>
		</div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aAREA['CITY'];?></span>
			<div class="Sel">
				<select name="nCid">
					<option value="0" ><?php echo aAREA['SELECTLOCATION'];?></option>
					<?php
					foreach ($aCity as $LPnCid => $LPaCity)
					{
						?>
						<option value="<?php echo $LPnCid;?>" <?php echo $LPaCity['sSelect'];?> ><?php echo $LPaCity['sName0'];?></option>
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
					<option value="-1" ><?php echo aAREA['SELECTONLINE'];?></option>
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
						<th><?php echo aAREA['NAME'];?></th>
						<th><?php echo aAREA['CITY'];?></th>
						<th><?php echo STATUS;?></th>
						<th><?php echo CREATETIME;?></th>
						<th><?php echo UPDATETIME;?></th>
						<th><?php echo OPERATE;?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($aData as $LPnLid => $LPaArea)
					{
						?>
						<tr>
							<td><?php echo $LPnLid;?></td>
							<td><?php echo $LPaArea['sName0'];?></td>
							<td><?php echo $aCity[$LPaArea['nCid']]['sName0'];?></td>
							<td class="<?php echo $aOnline[$LPaArea['nOnline']]['sClass']; ?>"><?php echo $aOnline[$LPaArea['nOnline']]['sText'];?></td>
							<td><?php echo $LPaArea['sCreateTime'];?></td>
							<td><?php echo $LPaArea['sUpdateTime'];?></td>
							<td>
								<a href="<?php echo $LPaArea['sIns'];?>" class="TableBtnBg">
									<i class="fas fa-pen"></i>
								</a>
								<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaArea['sDel'];?>">
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