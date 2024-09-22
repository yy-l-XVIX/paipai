<?php $aData = json_decode($sData,true);?>
<div class="transferBox">
	<form action="<?php echo $aUrl['sAct'];?>" method="POST" id="JqPostForm">
		<div class="MoneyActBox">
			<table class="MoneyActTable">
				<tbody>
					<tr>
						<td class="MoneyActTdCell1">
							<div class="MoneyActTit"><?php echo aTRANSFER['ACCOUNT'];?></div>
						</td>
						<td class="MoneyActTdCell2 multiple">
							<div class="Ipt">
								<input type="text" name="sAccount" value="<?php echo $sAccount;?>" placeholder="<?php echo aTRANSFER['INPUTACCOUNT'];?>">
							</div>
							<a class="MoneyActBtn" href="<?php echo $aUrl['sTransferChoose']; ?>">
								<div class="MoneyActBtnTxt"><?php echo aTRANSFER['FRIENDS'];?></div>
							</a>
						</td>
					</tr>
				</tbody>
				<tbody>
					<tr>
						<td class="MoneyActTdCell1">
							<div class="MoneyActTit"><?php echo aTRANSFER['TRANSMONEY'];?></div>
						</td>
						<td class="MoneyActTdCell2">
							<div class="Ipt">
								<input type="number" min="0" name="nMoney" class="FontRedImp" placeholder="<?php echo aTRANSFER['TRANSINF'];?>">
							</div>
						</td>
					</tr>
					<tr>
						<td></td>
						<td class="MoneyActTdNotice FontWeight600">
							<span><?php echo aTRANSFER['MYMONEY'];?> : </span>
							<span><?php echo $aUser['nMoney'];?></span>
						</td>
					</tr>
				</tbody>
				<tbody>
					<tr>
						<td class="MoneyActTdCell1">
							<div class="MoneyActTit"><?php echo aTRANSFER['MEMO'];?></div>
						</td>
						<td class="MoneyActTdCell2">
							<div class="Ipt">
								<input type="text" name="sMemo" placeholder="<?php echo aTRANSFER['MEMO'];?>">
							</div>
						</td>
					</tr>
				</tbody>
				<tbody>
					<tr>
						<td class="MoneyActTdCell1">
							<div class="MoneyActTit">
								<span><?php echo aTRANSFER['TRANSPASSWORD'];?></span>
								<span class="FontRed">*</span>
							</div>
						</td>
						<td class="MoneyActTdCell2">
							<div class="Ipt">
								<input type="password" name="sTransPassword" placeholder="<?php echo aTRANSFER['ENTERPASSWORD'];?>">
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="BtnActBox">
			<div class="BtnAct JqSubmit"><?php echo aTRANSFER['SUBMIT'];?></div>
		</div>
	</form>
</div>