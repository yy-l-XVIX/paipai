<?php $aData = json_decode($sData,true);?>
<!-- 身分證照片 -->
<form method="post" action="<?php echo $aUrl['sAct'];?>" class="JqForm" id="JqIdForm" data-info="<?php echo aID['INFO'];?>">
	<input type="hidden" name="nFileCount" value="<?php echo $nFileCount;?>">
	<div class="idBox">
		<?php
		foreach ($aData as $LPnType0 => $LPaImage)
		{
			?>
			<div class="idBlock">
				<div class="idTit">
					<span><?php echo $LPaImage['sText'];?></span>
					<span class="FontRed">*</span>
				</div>
				<?php
				if ($aUser['nStatus'] != 11 && $LPaImage['sUrl'] != '' )
				{
					// 圖片不可以更動
					?>
					<div class="FileImg">
						<img src="<?php echo $LPaImage['sUrl'];?>" alt="">
					</div>
					<?php
				}
				else
				{
					// 可以上傳圖片
					?>
					<div class="FileImg">
						<img class="JqPreviewImage" data-file="<?php echo $LPnType0;?>" src="<?php echo $LPaImage['sUrl'];?>">
					</div>
					<div class="FileBtnAdd JqFileActive <?php echo $LPaImage['sActive'];?>">
						<input type="file" name="sFile<?php echo $LPnType0;?>" class="JqFile" data-filebtn="<?php echo $LPnType0;?>" <?php echo $LPaImage['sRequire'];?>  accept="image/*">
						<div class="original"><?php echo UPLOADIMG;?></div>
						<div class="change"><?php echo CHANGEIMG;?></div>
					</div>
					<?php
				}
				?>

			</div>
			<?php
		}
		?>

		<?php
		if($aUser['nStatus'] == 11 || $aUser['nType3'] != 1)
		{
			?>
			<div class="BtnActBox">
				<div class="BtnAct JqSubmit"><?php echo SUBMIT;?></div>
			</div>
			<?php
		}
		?>
	</div>
</form>