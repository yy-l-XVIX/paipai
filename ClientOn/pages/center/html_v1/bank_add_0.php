<?php
	$aData = json_decode($sData,true);
?>
<!-- 新增銀行帳戶 -->
<form method="post" action="<?php echo $aUrl['sFormAct'];?>" class="JqForm" id="JqBankAddForm" data-info="<?php echo aBANKADD['INFO'];?>">
	<div class="bankAddBox">
		<table class="infData">
			<tbody>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aBANKADD['SELBANK'];?></span>
							<!-- 若為必填才顯示 -->
							<span class="FontRed">*</span>
						</div>
					</td>
					<td class="infDataCell2">
						<div class="Sel">
							<select name="nBid" required>
								<option value="-1" selected disabled><?php echo PLEASESELECT;?></option>
								<?php
									foreach($aBank as $LPnId => $LPaDetail)
									{
								?>
										<option value="<?php echo $LPnId;?>">(<?php echo $LPaDetail['sCode'];?>)<?php echo $LPaDetail['sName0'];?></option>
								<?php
									}
								?>
							</select>
							<div class="SelDecro"></div>
						</div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aBANKADD['BANKBRANCH'];?></span>
							<!-- 若為必填才顯示 -->
							<span class="FontRed">*</span>
						</div>
					</td>
					<td class="infDataCell2">
						<!-- 可更改 -->
						<div class="Ipt">
							<input type="text" name="sName2" value="" placeholder="<?php echo aBANKADD['BANKBRANCH'];?>" required>
						</div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aBANKADD['CARDNAME'];?></span>
							<!-- 若為必填才顯示 -->
							<span class="FontRed">*</span>
						</div>
					</td>
					<td class="infDataCell2">
						<!-- 可更改 -->
						<div class="Ipt">
							<input type="text" name="sName1" value="" placeholder="<?php echo aBANKADD['CARDNAMEINFO'];?>" required>
						</div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aBANKADD['CARDNUM'];?></span>
							<!-- 若為必填才顯示 -->
							<span class="FontRed">*</span>
						</div>
					</td>
					<td class="infDataCell2">
						<!-- 可更改 -->
						<div class="Ipt">
							<input type="text" name="sName0" value="" placeholder="<?php echo aBANKADD['CARDNUMINFO'];?>" required>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<div class="bankAddBlock">
			<div class="bankAddTit">
				<span><?php echo aBANKADD['CARDIMAGE'];?></span>
				<!-- 若為必填才顯示 -->
				<span class="FontRed">*</span>
			</div>
			<div class="FileImg">
				<img class="JqPreviewImage" data-file="0" src="">
			</div>
			<div class="FileBtnAdd JqFileActive">
				<input type="file" name="sFile" class="JqFile" data-filebtn="0" accept="image/*" required>
				<div class="original"><?php echo UPLOADIMG;?></div>
				<div class="change"><?php echo CHANGEIMG;?></div>
			</div>
		</div>
		<div class="BtnActBox">
			<div class="BtnAct JqSubmit"><?php echo SUBMIT;?></div>
		</div>
	</div>
</form>