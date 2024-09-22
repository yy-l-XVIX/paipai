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
			<select name="nOnline">
				<?php
				foreach ($aOnline as $LPnOnline => $LPaOnline)
				{
					?>
					<option value="<?php echo $LPnOnline;?>" <?php echo $LPaOnline['sSelect'];?> >
						<?php echo $LPaOnline['sText'];?>
					</option>
					<?php
				}
				?>
			</select>
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aBANK['BANKNAME'];?></span>
		<div class="Sel">
			<select name="nBid">
				<?php
				foreach ($aBank as $LPnBank => $LPaBank)
				{
					?>
					<option value="<?php echo $LPnBank;?>" <?php echo $LPaBank['sSelect'];?> >
						<?php echo $LPaBank['sTitle'];?>
					</option>
					<?php
				}
				?>
			</select>
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aBANK['ACCOUNT'];?></span>
		<?php
			if($nId == 0)
			{
		?>
		<div class="Ipt">
			<input type="text" name="sAccount" value="<?php echo $aData['sAccount'];?>" placeholder="<?php echo aBANK['ACCOUNT'];?>">
		</div>
		<?php
			}
			else
			{
				echo $aData['sAccount'];
			}
		?>

	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aBANK['NAME2'];?></span>
		<div class="Ipt">
			<input type="text" name="sName2" value="<?php echo $aData['sName2'];?>" placeholder="<?php echo aBANK['NAME2'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aBANK['NAME1'];?></span>
		<div class="Ipt">
			<input type="text" name="sName1" value="<?php echo $aData['sName1'];?>" placeholder="<?php echo aBANK['NAME1'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aBANK['NAME0'];?></span>
		<div class="Ipt">
			<input type="text" name="sName0" value="<?php echo $aData['sName0'];?>" placeholder="<?php echo aBANK['NAME0'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20" >
		<span class="InlineBlockTit"><?php echo '圖片';?></span>
		<?php
		if(!isset($aData['sImgUrl']))
		{
				# 尚未有圖
			?>
			<input type="file" name="sFile">
			<span class="FontRed"><?php echo aIMGERROR['LIMIT'];?></span>
		<?php
		}
		else
		{
				# 有圖
			?>
			<?php
			/* 功能正常 先隱藏
			<a class="BtnAny2" href="<?php echo $aData['sDelImgUrl'];?>">
				<i class="fas fa-times"></i>
			</a>
			*/
			?>
			<div class="BlockImg MarginTop5">
				<img src="<?php echo $aData['sImgUrl']?>" alt="">
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