<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aPAYMENTONLINETUNNEL['TUNNELVALUE'];?></span>
			<div class="Sel">
				<select name="nPid">
					<option value="-1" ><?php echo ALL;?></option>
					<?php
						foreach($aPayment as $LPnId => $LPaDetail)
						{
					?>
							<option value="<?php echo $LPnId;?>" <?php echo $LPaDetail['sSelect'];?> ><?php echo $LPaDetail['sName0'];?></option>
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
					<option value="-1" ><?php echo PLEASESELECT;?></option>
					<?php
						foreach($aOnline as $LPnId => $LPaDetail)
						{
					?>
							<option value="<?php echo $LPnId;?>" <?php echo $LPaDetail['sSelect'];?> ><?php echo $LPaDetail['sText'];?></option>
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
						<th><?php echo aPAYMENTONLINETUNNEL['TUNNELKEY'];?></th>
						<th><?php echo aPAYMENTONLINETUNNEL['TUNNELVALUE'];?></th>
						<th><?php echo aPAYMENTONLINETUNNEL['TUNNELMIN'];?></th>
						<th><?php echo aPAYMENTONLINETUNNEL['TUNNELMAX'];?></th>
						<th><?php echo STATUS;?></th>
						<th><?php echo OPERATE;?></th>
						<th><?php echo aPAYMENTONLINETUNNEL['UPDATETIME'];?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($aData as $LPnPid => $LPaDetail)
					{
					?>
						<tr>
							<td colspan="7" class="BgBlue FontWhite"><?php echo $aPayment[$LPnPid]['sName0'];?></td>
						</tr>
						<?php
						foreach($LPaDetail as $LPnId => $LPaTunnel)
						{
							?>
							<tr>
								<td><?php echo $LPaTunnel['sKey'];?></td>
								<td><?php echo $LPaTunnel['sValue'];?></td>
								<td><?php echo $LPaTunnel['nMin'];?></td>
								<td><?php echo $LPaTunnel['nMax'];?></td>
								<td class="<?php echo aONLINE[$LPaTunnel['nOnline']]['sClass'];?>"><?php echo aONLINE[$LPaTunnel['nOnline']]['sText'];?></td>
								<td>
									<a href="<?php echo $LPaTunnel['sIns'];?>" class="TableBtnBg">
										<i class="fas fa-pen"></i>
									</a>
									<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaTunnel['sDel'];?>">
										<i class="fas fa-times"></i>
									</div>
								</td>
								<td><?php echo $LPaTunnel['sUpdateTime'];?></td>
							</tr>
							<?php
						}
						?>
					<?php
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<!-- <?php #echo $aPageList['sHtml'];?> -->