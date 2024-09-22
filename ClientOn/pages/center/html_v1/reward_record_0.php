<?php $aData = json_decode($sData,true);?>
<!-- 返點紀錄 -->
<div class="HavePageBox">
	<div class="rechargeRecordBox">
		<div class="rechargeRecordSearchBox">
			<form action="<?php echo $aUrl['sPage'];?>" method="POST">
				<table class="FormSearchTable">
					<tbody>
						<tr>
							<td style="width:100%;">
								<div class="Ipt">
									<input type="text" name="sAccount" value="<?php echo $sAccount;?>" placeholder="<?php echo aRECORD['SEARCHACCOUNT'];?>">
								</div>
							</td>
							<td>
								<div class="FormSearchBtn">
									<input type="submit">
									<div class="FormSearchBtnTxt"><i class="fas fa-search"></i></div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</div>
		<div class="FormBox">
			<table class="FormTable">
				<thead>
					<tr>
						<th><?php echo aRECORD['DATE'];?></th>
						<th><?php echo aRECORD['MEMBER'];?></th>
						<th><?php echo aRECORD['MONEY'];?></th>

					</tr>
				</thead>
				<tbody>
					<?php
						foreach($aData as $LPnId => $LPaDetail)
						{
					?>
							<tr>
								<td class="FormTdDate TextAlignRight">
									<div class="WordBreakBreakAll">
										<div><?php echo date('Y-m-d',$LPaDetail['nCreateTime']);?></div>
										<div class="FormFontTime"><?php echo date('H:i:s',$LPaDetail['nCreateTime']);?></div>
									</div>
								</td>
								<td>
									<div class="WordBreakBreakAll"><?php echo $aMemberData[$LPaDetail['nFromUid']]['sAccount'];?></div>
								</td>
								<td class="FormFontRed">
									<div class="WordBreakBreakAll"><?php echo $LPaDetail['nDelta'];?></div>
								</td>
							</tr>
					<?php
						}
					?>
				</tbody>
				<tfoot>
					<tr>

						<td></td>
						<td><?php echo aRECORD['SUBTOTAL'];?></td>
						<td class="FormFontRed"><?php echo $aTotalData['nSubTotal'];?></td>
					</tr>
					<tr>

						<td></td>
						<td><?php echo aRECORD['TOTAL'];?></td>
						<td class="FormFontRed"><?php echo $aTotalData['nTotal'];?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<?php echo $aPageList['sHtml'];?>