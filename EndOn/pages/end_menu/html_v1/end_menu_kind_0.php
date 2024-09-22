<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="Ipt">
			<input type="text" name="sMenuName0" placeholder="<?php echo aMENU['SEARCHNAME'];?>" value="<?php echo $sMenuName0;?>">
		</div>
		<div class="Sel">
			<select name="nOnline">
				<?php
				foreach ($aOnline as $LPnOnline => $LPaOnline)
				{
					?>
					<option value="<?php echo $LPnOnline;?>" <?php echo $LPaOnline['sSelect'];?> >
						<?php echo $LPaOnline['sTitle'];?>
					</option>
					<?php
				}
				?>
			</select>
		</div>
		<input type="submit" class="BtnAny" value="<?php echo aMENU['SEARCH'];?>">
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
						<th>No.</th>
						<th><?php echo aMENU['MENUNAME0'];?></th>
						<th><?php echo aMENU['MENUTABLE0'];?></th>
						<th><?php echo aMENU['STATUS'];?></th>
						<th><?php echo SORT;?></th>
						<th><?php echo OPERATE;?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($aData as $LPnKid => $LPaData)
					{
						?>
						<tr>
							<td><?php echo $LPnKid;?></td>
							<td><?php echo $LPaData['sMenuName0'];?></td>
							<td><?php echo $LPaData['sMenuTable0'];?></td>
							<td class="<?php echo $aOnline[$LPaData['nOnline']]['sClass'];?>"><?php echo $aOnline[$LPaData['nOnline']]['sTitle'];?></td>
							<td><?php echo $LPaData['nSort'];?></td>
							<td>
								<a href="<?php echo $LPaData['sUptUrl'];?>" class="TableBtnBg">
									<i class="fas fa-pen"></i>
								</a>
							<?php
							/*
								<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaData['sDelUrl'];?>">
									<i class="fas fa-times"></i>
								</div>

							</td>
							*/
							?>
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
