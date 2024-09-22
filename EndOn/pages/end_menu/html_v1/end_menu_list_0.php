<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage'];?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="Ipt">
			<input type="text" name="sListName0" placeholder="<?php echo aLIST['SEARCHNAME'];?>" value="<?php echo $sListName0;?>">
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
		<div class="Sel">
			<select name="nMid">
				<?php
				foreach ($aMenuKind as $LPnMid => $LPaMenuKind)
				{
					?>
					<option value="<?php echo $LPnMid;?>" <?php echo $LPaMenuKind['sSelect'];?> >
						<?php echo $LPaMenuKind['sMenuName0'];?>
					</option>
					<?php
				}
				?>
			</select>
		</div>
		<input type="submit" class="BtnAny" value="<?php echo aLIST['SEARCH'];?>">
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
						<th><?php echo aLIST['LISTNAME0'];?></th>
						<th><?php echo aLIST['LISTTABLE0'];?></th>
						<th><?php echo aLIST['STATUS'];?></th>
						<th><?php echo aLIST['TYPE0'];?></th>
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
							<td><?php echo $LPaData['sListName0'];?></td>
							<td><?php echo $LPaData['sListTable0'];?></td>
							<td class="<?php echo $aOnline[$LPaData['nOnline']]['sClass'];?>"><?php echo $aOnline[$LPaData['nOnline']]['sTitle'];?></td>
							<td><?php echo $aType0[$LPaData['nType0']]['sTitle'];?></td>
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
								*/
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