<?php $aData = json_decode($sData,true);?>
<div class="withdrawalBox">
	<form action="<?php echo $aUrl['sAct'];?>" method="POST" id="JqPostForm">
		<div class="MoneyActBox">
			<table class="MoneyActTable">
				<tbody>
					<tr>
						<td class="MoneyActTdCell1" style="position:relative;" colspan="2">
							<div class="MoneyActTit"><?php echo aWITHDRAWAL['SELECTBANK'];?></div>
							<?php
							if (sizeof($aData) < $aSystem['aParam']['nCardLimit'])
							{
								?>
								<a class="MoneyActBtn" href="<?php echo $aUrl['sBankAdd']; ?>">
									<div class="MoneyActBtnTxt"><?php echo aWITHDRAWAL['ADDBANK'];?></div>
								</a>
								<?php
							}
							?>
						</td>
					</tr>
				</tbody>
				<?php
				foreach ($aData as $LPnId => $LPaBank)
				{
					?>
					<tbody>
						<tr>
							<td class="withdrawalSelTdPick">
								<div class="withdrawalSelPick">
									<label for="bank<?php echo $LPnId; ?>">
										<input type="radio" id="bank<?php echo $LPnId;?>" class="JqSelectBank" name="nBid" value="<?php echo $LPnId; ?>">
									</label>
								</div>
							</td>
							<td class="withdrawalSelTdInf JqWithdrawalSelBtn">
								<div class="withdrawalSelInfName WordBreakBreakAll">
									<span><?php echo $aBankData[$LPaBank['nBid']]['sName0'];?></span>
									<span>(<?php echo $aBankData[$LPaBank['nBid']]['sCode'];?>)</span>
									<span><?php echo $LPaBank['sName2'];?></span>
								</div>
								<div class="withdrawalSelInfTxt WordBreakBreakAll">
									<span><?php echo aWITHDRAWAL['CARDNUMBER'];?> : </span>
									<span><?php echo $LPaBank['sName0'];?></span>
								</div>
							</td>
						</tr>
					</tbody>
					<?php
				}
				?>
			</table>
		</div>
		<div class="MoneyActBox">
			<table class="MoneyActTable">
				<tbody>
					<tr>
						<td class="MoneyActTdCell1">
							<div class="MoneyActTit"><?php echo aWITHDRAWAL['MONEY'];?></div>
						</td>
						<td class="MoneyActTdCell2">
							<div class="Ipt">
								<input type="number" name="nMoney" class="FontRedImp" placeholder="<?php echo aWITHDRAWAL['MONEYINF'];?>">
							</div>
						</td>
					</tr>
					<tr>
						<td></td>
						<td class="MoneyActTdNotice FontWeight600">
							<span><?php echo aWITHDRAWAL['MYMONEY'];?> : </span>
							<span><?php echo $aUser['nMoney'];?></span>
						</td>
					</tr>
				</tbody>
				<tbody>
					<tr>
						<td class="MoneyActTdCell1">
							<div class="MoneyActTit"><?php echo aWITHDRAWAL['FEE'];?></div>
						</td>
						<td class="MoneyActTdCell2">
							<div class="MoneyActTxt"><?php echo $aSystem['aParam']['nWithdrawalFee'];?></div>
						</td>
					</tr>
				</tbody>
				<tbody>
					<tr>
						<td class="MoneyActTdCell1">
							<div class="MoneyActTit">
								<span><?php echo aWITHDRAWAL['TRANSPASSWORD'];?></span>
								<span class="FontRed">*</span>
							</div>
						</td>
						<td class="MoneyActTdCell2">
							<div class="Ipt">
								<input type="password" name="sTransPassword" placeholder="<?php echo aWITHDRAWAL['ENTERPASSWORD'];?>">
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="BtnActBox">
			<div class="BtnAct JqSubmit"><?php echo aWITHDRAWAL['SUBMIT'];?></div>
		</div>
	</form>
</div>