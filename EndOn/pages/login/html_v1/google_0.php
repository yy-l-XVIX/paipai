<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aData['sUrl'];?>" method="post">
	<input type="hidden" name="sBackUrl" value="<?php echo $aData['sBackUrl'];?>" >
	<input type="hidden" name="sJWT" value="<?php echo $aData['sJWT'];?>" >
	<div class="loginBox">
		<div class="loginContainer">
			<div class="loginInner">
				<div class="loginWebName"><?php echo aGOOGLE['GOOGLE'];?></div>
				<div class="loginTable Table">
					<div>
						<div>
							<div class="loginCellIcon">
								<i class="far fa-user"></i>
							</div>
							<div class="loginCellWrite">
								<div class="Ipt">
									<input type="text" name="sAccount" disabled  value="<?php echo $aData['sAccount'];?>" >
								</div>
							</div>
						</div>
						<div>
							<div class="loginCellIcon">
								<i class="fab fa-google"></i>
							</div>
							<div class="loginCellWrite">
								<div class="Ipt">
									<input type="number" name="sGoogle" placeholder="<?php echo aGOOGLE['CODE'];?>">
								</div>
							</div>
						</div>
					</div>
				</div>
				<input type="submit" class="loginBtn MarginBottom20" value="<?php echo SUBMIT;?>">
				<a href="<?php echo $aData['sLogout'];?>" class="loginBtn"><?php echo aGOOGLE['LOGOUT'];?></a>
			</div>
		</div>
	</div>
</form>