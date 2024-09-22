<?php $aData = json_decode($sData,true);?>
<form id="JqPostForm" action="<?php echo $aUrl['sAct'].'&sJWT='.$sJWT;?>" data-info="* <?php echo aINF['NOTEMPTY'];?>" enctype="multipart/form-data">

	<input type="hidden" class="JqCopyUrl" value="<?php echo $sShareUrl; ?>" readonly>
	<input type="hidden" class="JqChangeRole" data-role="<?php echo $aChangeRole['sText'];?>" data-url="<?php echo $aChangeRole['sUrl'];?>">
	<!-- 個人資訊-基本資料 -->
	<div class="infProfileBox">
		<div class="infProfileTop">
			<!-- 若此人身份為雇主,selfieBox + boss -->
			<div class="infProfileImg selfieBox <?php echo $aData['sSelfieBoxClass'];?>">
				<img src="<?php echo $aData['sHeadImage'];?>" data-file="0" class="JqPreviewImage">
				<?php
				#若此人身份為人才
				if($aData['sRole'] == 'staff')
				{
					?>
					<!-- 若為下班selfieStatus + off , 若為工作中 selfieStatus + ing -->
					<div class="selfieStatus <?php echo $aData['sWorkStatus'];?>"></div>
					<?php
				}
				?>
			</div>
			<?php
			if($bEdit)
			{
				#編輯
				?>
				<div class="infProfileBtnBox">
					<div class="infProfileBtn JqFileActive">
						<input type="file" class="JqFile" name="sFile" data-filebtn="0" accept="image/*">
						<span><?php echo aINF['CHANGEHEAD'];?></span>
					</div>
				</div>
				<?php
			}
			else
			{
				#純顯示
				?>
				<div class="infProfileName">
					<span><?php echo $aData['sName0'];?></span>
					<span class="infProfileKind">
						<div><?php echo $aUserKind[$aData['nKid']]['sName0']; ?></div>
					</span>
				</div>

				<?php
				/*
				<div class="infProfileAccount">
					<span><?php echo ACCOUNT;?>:</span>
					<span><?php echo $aData['sAccount'];?></span>
				</div>
				*/
			}
			?>

			<?php
			if($aData['sRole'] == 'staff')
			{
				if ($aData['nType4'] == 1 || $bEdit)
				{
				?>
				<div class="MarginTopBottom10">
					<div class="BtnAny JqCopy"><?php echo aINF['SHARE'];?></div>
				</div>
				<?php
				}
				?>
				<div class="infProfileMenuBox">
					<?php
					if($bEdit)
					{
						#編輯
						?>
						<a href="<?php echo $aUrl['sSetting'];?>" class="infProfileMenu"><?php echo aINF['USERDATA'];?></a>
						<?php
					}
					?>

					<!-- 當前頁面 infProfileMenu + active -->
					<div class="infProfileMenu active"><?php echo aINF['BASICDATA'];?></div>
					<a href="<?php echo $aUrl['sPhoto'].'&sRole='.$aData['sRole']; ?>" class="infProfileMenu"><?php echo $aDataPending['sPhoto'];?> <?php echo aINF['PHOTO'];?></a>
					<a href="<?php echo $aUrl['sVideo'].'&sRole='.$aData['sRole']; ?>" class="infProfileMenu"><?php echo $aDataPending['sVideo'];?> <?php echo aINF['VIDEO'];?></a>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
	if($aData['sRole'] == 'staff')
	{
		if ($aData['nType4'] == 0 && !$bEdit)
		{
			?>
			<table class="infData">
				<tbody>
					<tr>
						<td colspan="2" class="infDataTxt TextAlignCenter"><?php echo aINF['PRIVATEACCOUNT'];?></td>
					</tr>
				</tbody>
			</table>
			<?php
		}
		else
		{
		?>
		<table class="infData">
			<tbody>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aINF['HEIGHT'];?></span>
						</div>
					</td>
					<td class="infDataCell2">
						<?php
						if($bEdit)
						{
							#編輯
							?>
							<div class="Ipt">
								<input type="text" placeholder="<?php echo aINF['PLEASEENTER'];?>" name="sHeight" value="<?php echo $aData['sHeight'];?>">
							</div>
							<?php
						}
						else
						{
							#純顯示
							?>
							<div class="infDataTxt"><?php echo $aData['sHeight'];?></div>
							<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aINF['WEIGHT'];?></span>
						</div>
					</td>
					<td class="infDataCell2">
						<?php
						if($bEdit)
						{
							#編輯
							?>
							<div class="Ipt">
								<input type="text" placeholder="<?php echo aINF['PLEASEENTER'];?>" name="sWeight" value="<?php echo $aData['sWeight'];?>">
							</div>
							<?php
						}
						else
						{
							#純顯示
							?>
							<div class="infDataTxt"><?php echo $aData['sWeight'];?></div>
							<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aINF['AGE'];?></span>
						</div>
					</td>
					<td class="infDataCell2">
						<div class="infDataTxt"><?php echo $aData['nAge'];?></div>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aINF['SIZE'];?></span>
						</div>
					</td>
					<td class="infDataCell2">
						<?php
						if($bEdit)
						{
							#編輯
							?>
							<div class="Ipt">
								<input type="text" placeholder="<?php echo aINF['SIZEINFO'];?>" name="sSize" value="<?php echo $aData['sSize'];?>">
							</div>
							<?php
						}
						else
						{
							#純顯示
							?>
							<div class="infDataTxt"><?php echo $aData['sSize'];?></div>
							<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aINF['CONTENT0'];?></span>
						</div>
					</td>
					<td class="infDataCell2">
						<?php
						if($bEdit)
						{
							#編輯
							?>
							<div class="Ipt">
								<input type="text" placeholder="<?php echo aINF['PLEASEENTER'];?>" name="sContent0" value="<?php echo $aData['sContent0'];?>">
							</div>
							<?php
						}
						else
						{
							#純顯示
							?>
							<div class="infDataTxt"><?php echo $aData['sContent0'];?></div>
							<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aINF['CONTENT1'];?></span>
						</div>
					</td>
					<td class="infDataCell2">
						<?php
						if($bEdit)
						{
							#編輯
							?>
							<div class="Ipt">
								<input type="text" placeholder="<?php echo aINF['PLEASEENTER'];?>" name="sContent1" value="<?php echo $aData['sContent1'];?>">
							</div>
							<?php
						}
						else
						{
							#純顯示
							?>
							<div class="infDataTxt"><?php echo $aData['sContent1'];?></div>
							<?php
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>

		<table class="infData">
			<tbody>
				<?php
				if($bEdit)
				{
					?>
					<tr>
						<td class="infDataCell1">
							<div class="infDataTit">
								<span><?php echo aINF['PRIVACY'];?></span>
							</div>
						</td>
						<td class="infDataCell2 infDataChooseCell">
							<div class="infDataTxt"><?php echo aINF['PRIVACYINFO'];?></div>
							<div class="infDataChoose">
								<label for="Profile">
									<input type="radio" id="Profile" value="1" name="nType4" <?php echo $aData['nType4'] == 1 ?'checked':'';?>>
									<span><?php echo aINF['SHOW'];?></span>
								</label>
								<label for="Profile">
									<input type="radio" id="Profile" value="0" name="nType4" <?php echo $aData['nType4'] == 0 ?'checked':'';?>>
									<span><?php echo aINF['HIDE'];?></span>
								</label>
							</div>
						</td>
					</tr>
					<?php
				}
				?>
				<!-- phone -->
				<?php
				if ($bEdit || (!$bEdit && $aData['nType0']==1))
				{
					?>
					<tr>
						<td class="infDataCell1">
							<div class="infDataTit">
								<span><?php echo aINF['PHONE'];?></span>
								<?php
								if($bEdit)
								{
									#純顯示
								?>
									<!-- 若為必填才顯示 -->
									<span class="FontRed">*</span>
								<?php
								}
								?>
							</div>
						</td>
						<td class="infDataCell2 infDataChooseCell">
							<?php
							if($bEdit)
							{
								#編輯
								?>
								<div class="Ipt">
									<input type="text" placeholder="<?php echo aINF['PLEASEENTER'];?>" name="sPhone" value="<?php echo $aData['sPhone'];?>" inputmode="tel" required>
								</div>
								<div class="infDataChoose">
									<label for="Phone1">
										<input type="radio" id="Phone1" value="1" name="nType0" <?php echo $aData['nType0'] == 1 ?'checked':'';?>>
										<span><?php echo aINF['SHOW'];?></span>
									</label>
									<label for="Phone0">
										<input type="radio" id="Phone0" value="0" name="nType0" <?php echo $aData['nType0'] == 0 ?'checked':'';?>>
										<span><?php echo aINF['HIDE'];?></span>
									</label>
								</div>
								<?php
							}
							else
							{
								#純顯示
							?>
								<div class="infDataTxt"><?php echo $aData['sPhone'];?></div>
							<?php
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
				<!-- wechat -->
				<?php
				if ($bEdit || (!$bEdit && $aData['nType1']==1))
				{
					?>
					<tr>
						<td class="infDataCell1">
							<div class="infDataTit">
								<span><?php echo aINF['WECHAT'];?></span>
							</div>
						</td>
						<td class="infDataCell2 infDataChooseCell">
							<?php
							if($bEdit)
							{
								#編輯
								?>
								<div class="Ipt">
									<input type="text" placeholder="<?php echo aINF['PLEASEENTER'];?>" name="sWechat" value="<?php echo $aData['sWechat'];?>" >
								</div>
								<div class="infDataChoose">
									<label for="Wechat1">
										<input type="radio" id="Wechat1" value="1" name="nType1" <?php echo $aData['nType1'] == 1 ?'checked':'';?>>
										<span><?php echo aINF['SHOW'];?></span>
									</label>
									<label for="Wechat0">
										<input type="radio" id="Wechat0" value="0" name="nType1" <?php echo $aData['nType1'] == 0 ?'checked':'';?>>
										<span><?php echo aINF['HIDE'];?></span>
									</label>
								</div>
								<?php
							}
							else
							{
								#純顯示
							?>
								<div class="infDataTxt"><?php echo $aData['sWechat'];?></div>
							<?php
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
				<!-- email -->
				<?php
				if ($bEdit || (!$bEdit && $aData['nType2']==1))
				{
					?>
					<tr>
						<td class="infDataCell1">
							<div class="infDataTit">
								<span><?php echo aINF['EMAIL'];?></span>
							</div>
						</td>
						<td class="infDataCell2 infDataChooseCell">
							<?php
							if($bEdit)
							{
								#編輯
								?>
								<div class="Ipt">
									<input type="text" placeholder="<?php echo aINF['PLEASEENTER'];?>" name="sEmail" value="<?php echo $aData['sEmail'];?>" inputmode="email">
								</div>
								<div class="infDataChoose">
									<label for="Email1">
										<input type="radio" id="Email1" value="1" name="nType2" <?php echo $aData['nType2'] == 1 ?'checked':'';?>>
										<span><?php echo aINF['SHOW'];?></span>
									</label>
									<label for="Email0">
										<input type="radio" id="Email0" value="0" name="nType2" <?php echo $aData['nType2'] == 0 ?'checked':'';?>>
										<span><?php echo aINF['HIDE'];?></span>
									</label>
								</div>
								<?php
							}
							else
							{
								#純顯示
								?>
								<div class="infDataTxt"><?php echo $aData['sEmail'];?></div>
								<?php
							}
							?>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
		}
	}
	?>
</form>
<?php
if($aData['sRole'] == 'boss')
{
	#雇主才顯示
	?>
	<table class="infData">
		<tbody>
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit"><?php echo aINF['SCORED'];?></div>
				</td>
				<td class="infDataCell2">
					<div class="infDataTxt">
						<?php
						for ($i=0; $i < 5; $i++)
						{
							$LPsImg = 'scoreActive';
							if ($aData['nScore'] <= $i)
							{
								$LPsImg = 'score';
							}
							?>
							<!-- 要有顏色就用這個 -->
							<div class="infScore">
								<img src="images/<?php echo $LPsImg;?>.png" alt="">
							</div>
							<?php
						}
						?>
					</div>
				</td>
			</tr>
			<tr>
				<td class="infDataCell1">
					<a class="infDataTit" href="<?php echo $aUrl['sComment'].'&nId='.$nId;?>"><?php echo aINF['CHECKSCORE'];?></a>
				</td>
				<td class="infDataCell2">
					<a class="infDataArrow" href="<?php echo $aUrl['sComment'].'&nId='.$nId;?>">
						<i class="fas fa-chevron-right"></i>
					</a>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}
?>

<?php
if($nId != $aUser['nId'])
{
	?>
	<div class="infBtnActBox">
		<!-- 若是"解除好友" class + disable -->
		<div class="infBtnAct infFriend JqAct <?php echo $aData['aFriendBtn']['sClass']?>" data-JqUrl="<?php echo $aData['aFriendBtn']['sUrl'];?>">
			<div class="infBtnActTxt"><?php echo $aData['aFriendBtn']['sText']?></div>

		</div>
		<div class="infBtnAct JqAct <?php echo $aData['aBlockBtn']['sClass']?>" data-JqUrl="<?php echo $aData['aBlockBtn']['sUrl'];?>">
			<div class="infBtnActTxt"><?php echo $aData['aBlockBtn']['sText']?></div>
		</div>
	</div>
	<?php
}
else
{
	#純顯示
	?>
	<div class="BtnActBox">
		<div class="BtnAct JqSubmit"><?php echo SUBMIT?></div>
	</div>
	<?php
}
?>