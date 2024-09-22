<?php $aData = json_decode($sData,true);?>
<form id="JqPostForm" action="<?php echo $aUrl['sAct'].'&sJWT='.$sJWT;?>" data-info="<?php echo aSETTING['FORMINFO'];?>" enctype="multipart/form-data">
	<!-- 編輯個人檔案-用戶資料 -->
	<div class="infProfileBox">
		<div class="infProfileTop">
			<!-- 若此人身份為雇主,selfieBox + boss -->
			<div class="infProfileImg selfieBox <?php echo $sSelfieBoxClass;?>">
				<img src="<?php echo $aUser['sHeadImage'];?>" data-file="0" class="JqPreviewImage">
			</div>
			<div class="infProfileBtnBox">
				<div class="infProfileBtn JqFileActive">
					<input type="file" class="JqFile" name="sFile" data-filebtn="0" >
					<span><?php echo aSETTING['CHANGHEAD'];?></span>
				</div>
			</div>
			<?php
				if($sUserCurrentRole == 'staff')
				{
			?>
					<div class="infProfileMenuBox">

						<!-- 當前頁面 infProfileMenu + active -->
						<div class="infProfileMenu active"><?php echo aSETTING['SETTING'];?></div>

						<a href="<?php echo $aUrl['sInf'];?>" class="infProfileMenu"><?php echo aSETTING['INFO'];?></a>
						<a href="<?php echo $aUrl['sPhoto'];?>" class="infProfileMenu"><?php echo $aDataPending['sPhoto'];?> <?php echo aSETTING['PHOTO'];?></a>
						<a href="<?php echo $aUrl['sVideo'];?>" class="infProfileMenu"><?php echo $aDataPending['sVideo'];?> <?php echo aSETTING['VIDEO'];?></a>
					</div>
			<?php
				}
			?>
		</div>
	</div>
	<!-- 有些資訊互為好友才能看? -->
	<?php
	if ($sPendingMessage != '') //未開通提醒訊息
	{
	?>
		<div class="FontRed TextAlignCenter PaddingTopBottom5 WordBreakBreakAll"><?php echo $sPendingMessage;?></div>
	<?php
	}
	?>
	<table class="infData">
		<tbody>
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<span><?php echo ACCOUNT;?></span>
					</div>
				</td>
				<td class="infDataCell2">
					<!-- 不可更改 -->
					<div class="infDataTxt"><?php echo $aUser['sAccount'];?></div>
				</td>
			</tr>
			<?php
			/*
			// 2021-04-26 YL
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<?php echo $aDataPending['sName1'];?>
						<span><?php echo aSETTING['NAME1'];?></span>
					</div>
				</td>
				<td class="infDataCell2">
					<?php
					// if ($aUser['nStatus'] < 11)
					if (true)
					{
						?>
						<!-- 不可更改 -->
						<div class="infDataTxt"><?php echo $aUser['sName1'];?></div>
						<?php
					}
					else
					{
						?>
						<!-- 可更改 -->
						<div class="Ipt">
							<input type="text" placeholder="<?php echo aSETTING['NAME1INFO'];?>" name="sName1" value="<?php echo $aUser['sName1'];?>" required data-old="<?php echo $aUser['sName1'];?>">
						</div>
						<?php
					}
					?>
				</td>
			</tr>
			*/
			?>
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<span><?php echo aSETTING['NAME0'];?></span>
						<!-- 若為必填才顯示 -->
						<span class="FontRed">*</span>
					</div>
				</td>
				<td class="infDataCell2">
					<?php
					if (false)
					{
					?>
						<!-- 不可更改 -->
						<div class="infDataTxt"><?php echo $aUser['sName0'];?></div>
					<?php
					}
					else
					{
					?>
						<!-- 可更改 -->
						<div class="Ipt">
							<input type="text" placeholder="<?php echo aSETTING['NAME0INFO'];?>" name="sName0" value="<?php echo $aUser['sName0'];?>" required data-old="<?php echo $aUser['sName0'];?>">
						</div>
					<?php
					}
					?>
				</td>
			</tr>
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<?php echo $aDataPending['sBirthday'];?>
						<span><?php echo aSETTING['BIRTHDAY'];?></span>
						<!-- 若為必填才顯示 -->
						<span class="FontRed">*</span>
					</div>
				</td>
				<td class="infDataCell2">
					<?php
					if ($aUser['sBirthday'] != '')
					{
						?>
						<!-- 不可更改 -->
						<div class="infDataTxt"><?php echo $aUser['sBirthday'];?></div>
						<?php
					}
					else
					{
						?>
						<!-- 可更改 -->
						<div class="Ipt">
							<input type="text" class="JqBirthday" placeholder="<?php echo aSETTING['BIRTHDAY'];?>" name="sBirthday" value="<?php echo $aUser['sBirthday'];?>" autocomplete="off" required data-old="<?php echo $aUser['sBirthday'];?>">
						</div>
						<?php
					}
					?>
				</td>
			</tr>
			<?php
			/*
			<tr>
				<td class="infDataCell1">
					<div class="infDataTit">
						<?php echo $aDataPending['sIdNumber'];?>
						<span><?php echo aSETTING['IDNUMBER'];?></span>
						<!-- 若為必填才顯示 -->
						<span class="FontRed">*</span>
					</div>
				</td>
				<td class="infDataCell2">
					<?php
					if ($aUser['sIdNumber'] != '')
					{
						?>
						<!-- 不可更改 -->
						<div class="infDataTxt"><?php echo $aUser['sIdNumber'];?></div>
						<?php
					}
					else
					{
						?>
						<!-- 可更改 -->
						<div class="Ipt">
							<input type="text" placeholder="<?php echo aSETTING['IDNUMBER'];?>" name="sIdNumber" value="<?php echo $aUser['sIdNumber'];?>" required data-old="<?php echo $aUser['sIdNumber'];?>">
						</div>
						<?php
					}
					?>
				</td>
			</tr>
			*/
			?>
			<tr>
				<td class="infDataCell1 <?php echo $bLocationDisabled ?'VerticalAlignTop':''; ?>">
					<div class="infDataTit">
						<span><?php echo aSETTING['LOCATION'];?></span>
						<!-- 若為必填才顯示 -->
						<span class="FontRed">*</span>
					</div>
				</td>
				<?php
				if($bLocationDisabled)
				{
					?>
					<td class="infDataCell2 VerticalAlignTop">
						<!-- 不可更改 -->
						<div class="infDataTxt">
							<div><?php echo $aLocation[$aUser['nLid']]['sName0'];?></div>
							<div class="infDataSubMemo"><?php echo aSETTING['LASTUPDATE'];?> <?php echo $aUser['sLocationTime'];?></div>
						</div>
					</td>
					<?php
				}
				else
				{
					?>
					<td class="infDataCell2">
						<!-- 可更改 -->
						<div class="Sel">
							<select name="nLid" <?php echo $bLocationDisabled;?> data-old="<?php echo $aUser['nLid'];?>">
								<?php
								foreach ($aLocation as $LPnLid => $LPaLocation)
								{
									?>
									<option value="<?php echo $LPnLid;?>" <?php echo $LPaLocation['sSelect'];?>><?php echo $LPaLocation['sName0'];?></option>
									<?php
								}
								?>
							</select>
							<div class="SelDecro"></div>
						</div>
					</td>
					<?php
				}
				?>
			</tr>
		</tbody>
	</table>
</form>
<table class="infData">
	<tbody>
		<tr>
			<td class="infDataCell1">
				<a class="infDataTit" href="<?php echo $aUrl['sChangePwd'];?>"><?php echo aSETTING['CHANGEPASSWORD'];?></a>
			</td>
			<td class="infDataCell2">
				<a class="infDataArrow" href="<?php echo $aUrl['sChangePwd'];?>">
					<i class="fas fa-chevron-right"></i>
				</a>
			</td>
		</tr>
		<?php
		/* 2021-03-18
		<tr>
			<td class="infDataCell1">
				<a class="infDataTit" href="<?php echo $aUrl['sId'];?>">
					<?php echo $aDataPending['sIdImage'];?>
					<?php echo aSETTING['ID'];?>
				</a>
			</td>
			<td class="infDataCell2">
				<a class="infDataArrow" href="<?php echo $aUrl['sId'];?>">
					<i class="fas fa-chevron-right"></i>
				</a>
			</td>
		</tr>
		<tr>
			<td class="infDataCell1">
				<a class="infDataTit" href="<?php echo $aUrl['sBankList'];?>">
					<?php echo $aDataPending['sBankCard'];?>
					<?php echo aSETTING['BANKACCOUNT'];?>
				</a>
			</td>
			<td class="infDataCell2">
				<a class="infDataArrow" href="<?php echo $aUrl['sBankList'];?>">
					<i class="fas fa-chevron-right"></i>
				</a>
			</td>
		</tr>
		<tr>
			<td class="infDataCell1">
				<a class="infDataTit" href="<?php echo $aUrl['sChangeTransPwd'];?>"><?php echo aSETTING['CHANGETRANSPASSWORD'];?></a>
			</td>
			<td class="infDataCell2">
				<a class="infDataArrow" href="<?php echo $aUrl['sChangeTransPwd'];?>">
					<i class="fas fa-chevron-right"></i>
				</a>
			</td>
		</tr>
		*/
		?>
	</tbody>
</table>

<div class="BtnActBox">
	<div class="BtnAct JqSubmit"><?php echo SUBMIT?></div>
</div>