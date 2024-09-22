<?php $aData = json_decode($sData,true);?>
<!-- 點數兌換 -->
<div class="accessBox">
	<div class="accessBlock">
		<div class="accessTopic">*<?php echo aRECHARGE['INFO'];?>*</div>
		<?php
		if(false)
		{
		?>
		<div class="accessKindBox">
			<?php
			foreach ($aPayMethod as $LPnType => $LPaPayMethod)
			{
				if ($LPaPayMethod['sSelect'] == 'active')
				{
					?>
					<div class="accessKindBtn active"><?php echo $LPaPayMethod['sText'];?></div>
					<?php
				}
				else
				{
					?>
					 <a href="<?php echo $LPaPayMethod['sUrl'];?>" class="accessKindBtn"><?php echo $LPaPayMethod['sText'];?></a>
					<?php
				}
			}
			?>
		</div>
		<?php
		}
		?>
		<form id="JqPointchargeForm">
			<input type="hidden" name="sJWT" value="<?php echo $sJWTAct;?>">
			<input type="hidden" name="sPage" value="<?php echo $aUrl['sPage'];?>">
			<input type="hidden" name="sAct" value="<?php echo $aUrl['sAct'];?>">
			<input type="hidden" name="nKid" value="<?php echo $nKid;?>">
			<input type="hidden" name="nPid" value="<?php echo $aData['nId'];?>">
			<div class="accessData">
				<table class="accessTable">
					<tbody class="accessTbody">
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['WAY'];?>:</div>
							</td>
							<td class="accessTd">
								<div class="Sel">
									<select name=""onchange="location.href=this.value">
										<?php
										foreach ($aPayMethod as $LPnType => $LPaPayMethod)
										{
											if ($LPaPayMethod['sSelect'] == 'selected')
											{
												echo '<option class="accessKindBtn" selected>'.$LPaPayMethod['sText'].'</option>';
											}
											else
											{
												echo '<option class="accessKindBtn" value="'.$LPaPayMethod['sUrl'].'">'.$LPaPayMethod['sText'].'</option>';
											}
										}
										?>
									</select>
									<div class="SelDecro"></div>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['BANK']?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input class="JqCopyMe" type="text" value="<?php echo $aData['sBankName0'];?>" disabled>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['NAME']?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input class="JqCopyMe" type="text" value="<?php echo $aData['sPaymentName0'];?>" disabled>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['ACCOUNT']?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input class="JqCopyMe" type="text" value="<?php echo $aData['sAccount0'];?>" disabled>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['METHOD'];?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input type="text" value="<?php echo $aData['sName0'];?>" disabled>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['MONEY'];?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input class="JqMoney" type="text" value="<?php echo number_format($aData['nMoney']);?>" disabled>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['FEE']?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input  type="text" value="<?php echo $aSystem['aParam']['nRechargeFee'];?>" disabled>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['TOTAL']?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input  type="text" value="<?php echo number_format($aData['nTotalMoney']);?>" disabled>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['MEMO']?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input type="text" name="sMemo" placeholder="<?php echo aRECHARGE['MEMOINFO'];?>" >
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="accessBtnBox">
				<a href="javascript:void(0)" class="BtnAct JqSubmit"><?php echo aRECHARGE['GOPAY'];?></a>
			</div>
		</form>
	</div>
</div>