<?php $aData = json_decode($sData,true);?>
<!-- 個人資訊-影片 -->
<form id="JqPostForm" action="<?php echo $aUrl['sAct'].'&sJWT='.$sJWT;?>" enctype="multipart/form-data">
	<input type="hidden" class="JqChangeRole" data-role="<?php echo $aChangeRole['sText'];?>" data-url="<?php echo $aChangeRole['sUrl'];?>">
	<div class="infProfileBox">
		<div class="infProfileTop">
			<div class="infProfileImg selfieBox <?php echo $aData['sSelfieBoxClass'];?>">
				<img src="<?php echo $aData['sHeadImage'];?>" data-file="0" class="JqPreviewImage">
				<?php
				if($aData['sRole'] == 'staff')
				{
					#若此人身份為人才
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
						<span><?php echo aVIDEO['CHANGHEAD'];?></span>
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
						#編輯時才顯示
						?>
						<a href="<?php echo $aUrl['sSetting'];?>" class="infProfileMenu"><?php echo aVIDEO['SETTING'];?></a>
						<?php
					}
					?>
					<!-- 當前頁面 infProfileMenu + active -->
					<a href="<?php echo $aUrl['sInf'].'&sRole='.$aData['sRole'];?>" class="infProfileMenu"><?php echo aVIDEO['INFO'];?></a>
					<a href="<?php echo $aUrl['sPhoto'].'&sRole='.$aData['sRole'];?>" class="infProfileMenu"><?php echo $aDataPending['sPhoto'];?> <?php echo aVIDEO['PHOTO'];?></a>
					<div class="infProfileMenu active"><?php echo $aDataPending['sVideo'];?> <?php echo aVIDEO['VIDEO'];?></div>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
	if ($bEdit && sizeof($aData['aVideo']) < $aSystem['aParam']['nVideoLimit'])
	{
		?>
		<div class="FontRed TextAlignCenter PaddingTopBottom5 WordBreakBreakAll">
			<?php echo str_replace('[[::nVideoLimit::]]',$aSystem['aParam']['nVideoLimit'],aVIDEO['VIDEOINFO']);?>
		</div>
		<?php
	}
	?>
	<div class="infVideoBox">
		<?php
		if ($aData['nType4'] == 0 && !$bEdit)
		{
			?>
			<table class="infData">
				<tbody>
					<tr>
						<td colspan="2" class="infDataTxt TextAlignCenter"><?php echo aVIDEO['PRIVATEACCOUNT'];?></td>
					</tr>
				</tbody>
			</table>
			<?php
		}
		else
		{
			foreach ($aData['aVideo'] as $LPnId => $LPsVideoUrl)
			{
				#已上傳影片
				// <div class="infVideoVideo JqVideoBtnZoom">
				?>
				<div class="infVideoVideo">
					<video class="JqVideo" preload="metadata" data-act="<?php echo $aUrl['sAct'].'&sJWT='.$sDelVideoJWT.'&nId='.$LPnId;?>" controls playsinline >
						<source src="<?php echo $LPsVideoUrl;?>#t=0.001" type="video/mp4" />
					</video>
				</div>
				<?php
				if ($bEdit)
				{
					?>
					<div class="BtnActBox">
						<div class="BtnAct2 JqDelVideo" data-act="<?php echo $aUrl['sAct'].'&sJWT='.$sDelVideoJWT.'&nId='.$LPnId;?>"><?php echo aVIDEO['DEL'];?></div>
					</div>
					<?php
				}
			}

			if (sizeof($aData['aVideo']) < $aSystem['aParam']['nVideoLimit'] && $bEdit)
			{
				#未上傳影片
				?>
				<video class="DisplayBlockNone JqPreviewImage" data-file="1" src="" controls playsinline></video>
				<div class="infVideoBtnAdd">
					<input type="file" class="JqFile" name="sFileVideo" data-filebtn="1" accept="video/*">
					<div><?php echo aVIDEO['ADD'];?></div>
				</div>
				<?php
			}
		}
		?>
	</div>
</form>
<?php
/*
<div class="WindowBox JqWindowBox JqVideoZoomBox">
	<header>
		<div class="headerContainer">
			<div class="headerIcon headerLeft JqClose">
				<i class="fas fa-arrow-left"></i>
			</div>
			<?php
			if ($bEdit && $aUser['nStatus'] == 11)
			{
				?>
				<div class="headerBtn headerRight0 JqDelVideo" data-act=""><?php echo aVIDEO['DEL'];?></div>
				<?php
			}
			?>
		</div>
	</header>
	<div class="infVideoWindowBox" style="position: relative;z-index: 13;">
		<video src="images/video.mp4" class="JqZoomVideo" controls playsinline ></video>
	</div>
	<div class="WindowBg"></div>
</div>
*/
?>
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