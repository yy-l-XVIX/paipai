<?php $aData = json_decode($sData,true);?>
<!-- 點數兌換 -->
<div class="accessBox">
	<div class="accessBlock">
		<div class="accessTopic">*<?php echo aPOINTCHARGE['INFO'];?>*</div>
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
			<div class="accessData">
				<table class="accessTable">
					<tbody class="accessTbody">
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aPOINTCHARGE['WAY'];?>:</div>
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
								<div class="accessDataTit"><?php echo aPOINTCHARGE['USERMONEY']?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input class="JqMoney" type="text" value="<?php echo $aUser['nMoney'];?>" disabled>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aPOINTCHARGE['METHOD'];?>:</div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input type="text" name="sName0" value="<?php echo $aData['sName0'];?>" disabled>
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aPOINTCHARGE['MONEY'];?>:</div>
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
				<a href="javascript:void(0)" class="BtnAct JqSubmit"><?php echo aPOINTCHARGE['GOPAY'];?></a>
			</div>
		</form>
	</div>
</div>