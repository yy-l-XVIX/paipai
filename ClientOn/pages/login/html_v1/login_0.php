<?php $aData = json_decode($sData,true);?>
<!-- 登入 -->
<div class="accessBox login">
	<div class="accessLogo">
		<img src="images/logo.png" alt="">
	</div>
	<div class="accessBlock">
		<form id="JqLoginForm" method="post">
			<input type="hidden" name="sAct" value="<?php echo $aUrl['sAct'];?>" >
			<input type="hidden" name="sBackUrl" value="<?php echo $aData['sBackUrl'];?>">
			<div class="accessKindBox">
				<label for="nKid3" class="accessKindBtn <?php echo $aData['nKid']== 3 ?'active':'';?> JqLgnBtnKind">
					<input type="radio" id="nKid3" name="nKid" value="3" <?php echo $aData['nKid']== 3 ?'checked':'';?>>
					<span><?php echo aLOGIN['MANLOGIN'];?></span>
				</label>
				<label for="nKid1" class="accessKindBtn <?php echo $aData['nKid']== 1 ?'active':'';?> JqLgnBtnKind">
					<input type="radio" id="nKid1" name="nKid" value="1" <?php echo $aData['nKid']== 1 ?'checked':'';?>>
					<span><?php echo aLOGIN['BOSSLOGIN'];?></span>
				</label>
			</div>
			<div class="accessData">
				<table class="accessTable">
					<tbody class="accessTbody">
						<tr class="accessTr">
							<td class="accessTd"><i class="fas fa-user"></i></td>
							<td class="accessTd">
								<div class="Ipt">
									<input type="text" name="sAccount" placeholder="<?php echo aLOGIN['INPUTACCOUNT'];?>" value="<?php echo $aData['sAccount'];?>" required="required">
								</div>
							</td>
						</tr>
						<tr class="accessTr">
							<td class="accessTd"><i class="fas fa-lock"></i></td>
							<td class="accessTd">
								<div class="Ipt">
									<input type="password" placeholder="<?php echo aLOGIN['INPUTPASSWORD'];?>" name="sPassword" required="required">
								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="loginRem">
				<label for="rememberMe">
					<input type="checkbox" id="rememberMe" name="nRemember" <?php echo $aData['sCheck'];?>>
					<span><?php echo aLOGIN['REMEMBER'];?></span>
				</label>
			</div>
			<div class="accessBtnBox">
				<a href="javascript:void(0)"  class="BtnAct JqSubmit"><?php echo aLOGIN['LOGIN'];?></a>
			</div>
		</form>
	</div>
	<div class="loginBtnBox">
		<a href="<?php echo $aUrl['sRegister'];?>" class="loginBtn"><?php echo aLOGIN['REGISTER'];?></a>
		<a href="<?php echo $aUrl['sForgot'];?>" class="loginBtn"><?php echo aLOGIN['FORGOT'];?></a>
	</div>
</div>