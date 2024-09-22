<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="MarginBottom20">
	<div class="MarginBottom10">
		<?php
		foreach ($aDay as $LPsText => $LPaDate)
		{
			?>
			<span class="JqDate BtnKind <?php echo $LPaDate['sSelect'];?>" data-day="<?php echo $LPsText;?>" data-date0="<?php echo $LPaDate['sStartDay']?>" data-date1="<?php echo $LPaDate['sEndDay']?>">
				<?php echo aDAYTEXT[$LPsText];?>
			</span>
			<?php
		}
		?>
		<input type="hidden" name="sSelDay" value="<?php echo $sSelDay;?>">
	</div>
	<div>
		<div class="Ipt">
			<input type="text" name="sStartTime" class="JqStartTime" value="<?php echo $sStartTime;?>">
		</div>
		<span>~</span>
		<div class="Ipt">
			<input type="text" name="sEndTime" class="JqEndTime" value="<?php echo $sEndTime;?>">
		</div>
		<input type="submit" class="BtnAny" value="<?php echo SEARCH;?>">
	</div>
</form>
<!-- 收入總匯 -->
<div class="Information MarginBottom20">
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
						<th rowspan="2"></th>
						<th rowspan="2"><?php echo aREPORT['PRICE'];?></th>
						<th rowspan="2"><?php echo aREPORT['BUYCOUNT'];?></th>
						<th rowspan="2"><?php echo aREPORT['BUYAMOUNT'];?></th>
						<th colspan="3"><?php echo aREPORT['COMPANY'];?></th>
						<th colspan="3"><?php echo aREPORT['ONLINE'];?></th>
						<?php
						/*
						<th colspan="3"><?php echo aREPORT['POINT'];?></th>
						<th rowspan="2"><?php echo aREPORT['PROMO']?></th>
						<th rowspan="2"><?php echo aREPORT['PROMOTAX']?></th>
						*/
						?>
					</tr>
					<tr>
						<th><?php echo aREPORT['COUNT'];?></th>
						<th><?php echo aREPORT['MONEY'];?></th>
						<th><?php echo aREPORT['FEE'];?></th>
						<th><?php echo aREPORT['COUNT'];?></th>
						<th><?php echo aREPORT['MONEY'];?></th>
						<th><?php echo aREPORT['FEE'];?></th>
						<?php
						/*
						<th><?php echo aREPORT['COUNT'];?></th>
						<th><?php echo aREPORT['MONEY'];?></th>
						<th><?php echo aREPORT['FEE'];?></th>
						*/
						?>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($aData['aUserKind'] as $LPnLid => $LPaData)
					{
						?>
						<tr>
							<td><?php echo $LPaData['sName0'];?></td>
							<td><?php echo number_format($LPaData['nPrice']);?></td>
							<td><?php echo $LPaData['nTotalCount'];?></td>
							<td><?php echo $LPaData['nTotalMoney'];?></td>
							<td><?php echo $LPaData['nCompanyCount'];?></td>
							<td><?php echo $LPaData['nCompanyMoney'];?></td>
							<td><?php echo $LPaData['nCompanyFee'];?></td>
							<td><?php echo $LPaData['nOnlineCount'];?></td>
							<td><?php echo $LPaData['nOnlineMoney'];?></td>
							<td><?php echo $LPaData['nOnlineFee'];?></td>
							<?php
							/*
							<td><?php echo $LPaData['nPointCount'];?></td>
							<td><?php echo $LPaData['nPointMoney'];?></td>
							<td><?php echo $LPaData['nPointFee'];?></td>
							<td><?php echo $LPaData['nPromoMoney'];?></td>
							<td><?php echo $LPaData['nPromoTax'];?></td>
							*/
							?>
						</tr>
						<?php
					}
					?>
					<tr>
						<td><?php echo aREPORT['TOTAL'];?></td>
						<td></td>
						<td><?php echo $aTotal['aUserKind']['nTotalCount'];?></td>
						<td><?php echo $aTotal['aUserKind']['nTotalMoney'];?></td>
						<td><?php echo $aTotal['aUserKind']['nCompanyCount'];?></td>
						<td><?php echo $aTotal['aUserKind']['nCompanyMoney'];?></td>
						<td><?php echo $aTotal['aUserKind']['nCompanyFee'];?></td>
						<td><?php echo $aTotal['aUserKind']['nOnlineCount'];?></td>
						<td><?php echo $aTotal['aUserKind']['nOnlineMoney'];?></td>
						<td><?php echo $aTotal['aUserKind']['nOnlineFee'];?></td>
						<?php
						/*
						<td><?php echo $aTotal['aUserKind']['nPointCount'];?></td>
						<td><?php echo $aTotal['aUserKind']['nPointMoney'];?></td>
						<td><?php echo $aTotal['aUserKind']['nPointFee'];?></td>
						<td><?php echo $aTotal['aUserKind']['nPromoMoney'];?></td>
						<td><?php echo $aTotal['aUserKind']['nPromoTax'];?></td>
						*/
						?>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<!-- 支出總匯 -->
<div class="Information MarginBottom20 DisplayBlockNone">
	<table class="InformationTit">
		<tbody>
			<tr>
				<td class="InformationTitCell" style="width:calc(100%/1);">
					<div class="InformationName"><?php echo WITHDRAWALREPORT; ?></div>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="InformationScroll">
		<div class="InformationTableBox">
			<table>
				<thead>
					<tr>
						<th></th>
						<th><?php echo aREPORT['COUNT'];?></th>
						<th><?php echo aREPORT['FEE'];?></th>
						<th><?php echo aREPORT['MONEY'];?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($aData['aWithdrawal'] as $LPnStatus => $LPaData)
					{
						?>
						<tr>
							<td><a target="_blank" href="<?php echo $LPaData['sUrl'];?>"><?php echo $LPaData['sName0'];?></a></td>
							<td><?php echo $LPaData['nCount'];?></td>
							<td><?php echo $LPaData['nFee'];?></td>
							<td><?php echo $LPaData['nMoney'];?></td>
						</tr>
						<?php
					}
					?>
					<tr>
						<td><?php echo aREPORT['TOTAL'];?></td>
						<td><?php echo $aTotal['aWithdrawal']['nCount'];?></td>
						<td><?php echo $aTotal['aWithdrawal']['nFee'];?></td>
						<td><?php echo $aTotal['aWithdrawal']['nMoney'];?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
