<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aData['sUrl'];?>" method="post">
	<input type="hidden" name="sBackUrl" value="<?php echo $aData['sBackUrl'];?>" >
	<input type="hidden" name="sJWT" value="<?php echo $aData['sJWT'];?>" >
	<div class="loginBox">
		<div class="loginContainer">
			<div class="loginInner">
				<div class="loginWebName"><?php echo $aSystem['sTitle'];?></div>
				<div class="loginTable Table">
					<div>
						<div>
							<div class="loginCellIcon">
								<i class="far fa-user"></i>
							</div>
							<div class="loginCellWrite">
								<div class="Ipt">
									<input type="text" name="sAccount" required placeholder="<?php echo aLOGIN['ACCOUNT'];?>" value="<?php echo $aData['sAccount'];?>" >
								</div>
							</div>
						</div>
						<div>
							<div class="loginCellIcon">
								<i class="fas fa-unlock-alt"></i>
							</div>
							<div class="loginCellWrite">
								<div class="Ipt">
									<input type="password" name="sPassword" required placeholder="<?php echo aLOGIN['PASSWORD'];?>">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="loginRem">
					<label for="">
						<input type="checkbox" name="nRemember" <?php echo $aData['sCheck'];?> >
						<span><?php echo aLOGIN['REMEMBER'];?></span>
					</label>
				</div>
				<input type="submit" class="loginBtn" value="<?php echo SUBMIT;?>">
			</div>
		</div>
	</div>
</form>