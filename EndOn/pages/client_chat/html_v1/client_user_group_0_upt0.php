<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<form action="<?php echo $aUrl['sAct'];?>" method="post" data-form="0">
	<input type="hidden" name="sJWT" value="<?php echo $sJWT;?>">
	<input type="hidden" name="nId" value="<?php echo $nId;?>">

	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo  aGROUP['NAME0'];?></span>
		<div class="Ipt">
			<input type="text" name="sName0" value="<?php echo $aData['sName0'];?>">
		</div>
	</div>
	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo  aGROUP['ACCOUNT'];?></span>
		<div class="Ipt">
			<input type="text" name="sAccount" placeholder="<?php echo ACCOUNT;?>" value="<?php echo $aData['aMember'][$aData['nUid']]['sAccount'];?>">
		</div>
	</div>

	<div class="Block MarginBottom20">
		<span class="InlineBlockTit"><?php echo aGROUP['GROUPMEMBER'];?> (<?php echo sizeof($aData['aMember']);?>)</span>
	</div>
	<?php
	if (!empty($aData['aMember']))
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
								<th><?php echo STATUS;?></th>
								<th><?php echo OPERATE;?></th>
							</tr>
						</thead>

						<!-- 若單純顯示紀錄,不會有點擊查看向下顯示一列資料,或是另開彈窗顯示資料 -->
						<tbody>
							<?php
							foreach($aData['aMember'] as $LPnUid => $LPaMember)
							{
							?>
								<tr>
									<td><?php echo $LPaMember['nId'];?></td>
									<td><?php echo $LPaMember['sAccount'];?></td>
									<td><?php echo $aStatus[$LPaMember['nStatus']];?></td>
									<td>
										<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="1" data-replace="<?php echo $LPaMember['sDel'];?>">
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