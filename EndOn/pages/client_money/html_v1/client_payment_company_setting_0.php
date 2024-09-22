<?php
	$aData = json_decode($sData,true);
?>
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
						<th><?php echo aPAYMENTCOMPANYSETTING['NAME'];?></th>
						<th><?php echo aPAYMENTCOMPANYSETTING['ACCOUNT'];?></th>
						<?php
							if(false)
							{
						?>
								<th><?php echo aPAYMENTCOMPANYSETTING['MAX'];?></th>
								<th><?php echo aPAYMENTCOMPANYSETTING['MIN'];?></th>
								<th><?php echo aPAYMENTCOMPANYSETTING['DAYLIMITMONEY'];?></th>
						<?php
							}
						?>
						<th><?php echo aPAYMENTCOMPANYSETTING['TOTALLIMITMONEY'];?></th>
						<th><?php echo aPAYMENTCOMPANYSETTING['TOTALLIMITTIMES'];?></th>
						<th><?php echo aPAYMENTCOMPANYSETTING['DAYLIMITTIMES'];?></th>
						<th><?php echo aPAYMENTCOMPANYSETTING['TOTALMONEY'];?></th>
						<th><?php echo aPAYMENTCOMPANYSETTING['TOTALTIMES'];?></th>
						<th><?php echo aPAYMENTCOMPANYSETTING['DAYTIMES'];?></th>
						<th><?php echo STATUS;?></th>
						<th><?php echo CREATETIME;?></th>
						<th><?php echo UPDATETIME;?></th>
						<th><?php echo OPERATE;?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach($aData as $LPnId => $LPaDetail)
					{
					?>
						<tr>
							<td><?php echo $LPaDetail['sName0'];?></td>
							<td><?php echo $LPaDetail['sAccount0'];?></td>
							<?php
								if(false)
								{
							?>
									<td><?php echo $LPaDetail['nMax'];?></td>
									<td><?php echo $LPaDetail['nMin'];?></td>
									<td><?php echo $LPaDetail['nDayLimitMoney'];?></td>
							<?php
								}
							?>
							<td><?php echo $LPaDetail['nTotalLimitMoney'];?></td>
							<td><?php echo $LPaDetail['nTotalLimitTimes'];?></td>
							<td><?php echo $LPaDetail['nDayLimitTimes'];?></td>
							<td><?php echo $LPaDetail['nTotalMoney'];?></td>
							<td><?php echo $LPaDetail['nTotalTimes'];?></td>
							<td><?php echo $LPaDetail['nDayTimes'];?></td>
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