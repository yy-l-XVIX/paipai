<?php $aData = json_decode($sData,true);?>
<!-- 會員中心 -->
<div class="centerBox">
	<!-- 若此會員擁有兩種身分centerProfileBox + active -->
	<div class="centerProfileBox <?php echo $sBothClass;?>">
		<table class="centerProfileTable">
			<tr>
				<td class="centerProfilePic">

					<!-- 若此人身份為雇主,selfieBox + boss -->
					<a class="selfieBox BG <?php echo $sUserCurrentRole;?>" style="background-image: url('<?php echo $aUser['sHeadImage'];?>');"  href="<?php echo $aUrl['sInf'];?>">
						<?php
						if($sUserCurrentRole == 'staff')
						{
						?>
							<!-- 若為下班selfieStatus + off , 若為工作中 selfieStatus + ing -->
							<div class="selfieStatus JqChangeStatus <?php echo $sStatusClass;?>"></div>
						<?php
						}
						?>
					</a>
				</td>
				<td class="centerProfileData">
					<a href="<?php echo $aUrl['sInf'];?>">
						<div class="MarginBottom5">
							<span class="JqCenterData1">
								<span><?php echo $aUser['sName0'];?></span>
								<span class="centerProfileKind"><?php echo $aUser['sRoleName'];?></span>
							</span>
						</div>
						<div>
							<span  class="JqCenterData2">
								<span><?php echo ACCOUNT;?>:</span>
								<span><?php echo $aUser['sAccount'];?></span>
							</span>
						</div>
						<div class="centerProfileDecro JqCenterProfileDecro">
							<i class="fas fa-chevron-right"></i>
						</div>
					</a>
				</td>
				<td class="centerProfileSetting">
					<a href="<?php echo $aUrl['sSetting'];?>">
						<i class="fas fa-cog"></i>
					</a>
				</td>
			</tr>
		</table>

		<!-- 若此會員擁有兩種身分centerProfileSub + active -->
		<table class="centerProfileSub <?php echo $sBothClass;?>">
			<tbody>
				<?php
				#若此會員擁有兩種身分才出現
				if($nHavClass == 2)
				{
				?>
				<tr>
					<td colspan="3" class="centerProfileSubSwitch">
						<a class="centerProfileSubSwitchBtn" href="<?php echo $aUrl['sCenter'];?>">
							<i class="fas fa-sync-alt"></i>
							<span><?php echo aCENTER['CHANGE'];?></span>
						</a>
					</td>
				</tr>
				<?php
				}
				?>
				<tr>
					<td class="centerProfileSubMenu">
						<a href="<?php echo $aUrl['sRegister'];?>" class="centerProfileSubMenuBtn">
							<div class="centerProfileSubLeft">
								<div class="centerProfileSubTit"><?php echo aCENTER['METHOD'];?></div>
								<div class="centerProfileSubTxt">
									<!-- 若尚未開通此身分, class + disable ,已開通 class + active  -->
									<?php
									$LPnCount = 1;
									foreach ($aUserKid as $LPnKid => $LPaUserKid)
									{
									?>
										<span class="<?php echo $LPaUserKid['sClass'];?>"><?php echo $LPaUserKid['sName0'];?></span>
										<?php
										if (sizeof($aUserKid) <= $LPnCount)
										{
											continue;
										}
										echo '<span>/</span>';
										$LPnCount ++;
									}
									?>
								</div>
							</div>
							<i class="fas fa-chevron-right centerProfileSubMenuBtnGo"></i>
						</a>
					</td>
					<?php
					/* 2021-03-18
					<td class="centerProfileSubMenu">
						<div class="centerProfileSubMenuBtn">
							<div class="centerProfileSubTit"><?php echo aCENTER['MONEY'];?></div>
							<div class="centerProfileSubTxt">
								<span class="centerProfileSubPoint active"><?php echo $aUser['nMoney'];?></span>
							</div>
						</div>
					</td>
					*/
					?>
					<td class="centerProfileSubMenu">
						<a href="<?php echo $aUrl['sPromo'];?>">
							<div class="centerProfileSubTit"><?php echo aCENTER['PROMO'];?></div>
							<div class="centerProfileSubTxt">
								<i class="fas fa-qrcode"></i>
							</div>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php
	$nCenterColAmount = 4;
	$sCenterClass = 'MarginBottom30';
	if($sUserCurrentRole == 'boss')
	{
		$nCenterColAmount = 3;
		$sCenterClass = 'MarginBottom120';
	}
	?>
	<div class="centerMenuBox <?php echo $sCenterClass; ?>">
		<table class="centerMenuTable">
			<tbody>
				<tr>
					<td style="width:calc(100%/<?php echo $nCenterColAmount; ?>);">
						<a href="<?php echo $aUrl['sFriend']?>" class="centerMenuBtn">
							<div class="centerMenuBtnIcon">
								<i class="fas fa-user-friends"></i>
								<?php
									if($nFriend > 0)
									{
								?>
								<div class="centerMenuBtnNotify"><?php echo ($nFriend > 99) ? '99+' : $nFriend;?></div>
								<?php
									}
								?>

							</div>
							<div class="centerMenuBtnTit">
								<div><?php echo aCENTER['FRIEND'];?></div>
							</div>
						</a>
					</td>
					<td style="width:calc(100%/<?php echo $nCenterColAmount; ?>);">
						<a href="<?php echo $aUrl['sBlock']?>" class="centerMenuBtn">
							<div class="centerMenuBtnIcon">
								<i class="fas fa-user-slash"></i>
							</div>
							<div class="centerMenuBtnTit">
								<div><?php echo aCENTER['BLOCK'];?></div>
							</div>
						</a>
					</td>
					<td style="width:calc(100%/<?php echo $nCenterColAmount; ?>);">
						<a href="<?php echo $aUrl['sMemberList']?>" class="centerMenuBtn">
							<div class="centerMenuBtnIcon">
								<i class="fas fa-search"></i>
							</div>
							<div class="centerMenuBtnTit">
								<div><?php echo aCENTER['MEMBERLIST'];?></div>
							</div>
						</a>
					</td>
					<?php
					if($sUserCurrentRole == 'staff')
					{
					?>
						<td style="width:calc(100%/<?php echo $nCenterColAmount; ?>);">
							<a href="<?php echo $aUrl['sSaved'];?>" class="centerMenuBtn">
								<div class="centerMenuBtnIcon">
									<i class="fas fa-heart"></i>
								</div>
								<div class="centerMenuBtnTit">
									<div><?php echo aCENTER['SAVED'];?></div>
								</div>
							</a>
						</td>
					<?php
					}
					?>
				</tr>
				<tr>
					<td style="width:calc(100%/<?php echo $nCenterColAmount; ?>);">
						<a href="<?php echo $aUrl['sService'];?>" class="centerMenuBtn">
							<div class="centerMenuBtnIcon">
								<i class="fas fa-clipboard-list"></i>
							</div>
							<div class="centerMenuBtnTit">
								<div><?php echo aCENTER['SERVICE'];?></div>
							</div>
						</a>
					</td>
					<?php
					if($sUserCurrentRole == 'staff')
					{
					?>
						<td style="width:calc(100%/<?php echo $nCenterColAmount; ?>);">
							<a href="<?php echo $aUrl['sJobRecord'];?>" class="centerMenuBtn">
								<div class="centerMenuBtnIcon">
									<i class="fas fa-file"></i>
								</div>
								<div class="centerMenuBtnTit">
									<div><?php echo aCENTER['JOBRECORD'];?></div>
								</div>
							</a>
						</td>
					<?php
					}
					?>
					<td style="width:calc(100%/<?php echo $nCenterColAmount; ?>);">
						<a href="<?php echo $aUrl['sAccountRecord'];?>" class="centerMenuBtn">
							<div class="centerMenuBtnIcon">
								<i class="fas fa-dollar-sign"></i>
							</div>
							<div class="centerMenuBtnTit">
								<div><?php echo aCENTER['RECORD'];?></div>
							</div>
						</a>
					</td>
					<td style="width:calc(100%/<?php echo $nCenterColAmount; ?>);">
						<a href="<?php echo $aUrl['sTerm'];?>" class="centerMenuBtn">
							<div class="centerMenuBtnIcon">
								<i class="fas fa-book"></i>
							</div>
							<div class="centerMenuBtnTit">
								<div><?php echo aCENTER['TERM'];?></div>
							</div>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<?php
	if($sUserCurrentRole == 'staff' && $aUser['nStatus'] != 11)
	{
	?>
		<div class="centerStatusBox">
			<table class="centerStatusTable">
				<tbody>
					<tr>
						<?php
						foreach ($aStatus as $LPnStatus => $LPaStatus)
						{
						?>
							<td class="centerStatusTdBtn JqChangeWork <?php echo $LPaStatus['sSelect'];?>" data-act="<?php echo $LPaStatus['sActUrl'];?>" style="width:calc(100%/<?php echo count($aStatus); ?>);">
								<div class="centerStatusBtn">
									<div class="centerStatusBtnTxt"><?php echo $LPaStatus['sText'];?></div>
								</div>
							</td>
						<?php
						}
						?>
					</tr>
				</tbody>
			</table>
		</div>
	<?php
	}
	?>

	<div class="centerBtnBox">
		<a href="<?php echo $aUrl['sLogout'];?>" class="BtnAct"><?php echo aCENTER['LOGOUT'];?></a>
	</div>
</div>