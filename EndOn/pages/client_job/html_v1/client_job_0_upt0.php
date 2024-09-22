<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="post" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWT;?>">
	<input type="hidden" name="nId" value="<?php echo $nId;?>">

	<!-- Select -->
	<div class="Block MarginBottom20">
		<div class="InlineBlockTit"><?php echo STATUS;?></div>
		<div class="Sel">
			<select name="nStatus">
				<?php
					foreach($aStatus as $LPnStatus => $LPaStatus)
					{
				?>
						<option value="<?php echo $LPnStatus;?>" <?php echo $LPaStatus['sSelect'];?>><?php echo $LPaStatus['sName0'];?></option>
				<?php
					}
				?>
			</select>
		</div>
	</div>
	<div class="Block MarginBottom20">
		<div class="InlineBlockTit"><?php echo aJOB['AREA'];?></div>
		<div class="Sel">
			<select name="nAid">
				<?php
				foreach ($aArea as $LPnAid => $LPaArea)
				{
					?>
					<option value="<?php echo $LPnAid?>" <?php echo $LPaArea['sSelect'];?>><?php echo $LPaArea['sName0'];?></option>
					<?php
				}
				?>
			</select>
		</div>
	</div>
	<div class="Block MarginBottom20">

		<!-- 若標題與內容要在同一行顯示,則標題使用InlineBlockTit,內容使用InlineBlockTxt -->
		<div class="InlineBlockTit"><?php echo ACCOUNT;?></div>
		<div class="InlineBlockTxt"><?php echo $aData['sAccount'];?></div>
	</div>


	<div class="Block MarginBottom20">
		<div class="InlineBlockTit"><?php echo aJOB['NAME0'];?></div>
		<div class="Ipt">
			<input type="text" name="sName0" value="<?php echo $aData['sName0'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<div class="InlineBlockTit"><?php echo aJOB['WORKTIME'];?></div>
		<div class="Ipt">
			<input type="text" class="JqStartTime" name="sStartTime" value="<?php echo $aData['sStartTime'];?>">
		</div>
		<span>~</span>
		<div class="Ipt">
			<input type="text" class="JqEndTime" name="sEndTime" value="<?php echo $aData['sEndTime'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<div class="InlineBlockTit"><?php echo aJOB['WORKMEN'];?></div>
		<div class="Ipt">
			<input type="text" name="nEmploye" value="<?php echo $aData['nEmploye'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<div class="InlineBlockTit"><?php echo aJOB['CONTENT0'];?></div>
		<div class="Textarea">
			<textarea name="sContent0" class="ckeditor MarginTop5"><?php echo $aData['sContent0'];?></textarea>

		</div>
	</div>
	<?php
	if ($aData['sImgUrl'] != '')
	{
		?>
		<div class="InlineBlockImg">
			<img src="<?php echo $aData['sImgUrl'];?>" alt="">
		</div>
		<?php
	}
	?>

	<div class="Block MarginBottom20">
		<div class="InlineBlockTit"><?php echo aJOB['GROUPMEN'];?> (<?php echo sizeof($aMemberData);?>)</div>
	</div>
	<?php
	if (!empty($aMemberData))
	{
		?>
		<div class="Information">
			<div class="InformationScroll">
				<div class="InformationTableBox" data-show="1">
					<table>
						<thead>
							<tr>
								<th><?php echo NO;?></th>
								<th><?php echo ACCOUNT;?></th>
								<th><?php echo STATUS?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach($aMemberData as $LPnUid => $LPaMember)
							{
							?>
								<tr>
									<td><?php echo $LPaMember['nId'];?></td>
									<td><?php echo $LPaMember['sAccount'];?></td>
									<td><?php echo $LPaMember['sJoin'];?></td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
	}
	?>

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