<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="post" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWT;?>">
	<input type="hidden" name="nId" value="<?php echo $nId;?>">

	<div class="Information">
		<table class="InformationTit">
			<tbody>
				<tr>
					<td class="InformationTitCell" style="width:calc(100%/1);">
						<div class="InformationName"><?php echo $aGroup['sName0'];?></div>
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
							<th><?php echo CONTENT;?></th>
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
								<td><?php echo $aMemberData[$LPaData['nUid']]['sAccount'];?></td>
								<td><?php echo $LPaData['sMsg'];?></td>
								<td><?php echo $LPaData['sCreateTime'];?></td>
								<td>
									<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaData['sDelUrl'];?>">
										<i class="fas fa-times"></i>
									</div>
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
	<!-- 操作選項 -->
	<div class="EditBtnBox">
		<a href="<?php echo $aUrl['sBack'];?>" class="EditBtn red">
			<i class="fas fa-times"></i>
			<span><?php echo CBACK;?></span>
		</a>
	</div>
</form>