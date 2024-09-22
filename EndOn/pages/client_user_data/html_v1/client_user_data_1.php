<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage']; ?>" method="POST" class="Form MarginBottom20">
	<div class="Search">
		<div class="Block MarginBottom20" >
			<div class="Sel">
				<select name="sSearchType">
					<?php
					foreach ($aSearchType as $LPsSearchType => $LPaSearchType)
					{
						?>
						<option value="<?php echo $LPsSearchType;?>" <?php echo $LPaSearchType['sSelect'];?> ><?php echo $LPaSearchType['sTitle'];?></option>
						<?php
					}
					?>
				</select>
			</div>
			<div class="Ipt">
				<input type="text" name="sSearch" value="<?php echo $sSearch;?>">
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo STATUS;?></span>
			<div class="Sel">
				<select name="nStatus">
					<option value="-1"><?php echo aUSER['SELSTATUS'];?></option>
					<?php
					foreach ($aStatus as $LPnStatus => $LPaStatus)
					{
						?>
						<option value="<?php echo $LPnStatus;?>" <?php echo $LPaStatus['sSelect'];?> ><?php echo $LPaStatus['sTitle'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo aUSER['KIND'];?></span>
			<div class="Sel">
				 <select name="nKind">
					<?php
					foreach ($aKind as $LPnKind => $LPaKind)
					{
						?>
						<option value="<?php echo $LPnKind;?>" <?php echo $LPaKind['sSelect'];?> ><?php echo $LPaKind['sTitle'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<label for="inputIncludeDown" class="IptCheckbox">
				<input type="checkbox" id="inputIncludeDown" name="nInclude" value="1" <?php echo $sInclude;?>>
				<span><?php echo aUSER['INCLUDE'];?></span>
			</label>
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
						<th>No.</th>
						<th><?php echo '['.aUSER['LEVEL'].'] '.aUSER['KIND'];?></th>
						<th><?php echo aUSER['NAME0'];?></th>
						<th><?php echo ACCOUNT;?></th>
						<th><?php echo aUSER['PA'];?></th>
						<th><?php echo STATUS;?></th>
						<th><?php echo aUSER['MONEY'];?></th>
						<th><?php echo aUSER['PROMOCODE'];?></th>
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
							<td><?php echo '['.$LPaData['nLevel'].'] '.$LPaData['sKind'];?></td>
							<td><?php echo $LPaData['sName0'];?></td>
							<td><?php echo $LPaData['sAccount'];?></td>
							<td><a href="<?php echo $aPaData[$LPaData['nPa']]['sUrl']; ?>"><?php echo $aPaData[$LPaData['nPa']]['sAccount']; ?></a></td>
							<td><?php echo $LPaData['sStatus'];?></td>
							<td><?php echo $LPaData['nMoney'];?></td>
							<td><?php echo $LPaData['sPromoCode'];?></td>
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