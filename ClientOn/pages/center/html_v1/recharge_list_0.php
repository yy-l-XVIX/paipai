<?php $aData = json_decode($sData,true);?>
<!-- 購買紀錄 -->
<div class="HavePageBox">
	<div class="rechargeListBox">
		<div class="rechargeListSearchBox">
			<form action="<?php echo $aUrl['sPage'];?>" method="POST">
				<table class="FormSearchTable">
					<tbody>
						<tr>
							<td style="width:50%;">
								<!-- 方案 -->
								<div class="Sel">
									<select name="nUkid" >
										<option value="0"><?php echo aRECHARGELIST['SELECTUKID'];?></option>
										<?php
										foreach ($aKindData as $LPnUkid => $LPaKind)
										{
											?>
											<option value="<?php echo $LPnUkid;?>" <?php echo $LPaKind['sSelect'];?>><?php echo $LPaKind['sName0'];?></option>
											<?php
										}
										?>
									</select>
									<div class="SelDecro"></div>
								</div>
							</td>
							<td style="width:50%;">
								<!-- 付款方式 -->
								<div class="Sel">
									<select name="sPayType">
										<option value=""><?php echo aRECHARGELIST['SELECTPAYTYPE'];?></option>
										<?php
										foreach ($aPayType as $LPsName1 => $LPaTunnel)
										{
												?>
												<option value="<?php echo $LPsName1;?>" <?php echo $LPaTunnel['sSelect'];?>><?php echo $LPaTunnel['sValue'];?></option>
												<?php
										}
										?>
									</select>
									<div class="SelDecro"></div>
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
				<input type="hidden" name="sPage" value="<?php echo $aUrl['sPage'];?>">
				<thead>
					<tr>
						<th><?php echo aRECHARGELIST['DATE'];?></th>
						<th><?php echo aRECHARGELIST['KIND'];?></th>
						<th><?php echo aRECHARGELIST['PAYTYPE'];?></th>
						<th><?php echo aRECHARGELIST['MONEY'];?></th>
						<th><?php echo aRECHARGELIST['STATUS'];?></th>
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
										<div><?php echo $LPaDetail['sUpdateDate'];?></div>
										<div class="FormFontTime"><?php echo $LPaDetail['sUpdateTime'];?></div>
									</div>
								</td>
								<td>
									<div class="WordBreakBreakAll"><?php echo $aKindData[$LPaDetail['nUkid']]['sName0'];?></div>
								</td>
								<td>
									<div class="WordBreakBreakAll"><?php echo $LPaDetail['sPayTypeName'];?></div>
								</td>
								<td class="FormFontRed">
									<div class="WordBreakBreakAll"><?php echo number_format($LPaDetail['nMoney']);?></div>
								</td>
								<td>
									<div class="WordBreakBreakAll <?php echo $aStatus[$LPaDetail['nStatus']]['sClass'];?>"><?php echo $aStatus[$LPaDetail['nStatus']]['sText'];?></div>
								</td>
							</tr>
					<?php
						}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td></td>
						<td><?php echo aRECHARGELIST['SUBTOTAL'];?></td>
						<td class="FormFontRed"><?php echo number_format($aTotalData['nSubTotal']);?></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td><?php echo aRECHARGELIST['TOTAL'];?></td>
						<td class="FormFontRed"><?php echo number_format($aTotalData['nTotal']);?></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<?php echo $aPageList['sHtml'];?>