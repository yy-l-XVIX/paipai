<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="Form MarginBottom20">
	<div>
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
		<div class="MarginBottom10">
			<span class="InlineBlockTit"><?php echo ACCOUNT;?></span>
			<div class="Ipt">
				<input type="text" name="sAccount" placeholder="<?php echo ACCOUNT;?>" value="<?php echo $sAccount;?>">
			</div>
		</div>
		<div class="MarginBottom10">
			<span class="InlineBlockTit"><?php echo KIND;?></span>
			<div class="Sel">
				<select name="nKind">
					<?php
					foreach ($aKind as $LPnKind => $LPaKind)
					{
						?>
						<option value="<?php echo $LPnKind;?>" <?php echo $LPaKind['sSelect'];?> ><?php echo $LPaKind['sTitle'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="MarginBottom10">
			<span class="InlineBlockTit"><?php echo STATUS;?></span>
			<div class="Sel">
				<select name="nKind">
					<?php
					foreach ($aStatus as $LPnStatus => $LPaStatus)
					{
						?>
						<option value="<?php echo $LPnStatus;?>" <?php echo $LPaStatus['sSelect'];?> ><?php echo $LPaStatus['sTitle'];?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>

		<input type="submit" class="BtnAny" value="<?php echo SEARCH;?>">
	</div>
</form>

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
						<th><?php echo KIND;?></th>
						<th><?php echo ACCOUNT;?></th>
						<th><?php echo aSERVICE['QUESTION'];?></th>
						<th><?php echo aSERVICE['RESPONSE'];?></th>
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
							<td><?php echo $LPaData['sKind'];?></td>
							<td><?php echo $LPaData['sAccount'];?></td>
							<td><?php echo $LPaData['sQuestion'];?></td>
							<td><?php echo $LPaData['sResponse'];?></td>
							<td class="<?php echo $aStatus[$LPaData['nStatus']]['sClass'];?>"><?php echo $aStatus[$LPaData['nStatus']]['sTitle'];?></td>
							<td><?php echo $LPaData['sCreateTime'];?></td>
							<td><?php echo $LPaData['sUpdateTime'];?></td>
							<td>
								<?php
									if($LPaData['nStatus'] == 0)
									{
								?>
										<a href="<?php echo $LPaData['sIns'];?>" class="TableBtnBg">
											<i class="fas fa-pen"></i>
										</a>
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
		</div>
	</div>
</div>
<?php echo $aPageList['sHtml'];?>