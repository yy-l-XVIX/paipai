<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="POST" data-form="0" enctype="multipart/form-data">
	<input type="hidden" name="sJWT" value="<?php echo $sJWTAct;?>" />
	<input type="hidden" name="nt" value="<?php echo NOWTIME;?>" />
	<input type="hidden" name="nId" value="<?php echo $aData['nId'];?>" />

	<!-- Select -->
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo STATUS;?></span>
		<div class="Sel">
			<select name="nType3">
				<?php
				foreach ($aType3 as $LPnType3 => $LPaType3)
				{
					?>
					<option value="<?php echo $LPnType3;?>" <?php echo $LPaType3['sSelect'];?> >
						<?php echo $LPaType3['sText'];?>
					</option>
					<?php
				}
				?>
			</select>
		</div>
	</div>

	<div class="Block MarginBottom20">
		<div class="InlineBlockTit"><?php echo ACCOUNT;?></div>
		<div class="InlineBlockTxt"><?php echo $aData['sAccount'];?></div>
	</div>
	<div class="Block MarginBottom20">
		<div class="InlineBlockTit"><?php echo aUSERID['REALNAME'];?></div>
		<div class="InlineBlockTxt"><?php echo $aData['sName1'];?></div>
	</div>
	<div class="Block MarginBottom20" >
		<span class="InlineBlockTit"><?php echo aUSERID['IDFRONT'];?></span>
		<?php
		if(!isset($aData['sImageUrl0']))
		{
				# 尚未有圖
			?>
			<input type="file" name="sFile0">
			<span class="FontRed"><?php echo aIMGERROR['LIMIT'];?></span>
		<?php
		}
		else
		{
				# 有圖
			/*
			<a class="BtnAny2" href="<?php echo $aData['sDelImageUrl0'];?>">
				<i class="fas fa-times"></i>
			</a>
			*/
			?>
			<div class="BlockImg MarginTop5">
				<img src="<?php echo $aData['sImageUrl0']?>" alt="">
			</div>
			<?php
		}
		?>
	</div>
	<div class="Block MarginBottom20" >
		<span class="InlineBlockTit"><?php echo aUSERID['IDBACK'];?></span>
		<?php
		if(!isset($aData['sImageUrl1']))
		{
				# 尚未有圖
			?>
			<input type="file" name="sFile1">
			<span class="FontRed"><?php echo aIMGERROR['LIMIT'];?></span>
		<?php
		}
		else
		{
				# 有圖
			/*
			<a class="BtnAny2" href="<?php echo $aData['sDelImageUrl1'];?>">
				<i class="fas fa-times"></i>
			</a>
			*/
			?>
			<div class="BlockImg MarginTop5">
				<img src="<?php echo $aData['sImageUrl1']?>" alt="">
			</div>
			<?php
		}
		?>
	</div>
	<!-- 操作選項 -->
	<div class="EditBtnBox">
		<div class="EditBtn JqStupidOut" data-showctrl="0">
			<i class="far fa-save"></i>
			<span><?php echo CSUBMIT;?></span>
		</div>
		<a href="<?php echo $aUrl['sBack'];?>" class="EditBtn red">
			<i class="fas fa-times"></i>
			<span><?php echo CBACK;?></span>
		</a>
	</div>
</form>