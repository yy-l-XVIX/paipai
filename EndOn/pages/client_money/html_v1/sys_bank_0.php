<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aSYSBANK['NAME'];?></span>
			<div class="Ipt">
				<input type="text" name="sName0" value="<?php echo $sName0;?>" >
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aSYSBANK['CODE'];?></span>
			<div class="Ipt">
				<input type="text" name="sCode" value="<?php echo $sCode;?>" >
			</div>
		</div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo STATUS;?></span>
			<div class="Sel">
				<select name="nOnline">
					<option value="-1" ><?php echo PLEASESELECT;?></option>
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
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aSYSBANK['TYPE0']['sTitle'];?></span>
			<div class="Sel">
				<select name="nType0">
					<option value="-1" ><?php echo PLEASESELECT;?></option>
					<?php
						foreach($aType0 as $LPnStatus => $LPaDetail)
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
						<th><?php echo aSYSBANK['NAME'];?></th>
						<th><?php echo aSYSBANK['CODE'];?></th>
						<th><?php echo aSYSBANK['TYPE0']['sTitle'];?></th>
						<th><?php echo STATUS;?></th>
						<th><?php echo CREATETIME;?></th>
						<th><?php echo UPDATETIME;?></th>
						<th><?php echo OPERATE;?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($aData as $LPnLid => $LPaDetail)
					{
					?>
						<tr>
							<td><?php echo $LPaDetail['sName0'];?></td>
							<td><?php echo $LPaDetail['sCode'];?></td>
							<td><?php echo aSYSBANK['TYPE0'][$LPaDetail['nType0']]['sText'];?></td>
							<td class="<?php echo $aOnline[$LPaDetail['nOnline']]['sClass']; ?>"><?php echo $aOnline[$LPaDetail['nOnline']]['sText'];?></td>
							<td><?php echo $LPaDetail['sCreateTime'];?></td>
							<td><?php echo $LPaDetail['sUpdateTime'];?></td>
							<td>
								<a href="<?php echo $LPaDetail['sIns'];?>" class="TableBtnBg">
									<i class="fas fa-pen"></i>
								</a>
								<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaDetail['sDel'];?>">
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