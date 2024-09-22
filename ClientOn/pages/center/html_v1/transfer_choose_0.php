<?php $aData = json_decode($sData,true);?>
<!-- 轉帳-選擇好友 -->
<input type="hidden" name="sTransfer" value="<?php echo $aUrl['sTransfer']; ?>">
<input type="hidden" name="nPageNo" value="<?php echo $aPage['nNowNo']+1?>">
<input type="hidden" name="sFetch" value="<?php echo $aUrl['sPage'].'&run_page=1&nFetch=1';?>">
<div class="transferChooseBox">
	<div class="transferChooseTit"><?php echo aCHOOSE['FRIEND'];?></div>
	<div class="transferChooseBlock JqAppend">
		<?php
		foreach ($aData as $LPnId => $LPaFriend)
		{
		?>
			<div class="transferChooseList JqListSelect">
				<table class="transferChooseTable">
					<tbody>
						<tr>
							<td class="transferChooseTdIcon">
								<div class="transferChooseIconChoose">
									<label for="friend<?php echo $LPnId; ?>">
										<input type="radio" id="friend<?php echo $LPnId; ?>" name="sSelectFriend" value="<?php echo $LPaFriend['sAccount'];?>" class="JqSelectFriend">
									</label>
								</div>
							</td>
							<td class="transferChooseTdPic JqChatGroupBtn">

								<!-- 若此人身份為雇主,selfieBox + boss -->
								<div class="selfieBox <?php echo $LPaFriend['sRoleClass'];?>">
									<img src="<?php echo $LPaFriend['sHeadImage'];?>" alt="">
								</div>
							</td>
							<td class="transferChooseTdName JqChatGroupBtn">
								<div><?php echo $LPaFriend['sAccount'];?></div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		<?php
		}
		?>
	</div>
	<?php
		#卷軸到底後,Loading時出現, class + active
		require_once('inc/#Loading.php');
	?>
</div>
<div class="DisplayNone JqCopy">
	<div class="transferChooseList JqListSelect">
		<table class="transferChooseTable">
			<tbody>
				<tr>
					<td class="transferChooseTdIcon">
						<div class="transferChooseIconChoose">
							<label for="friend[[::nId::]]">
								<input type="radio" id="friend[[::nId::]]" name="sSelectFriend" value="[[::sAccount::]]" class="JqSelectFriend">
							</label>
						</div>
					</td>
					<td class="transferChooseTdPic JqChatGroupBtn">

						<!-- 若此人身份為雇主,selfieBox + boss -->
						<div class="selfieBox [[::sRoleClass::]]">
							[[::sHeadImage::]]
						</div>
					</td>
					<td class="transferChooseTdName JqChatGroupBtn">
						<div>[[::sAccount::]]</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>