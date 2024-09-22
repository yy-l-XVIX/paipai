<?php $aData = json_decode($sData,true);?>
<!-- 線上入款 -->
<div class="accessBox">
	<div class="accessBlock">
		<div class="accessTopic">*<?php echo aRECHARGE['INFO'];?>*</div>
		<form id="JqPayOnlineForm">
			<input type="hidden" name="sJWT" value="<?php echo $sJWTAct;?>">
			<input type="hidden" name="sPage" value="<?php echo $aUrl['sPage'];?>">
			<input type="hidden" name="sAct" value="<?php echo $aUrl['sAct'];?>">
			<input type="hidden" name="nKid" value="<?php echo $nKid;?>">
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
								<div class="accessDataTit"><?php echo aRECHARGE['PAYMENT'];?>:</div>
							</td>
							<td class="accessTd">
								<div class="Sel">
									<select name="nPid" class="JqSelectPayment">
										<option value="0"><?php echo aRECHARGE['DEFAULT'];?></option>
										<?php
										foreach ($aData['aPayment'] as $LPnId => $LPaPayment)
										{
											?>
											<option value="<?php echo $LPnId;?>" <?php echo $LPaPayment['sSelect'];?>><?php echo $LPaPayment['sName0'];?></option>
											<?php
										}
										?>
									</select>

									<div class="SelDecro"></div>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['TUNNEL'];?>:</div>
							</td>
							<td class="accessTd">
								<div class="Sel">
									<select name="nTunnel">
										<option value="0"><?php echo aRECHARGE['DEFAULT'];?></option>
										<?php
										foreach ($aData['aTunnel'] as $LPnId => $LPaTunnel)
										{
											?>
											<option value="<?php echo $LPnId;?>" <?php echo $LPaTunnel['sSelect'];?>><?php echo $LPaTunnel['sValue'];?></option>
											<?php
										}
										?>
									</select>
									<div class="SelDecro"></div>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['METHOD'];?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input type="text" name="nMoney" value="<?php echo $aData['sName0'];?>" disabled>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aRECHARGE['MONEY'];?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input type="text" name="nMoney" value="<?php echo $aData['nMoney'];?>" disabled>
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