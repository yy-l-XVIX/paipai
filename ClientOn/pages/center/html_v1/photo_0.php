<?php $aData = json_decode($sData,true);?>
<!-- 個人資訊-照片 -->
<form id="JqPostForm" action="<?php echo $aUrl['sAct'].'&sJWT='.$sJWT;?>" enctype="multipart/form-data">
	<input type="hidden" class="JqChangeRole" data-role="<?php echo $aChangeRole['sText'];?>" data-url="<?php echo $aChangeRole['sUrl'];?>">
	<div class="infProfileBox">
		<div class="infProfileTop JqAppendDiv" data-box="0">
			<div class="infProfileImg selfieBox JqAppend <?php echo $aData['sSelfieBoxClass'];?>" >
				<img src="<?php echo $aData['sHeadImage'];?>" data-file="0" class="JqPreviewImage JqHead">
				<!-- 個人資料為人才時才呈現,編輯個人資料時不呈現 -->
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
						<span><?php echo aPHOTO['CHANGHEAD'];?></span>
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
				?>
				<!-- 人才才顯示 -->
				<div class="infProfileMenuBox">
					<?php
					if($bEdit)
					{
						#編輯
						?>
						<a href="<?php echo $aUrl['sSetting'];?>" class="infProfileMenu"><?php echo aPHOTO['SETTING'];?></a>
						<?php
					}
					?>
					<!-- 當前頁面 infProfileMenu + active -->
					<a href="<?php echo $aUrl['sInf'].'&sRole='.$aData['sRole'];?>" class="infProfileMenu"><?php echo aPHOTO['INFO'];?></a>
					<div class="infProfileMenu active"><?php echo $aDataPending['sPhoto'];?> <?php echo aPHOTO['PHOTO'];?></div>
					<a href="<?php echo $aUrl['sVideo'].'&sRole='.$aData['sRole'];?>" class="infProfileMenu"><?php echo $aDataPending['sVideo'];?> <?php echo aPHOTO['VIDEO'];?></a>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
	if ($bEdit && sizeof($aData['aImage']) < $aSystem['aParam']['nPhotoLimit'])
	{
		?>
		<div class="FontRed TextAlignCenter PaddingTopBottom5 WordBreakBreakAll">
			<?php echo str_replace('[[::nPhotoLimit::]]',$aSystem['aParam']['nPhotoLimit'],aPHOTO['PHOTOINFO']);?>
		</div>
		<?php
	}
	?>
	<?php
	if ($aData['nType4'] == 0 && !$bEdit)
	{
		?>
		<table class="infData">
			<tbody>
				<tr>
					<td colspan="2" class="infDataTxt TextAlignCenter"><?php echo aPHOTO['PRIVATEACCOUNT'];?></td>
				</tr>
			</tbody>
		</table>
		<?php
	}
	else
	{
		?>
		<div class="infPhotoBox JqAppendDiv" data-box="1">
			<table class="infPhotoTable">
				<tbody>
					<input type="hidden" class="JqImgLeft" value="<?php echo $aSystem['aParam']['nPhotoLimit']-sizeof($aData['aImage']);?>">
					<?php
					$nTdAmount = 3;
					$nPhotoCount = 1;
					for ($i=0; $i <= $aSystem['aParam']['nPhotoLimit']; $i++)
					{
						$LPaImage = array();
						if($nPhotoCount%$nTdAmount == 1)
						{
							echo '<tr>';
						}
						?>

						<?php
						if ($nPhotoCount == 1 && $bEdit && sizeof($aData['aImage']) < $aSystem['aParam']['nPhotoLimit'])
						{
							?>
							<td class="infPhotoTd">
								<!-- <img data-file="1" class="infPhotoBlock JqPreviewImage" src=""> -->
								<div class="infPhotoBlock infPhotoBtnAdd">
									<input type="file" class="JqFile" name="sFilePhoto[]" data-filebtn="1" accept="image/*" multiple/>
									<div><?php echo aPHOTO['ADD'];?></div>
								</div>
							</td>
							<?php
							$nPhotoCount++;
							continue;
						}
						else
						{
							$LPaImage = array_pop($aData['aImage']);
						}

						if (!empty($LPaImage))
						{
							?>
							<td class="infPhotoTd">
								<div class="infPhotoBlock JqPhotoBtnZoom active">
									<img src="<?php echo $LPaImage['sImgUrl'];?>" alt="" data-act="<?php echo $aUrl['sAct'].'&sJWT='.$sDelImgJWT.'&nId='.$LPaImage['nId'];?>">
								</div>
							</td>
							<?php
						}

						else
						{
							?>
							<td class="infPhotoTd JqAppend"></td>
							<?php
						}

						?>

						<?php
						if($nPhotoCount%$nTdAmount == 0)
						{
							echo '</tr>';
						}
						$nPhotoCount ++;
					}
					if(($nPhotoCount-1)%$nTdAmount != 0)
					{
						for($nAdd=1;$nAdd<=($nTdAmount-(($nPhotoCount-1)%$nTdAmount));$nAdd++)
						{
							echo '<td class="infPhotoTd JqAppend"></td>';
						}
						echo '</tr>';
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
	}
	?>
</form>

<!-- 放大圖片 -->
<div class="WindowBox JqWindowBox JqPhotoZoomBox">
	<header>
		<div class="headerContainer">
			<div class="headerIcon headerLeft JqClose">
				<i class="fas fa-arrow-left"></i>
			</div>
			<?php
			if ($bEdit)
			{
				?>
				<div class="headerBtn headerRight0 JqDelImg" data-act=""><?php echo aPHOTO['DEL'];?></div>
				<?php
			}
			?>
		</div>
	</header>
	<div class="infPhotoWindowBox">
		<img src="" alt="" class="JqZoomImg">
	</div>
	<div class="WindowBg"></div>
</div>

<?php
if ($bEdit)
{
	#純顯示
	?>
	<div class="BtnActBox JqFileBtnActBox">
		<div class="BtnAct JqSubmit"><?php echo SUBMIT?></div>
	</div>
	<?php
}
?>