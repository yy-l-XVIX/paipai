<?php $aData = json_decode($sData,true);?>
<!-- 編輯頁面 -->
<?php
	if ($nId == 0)
	{
		?>
		<form action="<?php echo $aUrl['sAct'];?>" method="post" data-form="0">
			<input type="hidden" name="sJWT" value="<?php echo $sJWT;?>">
			<input type="hidden" name="nId" value="<?php echo $nId;?>">

			<div class="Block MarginBottom20">
				<span class="InlineBlockTit"><?php echo ACCOUNT;?></span>
				<div class="Ipt">
					<input type="text" name="sAccount" placeholder="<?php echo ACCOUNT;?>" value="<?php echo $aMemberData[$aData['nUid']];?>">
				</div>
			</div>

			<div class="Block MarginBottom20">
				<span class="InlineBlockTit"><?php echo aDISCUSS['CONTENT0'];?></span>
				<div class="Textarea">
					<textarea name="sContent0" placeholder="<?php echo aDISCUSS['CONTENT0'];?>..." spellcheck="false"><?php echo $aData['sContent0'];?></textarea>
				</div>
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
		<?php
	}
	else
	{
		?>
		<div class="Information">
			<div class="InformationScroll">
				<div class="InformationTableBox" data-show="1">
					<table>
						<thead>
							<tr>
								<th><?php echo ACCOUNT;?></th>
								<th><?php echo aDISCUSS['CONTENT0'];?></th>
								<th><?php echo CREATETIME;?></th>
								<th><?php echo OPERATE;?></th>
							</tr>
						</thead>

						<!-- 若單純顯示紀錄,不會有點擊查看向下顯示一列資料,或是另開彈窗顯示資料 -->
						<tbody>
							<tr>
								<td><?php echo $aMemberData[$aData['nUid']];?></td>
								<td>
									<?php echo $aData['sContent0'];?>
									<div>
										<?php
										foreach ($aData['aImgUrl'] as $LPsImgUrl)
										{
											?>
											<img src="<?php echo $LPsImgUrl;?>" alt="">
											<?php
										}
										?>
									</div>
								</td>
								<td><?php echo $aData['sCreateTime'];?></td>
								<td></td>
							</tr>
							<tr>
								<td colspan="4"><?php echo aDISCUSS['REPLY'];?></td>
							</tr>
							<?php
							foreach($aData['aReply'] as $LPnId => $LPaReply)
							{
							?>
								<tr>
									<td><?php echo $aMemberData[$LPaReply['nUid']];?></td>
									<td>
										<?php echo $LPaReply['sContent0'];?>
										<div>
										<?php
										foreach ($LPaReply['aImgUrl'] as $LPsImgUrl)
										{
											?>
											<img src="<?php echo $LPsImgUrl;?>" alt="">
											<?php
										}
										?>
										</div>

									</td>
									<td><?php echo $LPaReply['sCreateTime'];?></td>
									<td>
										<div class="TableBtnBg red JqStupidOut JqReplaceS" data-showctrl="0" data-replace="<?php echo $LPaReply['sDelUrl'];?>">
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
		<?php
	}
?>
