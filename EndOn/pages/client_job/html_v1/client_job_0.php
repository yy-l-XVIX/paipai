<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="Form MarginBottom20">
	<div class="Search">
		<div class="MarginBottom10">
			<?php
			foreach ($aDay as $LPsText => $LPaDate)
			{
				?>
				<span class="JqDate BtnKind <?php echo $LPaDate['sSelect'];?>" data-day="<?php echo $LPsText;?>" data-date0="<?php echo $LPaDate['sStartDay']?>" data-date1="<?php echo $LPaDate['sEndDay']?>">
					<?php echo aDAYTEXT[$LPsText];?>
				</span>
				<?php
			}
			?>
			<input type="hidden" name="sSelDay" value="<?php echo $sSelDay;?>">
		</div>
		<div class="Block MarginBottom20" >
			<div class="Ipt">
				<input type="text" name="sStartTime" class="JqStartTime" value="<?php echo $sStartTime;?>">
			</div>
			<span>~</span>
			<div class="Ipt">
				<input type="text" name="sEndTime" class="JqEndTime" value="<?php echo $sEndTime;?>">
			</div>
		</div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aJOB['NAME0'];?></span>
			<div class="Ipt">
				<input type="text" name="sName0" value="<?php echo $sName0;?>" placeholder="<?php echo aJOB['NAME0'];?>">
			</div>
		</div>
		<div class="Block MarginBottom20">
			<span class="InlineBlockTit"><?php echo aJOB['AREA'];?></span>
			<div class="Sel">
				<select name="nAid" class="JqChangeArea">
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
			<span class="InlineBlockTit"><?php echo aJOB['STATUS'];?></span>
			<div class="Sel">
				<select name="nStatus">
					<?php
					foreach ($aStatus as $LPnStatus => $LPaStatus)
					{
						?>
						<option value="<?php echo $LPnStatus?>" <?php echo $LPaStatus['sSelect'];?>><?php echo $LPaStatus['sName0'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<input type="submit" class="BtnAny" value="<?php echo SEARCH;?>">
	</div>
</form>
<?php
/*
<!-- 新增按鈕 -->
<div class="Block MarginBottom10">
	<a href="<?php echo $aUrl['sIns'];?>" class="BtnAdd"><?php echo INS;?></a>
</div>
*/
?>
<!-- 純顯示資訊 -->
<div class="Information">
	<table class="InformationTit">
		<tbody>
			<tr>
				<td class="InformationTitCell" style="width:calc(100%/1);">
					<div class="InformationName"><?php echo $sHeadTitle; ?></div>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="InformationScroll">
		<div class="InformationTableBox">
			<table>
				<thead>
					<tr>
						<th><?php echo NO;?></th>
						<th><?php echo aJOB['NAME0'];?></th>
						<th><?php echo ACCOUNT;?></th>
						<th><?php echo aJOB['AREA'];?></th>
						<th><?php echo STATUS;?></th>
						<th><?php echo CREATETIME;?></th>
						<th><?php echo UPDATETIME;?></th>
						<th><?php echo OPERATE;?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach($aData as $LPnLid => $LPaData)
				{
					?>
					<tr>
						<td><?php echo $LPnLid;?></td>
						<td><?php echo $LPaData['sName0'];?></td>
						<td><?php echo $aMemberData[$LPaData['nUid']]['sName0'];?></td>
						<td><?php echo $LPaData['sArea'];?></td>
						<td class="<?php echo $aStatus[$LPaData['nStatus']]['sClass'];?>"><?php echo $aStatus[$LPaData['nStatus']]['sName0'];?></td>
						<td><?php echo $LPaData['sCreateTime'];?></td>
						<td><?php echo $LPaData['sUpdateTime'];?></td>
						<td>
							<a href="<?php echo $LPaData['sIns'];?>" class="TableBtnBg">
								<i class="fas fa-pen"></i>
							</a>
							<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaData['sDel'];?>">
								<i class="fas fa-times"></i>
							</div>
							<a href="<?php echo $LPaData['sChat'];?>" class="TableBtnBg">
								<?php echo aJOB['CHATHISTORY'];?>
							</a>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php echo $aPageList['sHtml'];?>