<?php $aData = json_decode($sData,true);?>
<form action="<?php echo $aUrl['sPage']; ?>" method="POST" class="Form MarginBottom20">
	<div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo ACCOUNT;?></span>
			<div class="Ipt">
				<input type="text" name="sAccount" value="<?php echo $sAccount;?>" placeholder="<?php echo ACCOUNT;?>">
			</div>
		</div>
		<div class="Block MarginBottom20" >
			<span class="InlineBlockTit"><?php echo STATUS;?></span>
			<div class="Sel">
				<select name="nType3">
					<?php
					foreach ($aType3 as $LPnType3 => $LPaType3)
					{
						?>
						<option value="<?php echo $LPnType3;?>" <?php echo $LPaType3['sSelect'];?>><?php echo $LPaType3['sText'];?></option>
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
						<th><?php echo NO;?></th>
						<th><?php echo ACCOUNT;?></th>
						<th><?php echo aUSERID['REALNAME'];?></th>
						<th><?php echo STATUS;?></th>
						<th><?php echo aUSERID['IDFRONT'];?></th>
						<th><?php echo aUSERID['IDBACK'];?></th>
						<th><?php echo CREATETIME;?></th>
						<th><?php echo OPERATE;?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($aData as $LPnId => $LPaData)
					{
						?>
						<tr>
							<td><?php echo $LPnId;?></td>
							<td><?php echo $LPaData['sAccount'];?></td>
							<td><?php echo $LPaData['sName1'];?></td>
							<td><?php echo $aType3[$LPaData['nType3']]['sText'];?></td>
							<td><img src="<?php echo $LPaData['sImageUrl0'];?>"></td>
							<td><img src="<?php echo $LPaData['sImageUrl1'];?>"></td>
							<td><?php echo $LPaData['sCreateTime'];?></td>
							<td>
								<a href="<?php echo $LPaData['sIns'];?>" class="TableBtnBg">
									<i class="fas fa-pen"></i>
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