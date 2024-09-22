<?php $aData = json_decode($sData,true);?>
<!-- 新增群組 -->
<div class="chatGroupAdd">
	<form id="JqPostForm">
		<input type="hidden" name="sSelectFriend" value="0">
		<input type="hidden" name="nId" value="<?php echo $nId;?>">
		<input type="hidden" name="sAct" value="<?php echo $aUrl['sAct'].'&sJWT='.$sJWT;?>">

		<table class="infData">
			<tbody>
				<tr>
					<td class="infDataCell1">
						<div class="infDataTit">
							<span><?php echo aGROUP['NAME0'];?></span>
						</div>
					</td>
					<td class="infDataCell2">
						<div class="Ipt">
							<input type="text" name="sName0" value="<?php echo $sName0;?>" placeholder="<?php echo aGROUP['PLEASEENTER'];?>">
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</form>

	<div class="chatGroupAddTit"><?php echo aGROUP['SELECTFRIEND'];?></div>
	<div class="chatGroupAddBox">
		<?php
		foreach ($aData as $LPnId => $LPaFriend)
		{
			?>
			<div class="chatGroupAddList JqListSelect">
				<table class="chatGroupAddTable">
					<tbody>
						<tr>
							<td class="chatGroupAddTdIcon">
								<div class="chatGroupAddIconChoose">
									<label for="select<?php echo $LPnId;?>">
										<input type="checkbox" id="select<?php echo $LPnId;?>" class="JqSelectFriend" data-id="<?php echo $LPnId;?>">
									</label>
								</div>
							</td>
							<td class="chatGroupAddTdPic JqChatGroupBtn">

								<!-- 若此人身份為雇主,selfieBox + boss -->
								<div class="selfieBox <?php echo $LPaFriend['sRole']; ?>">
									<img src="<?php echo $LPaFriend['sImgUrl']; ?>" alt="">
								</div>
							</td>
							<td class="chatGroupAddTdName JqChatGroupBtn">
								<div><?php echo $LPaFriend['sName0'];?></div>
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