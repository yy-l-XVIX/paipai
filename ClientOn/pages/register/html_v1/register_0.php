<?php $aData = json_decode($sData,true);?>
<!-- 註冊帳號 -->
<div class="accessBox">
	<div class="accessBlock">
		<form id="JqRegisterForm" data-info="<?php echo aREGISTER['INFO'];?>" action="<?php echo $aUrl['sAct'];?>">
			<input type="hidden" name="sPostJWT" value="<?php echo $sJWT;?>">
			<input type="hidden" name="sVcodeJWT" value="<?php echo $sVcodeJWT;?>">
			<input type="hidden" name="nSMSTime" value="<?php echo $aSystem['aParam']['nSMSTime'];?>">
			<input type="hidden" name="nKid" value="<?php echo $nKid;?>">
			<div class="accessData">
				<table class="accessTable">
					<tbody class="accessTbody">
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aREGISTER['ACCOUNT'];?></div>
							</td>
							<td class="accessTd JqNotice">
								<div class="Ipt">
									<input type="text" name="sAccount" placeholder="<?php echo aREGISTER['ACCOUNTRULE'];?>">
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<!-- 若點選獲取驗證碼,VeriBtn+active -->
								<div class="BtnAny VeriBtn JqGetVcode">
									<span class="VeriBtnGet"><?php echo aREGISTER['GETVCODE'];?></span>
									<span class="VeriBtnAgain JqCounting"></span>
								</div>
							</td>
							<td class="accessTd JqNotice">
								<div class="Ipt">
									<input type="text" name="nVcode" placeholder="<?php echo aREGISTER['VCODE'];?>">
								</div>
								<div class="Notice Format"><?php echo aERROR['PHONEFORMATE'];?></div>
								<div class="Notice Exist"><?php echo aERROR['PHONEEXIST'];?></div>
								<div class="Notice Oncheck"><?php echo aERROR['VERIFYING'];?></div>
							</td>
						</tr>

						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aREGISTER['PASSWORD'];?></div>
							</td>
							<td class="accessTd JqNotice">
								<div class="Ipt">
									<input type="password" name="sPassword0" placeholder="<?php echo aREGISTER['PASSWORDRULE'];?>">
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aREGISTER['CONFIRMPASSWORD'];?></div>
							</td>
							<td class="accessTd JqNotice">
								<div class="Ipt">
									<input type="password" name="sPassword1" placeholder="<?php echo aREGISTER['PASSWORDRULE'];?>">
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aREGISTER['NAME0'];?></div>
							</td>
							<td class="accessTd JqNotice">
								<div class="Ipt">
									<input type="text" name="sName0" placeholder="<?php echo aREGISTER['NAMERULE']?>">
								</div>
							</td>
						</tr>
						<?php
						/*
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aREGISTER['NAME1'];?></div>
							</td>
							<td class="accessTd JqNotice">
								<div class="Ipt">
									<input type="text" name="sName1" placeholder="<?php echo aREGISTER['NAME1INFO'];?>">
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aREGISTER['TRANSPASSWORD'];?></div>
							</td>
							<td class="accessTd JqNotice">
								<div class="Ipt">
									<input type="password" name="sTransPassword0" placeholder="<?php echo aREGISTER['TRANSPASSWORDRULE'];?>">
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aREGISTER['CONFIRMTRANSPASSWORD'];?></div>
							</td>
							<td class="accessTd JqNotice">
								<div class="Ipt">
									<input type="password" name="sTransPassword1" placeholder="<?php echo aREGISTER['TRANSPASSWORDRULE'];?>">
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aREGISTER['PHONE'];?></div>
							</td>
							<td class="accessTd JqNotice">
								<div class="Ipt">
									<input type="text" name="sPhone">
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd">
								<div class="accessDataTit"><?php echo aREGISTER['WECHAT'];?></div>
							</td>
							<td class="accessTd">
								<div class="Ipt">
									<input type="text" name="sWechat">
								</div>
							</td>
						</tr>
						*/
						?>
						<?php
						if($sPromoCode != '')
						{
							?>
							<tr class="accessTr">
								<td class="accessTd">
									<div class="accessDataTit"><?php echo aREGISTER['PROMOCODE'];?></div>
								</td>
								<td class="accessTd JqNotice">
									<div class="Ipt">
										<input type="hidden" name="sPromoCode" value="<?php echo $sPromoCode;?>">
										<input type="text" name="sPromoCode" value="<?php echo $sPromoCode;?>" disabled>
									</div>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
			<div class="accessBtnBox">
				<a href="javascript:void(0)" class="BtnAct JqSubmit"><?php echo aREGISTER['TITLE'];?></a>
			</div>
		</form>
	</div>
</div>